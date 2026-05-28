<?php
// Detect protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

// Check if we're accessing a generated preview file
$request_uri = $_SERVER['REQUEST_URI'];
if (preg_match('/\/([a-f0-9]{6})\.html$/', $request_uri, $matches)) {
    $code = $matches[1];
    $preview_file = $code . '.html';
    
    if (file_exists($preview_file)) {
        // Serve the preview file directly
        header('Content-Type: text/html');
        readfile($preview_file);
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $long_url = $_POST['url'];
    $custom_image = $_POST['image'];
    $custom_desc = $_POST['description'];
    $custom_title = $_POST['title'];
    
    // Generate random 6-character code
    $code = substr(md5(uniqid()), 0, 6);
    
    // Build HTML preview page - automatic redirect only
    $html_content = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0; URL=\'' . $long_url . '\'" />
    <meta property="og:title" content="' . htmlspecialchars($custom_title) . '" />
    <meta property="og:description" content="' . htmlspecialchars($custom_desc) . '" />
    <meta property="og:image" content="' . htmlspecialchars($custom_image) . '" />
    <meta property="og:url" content="' . htmlspecialchars($long_url) . '" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="' . htmlspecialchars($custom_title) . '" />
    <meta name="twitter:description" content="' . htmlspecialchars($custom_desc) . '" />
    <meta name="twitter:image" content="' . htmlspecialchars($custom_image) . '" />
    <title>' . htmlspecialchars($custom_title) . '</title>
</head>
<body>
    <!-- Automatic redirect - user never sees this page -->
</body>
</html>';
    
    // Save HTML file in the same directory
    $filename = $code . '.html';
    file_put_contents($filename, $html_content);
    
    // Build short URL
    $short_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/" . $filename;
    $success = "<div class='success'>✅ Your preview link: <a href='$short_url' target='_blank'>$short_url</a><br>
                <small>📱 When shared on Facebook/Twitter, your custom image & description will appear</small></div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MetaMarketing - Custom Social Media Preview Tool - Auto Redirect</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; max-width: 650px; margin: 50px auto; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        input, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #e0e0e0; border-radius: 10px; box-sizing: border-box; font-size: 14px; transition: 0.3s; }
        input:focus, textarea:focus { border-color: #667eea; outline: none; }
        button { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 14px 20px; border: none; border-radius: 10px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; transition: 0.3s; }
        button:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .success { background: #d4edda; padding: 20px; border-radius: 12px; margin-top: 20px; text-align: center; border: 1px solid #28a745; }
        .success a { color: #155724; font-weight: bold; word-break: break-all; }
        h1 { color: #333; text-align: center; }
        label { font-weight: bold; margin-top: 15px; display: block; color: #555; }
        hr { margin: 20px 0; border: none; height: 1px; background: linear-gradient(to right, transparent, #ccc, transparent); }
        .info { background: #e7f3ff; padding: 15px; border-radius: 10px; margin-top: 20px; font-size: 14px; color: #004085; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Custom Social Media Preview</h1>
        <p style="text-align: center; color: #666;">Create links with custom image & description for Facebook, Twitter, WhatsApp</p>
        
        <form method="POST">
            <label>🔗 Original URL:</label>
            <input type="url" name="url" placeholder="https://example.com/page.html" required>
            
            <label>📝 Preview Title (appears in social media):</label>
            <input type="text" name="title" placeholder="e.g., Check out this amazing article" required>
            
            <label>🖼️ Preview Image URL:</label>
            <input type="url" name="image" placeholder="https://example.com/image.jpg" required>
            
            <label>📄 Preview Description:</label>
            <textarea name="description" rows="3" placeholder="Write an engaging description that will appear on social media..." required></textarea>
            
            <button type="submit">✨ Generate Custom Link</button>
        </form>
        
        <?php echo $success ?? ''; ?>
        
        <div class="info">
            <strong>ℹ️ How it works:</strong><br>
            • Each link creates an HTML file like <code>abc123.html</code> in the same folder<br>
            • The file contains only meta tags + instant redirect<br>
            • Users are redirected instantly - they never see the preview page<br>
            • Facebook/Twitter read the meta tags to show your custom image & description<br>
            • <strong>No extra folders, no JSON files, just one PHP file + HTML redirect files</strong>
        </div>
    </div>
</body>
</html>



















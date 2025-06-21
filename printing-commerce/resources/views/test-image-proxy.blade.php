<!DOCTYPE html>
<html>
<head>
    <title>Image Proxy Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .image-container { margin: 20px 0; padding: 10px; border: 1px solid #ddd; }
        img { max-width: 100%; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Image Proxy Test</h1>
        
        <h2>File Information</h2>
        <pre>
Filename: {{ $filename }}
Path: {{ $path }}
File exists: {{ $file_exists ? 'Yes' : 'No' }}
        </pre>
        
        <h2>Image via Proxy</h2>
        <div class="image-container">
            <p>URL: {{ $proxy_url }}</p>
            <img src="{{ $proxy_url }}" alt="Image via proxy">
        </div>
        
        <h2>Image Direct (may have CORS issues)</h2>
        <div class="image-container">
            <p>URL: {{ $direct_url }}</p>
            <img src="{{ $direct_url }}" alt="Direct image">
        </div>
        
        <h2>Test Requests</h2>
        <ul>
            <li><a href="{{ url('/debug/storage/' . $filename) }}" target="_blank">Debug Storage Info</a></li>
            <li><a href="{{ $proxy_url }}" target="_blank">Direct Proxy Access</a></li>
        </ul>
    </div>
    
    <script>
        // Add error handling for images
        document.querySelectorAll('img').forEach(img => {
            img.onerror = function() {
                this.style.display = 'none';
                const errorMsg = document.createElement('div');
                errorMsg.innerHTML = '<p style="color: red;">Error loading image</p>';
                this.parentNode.appendChild(errorMsg);
            };
        });
    </script>
</body>
</html> 
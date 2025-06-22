<?php
// public/image-proxy.php

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get parameters
$type = $_GET['type'] ?? '';
$file = $_GET['file'] ?? '';
$uuid = $_GET['uuid'] ?? '';
$folder = $_GET['folder'] ?? 'catatan_pesanan'; // Default folder
$externalUrl = $_GET['url'] ?? ''; // New parameter for external URLs

// Debug logging
error_log("Image Proxy Request - Type: $type, File: $file, UUID: $uuid, Folder: $folder, External URL: $externalUrl");

// Handle external URLs (like Google profile photos)
if ($type === 'external' && !empty($externalUrl)) {
    $decodedUrl = urldecode($externalUrl);
    error_log("Proxying external URL: $decodedUrl");
    
    // Validate URL format
    if (filter_var($decodedUrl, FILTER_VALIDATE_URL)) {
        // Get image from external URL
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP Image Proxy',
                    'Accept: image/jpeg, image/png, image/webp, */*'
                ]
            ]
        ]);
        
        $imageData = @file_get_contents($decodedUrl, false, $context);
        
        if ($imageData !== false) {
            // Get content type from headers
            $contentType = '';
            foreach ($http_response_header as $header) {
                if (strpos(strtolower($header), 'content-type:') === 0) {
                    $contentType = trim(substr($header, 13));
                    break;
                }
            }
            
            // If couldn't determine content type, guess from URL
            if (empty($contentType)) {
                $pathInfo = pathinfo($decodedUrl);
                $extension = strtolower($pathInfo['extension'] ?? '');
                
                switch ($extension) {
                    case 'jpg':
                    case 'jpeg':
                        $contentType = 'image/jpeg';
                        break;
                    case 'png':
                        $contentType = 'image/png';
                        break;
                    case 'gif':
                        $contentType = 'image/gif';
                        break;
                    case 'webp':
                        $contentType = 'image/webp';
                        break;
                    default:
                        $contentType = 'image/jpeg'; // Assume JPEG if can't determine
                }
            }
            
            // Output the external image
            header('Content-Type: ' . $contentType);
            header('Content-Length: ' . strlen($imageData));
            header('Cache-Control: public, max-age=86400'); // Cache for 24 hours
            echo $imageData;
            exit;
        } else {
            http_response_code(404);
            exit('Could not retrieve external image');
        }
    } else {
        http_response_code(400);
        exit('Invalid external URL');
    }
}

if (empty($file)) {
    http_response_code(404);
    exit('File parameter is required');
}

// Determine file path based on type
switch ($type) {
    case 'chat':
        $filePath = __DIR__ . '/../storage/app/public/chat_files/' . $file;
        break;
    case 'user':
        // Check if it's an external URL (Google photo)
        if (filter_var($file, FILTER_VALIDATE_URL)) {
            // Redirect to the external URL proxy endpoint
            $redirectUrl = $_SERVER['SCRIPT_NAME'] . '?type=external&url=' . urlencode($file);
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        $filePath = __DIR__ . '/../assets3/img/user/' . $file;
        break;
    case 'pesanan':
        if (empty($uuid)) {
            http_response_code(404);
            exit('UUID required for pesanan type');
        }
        $filePath = __DIR__ . '/../assets3/img/pesanan/' . $uuid . '/' . $folder . '/' . $file;
        break;
    default:
        http_response_code(404);
        exit('Invalid type: ' . $type);
}

// Debug: Log the full path
error_log("Looking for file at: $filePath");

// Check if file exists
if (!file_exists($filePath)) {
    $alternativePaths = [];
    
    // Try alternative paths for user images
    if ($type === 'user') {
        $alternativePaths = [
            __DIR__ . '/../storage/app/public/user/' . $file,
            __DIR__ . '/../public/assets3/img/user/' . $file,
            __DIR__ . '/../assets/img/user/' . $file,
            __DIR__ . '/../public_html/assets3/img/user/' . $file,
        ];
    } 
    // Try alternative paths for pesanan images
    else if ($type === 'pesanan' && !empty($uuid)) {
        $alternativePaths = [
            // Coba di public_html dengan folder yang ditentukan
            __DIR__ . '/../public_html/assets3/img/pesanan/' . $uuid . '/' . $folder . '/' . $file,
            // Coba di public dengan folder yang ditentukan
            __DIR__ . '/assets3/img/pesanan/' . $uuid . '/' . $folder . '/' . $file,
            // Coba di storage dengan folder yang ditentukan
            __DIR__ . '/../storage/app/public/pesanan/' . $uuid . '/' . $folder . '/' . $file,
            
            // Coba folder alternatif jika folder yang ditentukan adalah hasil_desain
            __DIR__ . '/../assets3/img/pesanan/' . $uuid . '/catatan_pesanan/' . $file,
            __DIR__ . '/../public_html/assets3/img/pesanan/' . $uuid . '/catatan_pesanan/' . $file,
            __DIR__ . '/assets3/img/pesanan/' . $uuid . '/catatan_pesanan/' . $file,
            
            // Coba tanpa subfolder
            __DIR__ . '/../assets3/img/pesanan/' . $uuid . '/' . $file,
            __DIR__ . '/../public_html/assets3/img/pesanan/' . $uuid . '/' . $file,
            __DIR__ . '/assets3/img/pesanan/' . $uuid . '/' . $file,
            __DIR__ . '/../storage/app/public/pesanan/' . $uuid . '/' . $file,
        ];
    }
    
    foreach ($alternativePaths as $altPath) {
        error_log("Trying alternative path: $altPath");
        if (file_exists($altPath)) {
            $filePath = $altPath;
            error_log("Found file at alternative path: $altPath");
            break;
        }
    }
    
    // If still not found, return 404
    if (!file_exists($filePath)) {
        http_response_code(404);
        error_log("File not found at any location. Last tried: $filePath");
        exit('File not found at any location. Last tried: ' . $filePath);
    }
}

// Determine MIME type
$mimeType = mime_content_type($filePath);
if (!$mimeType) {
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    switch (strtolower($extension)) {
        case 'jpg':
        case 'jpeg':
            $mimeType = 'image/jpeg';
            break;
        case 'png':
            $mimeType = 'image/png';
            break;
        case 'gif':
            $mimeType = 'image/gif';
            break;
        case 'webp':
            $mimeType = 'image/webp';
            break;
        default:
            $mimeType = 'application/octet-stream';
    }
}

// Set response headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=31536000');

// Output the file
readfile($filePath);
exit;
?> 
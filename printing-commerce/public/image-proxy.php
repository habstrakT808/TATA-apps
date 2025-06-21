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

// Debug logging
error_log("Image Proxy Request - Type: $type, File: $file, UUID: $uuid");

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
        $filePath = __DIR__ . '/../assets3/img/user/' . $file;
        break;
    case 'pesanan':
        if (empty($uuid)) {
            http_response_code(404);
            exit('UUID required for pesanan type');
        }
        $filePath = __DIR__ . '/../assets3/img/pesanan/' . $uuid . '/catatan_pesanan/' . $file;
        break;
    default:
        http_response_code(404);
        exit('Invalid type: ' . $type);
}

// Debug: Log the full path
error_log("Looking for file at: $filePath");

// Check if file exists
if (!file_exists($filePath)) {
    // Try alternative paths for user images
    if ($type === 'user') {
        $alternativePaths = [
            __DIR__ . '/../storage/app/public/user/' . $file,
            __DIR__ . '/../public/assets3/img/user/' . $file,
            __DIR__ . '/../assets/img/user/' . $file,
        ];
        
        foreach ($alternativePaths as $altPath) {
            error_log("Trying alternative path: $altPath");
            if (file_exists($altPath)) {
                $filePath = $altPath;
                break;
            }
        }
    }
    
    // If still not found, return 404
    if (!file_exists($filePath)) {
        http_response_code(404);
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
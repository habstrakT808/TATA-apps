<?php
// Quick fix script to redirect image URLs to the proxy

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the URL from the request
$url = $_SERVER['REQUEST_URI'] ?? '';

// Check if this is a storage request
if (strpos($url, '/storage/chat_files/') !== false) {
    // Extract the filename
    $parts = explode('/storage/chat_files/', $url);
    if (isset($parts[1])) {
        $filename = $parts[1];
        
        // Redirect to the image proxy
        header('Location: /image-proxy.php?type=chat&file=' . urlencode($filename));
        exit;
    }
}

// If we get here, it's not a request we can handle
http_response_code(400);
echo json_encode([
    'error' => 'Invalid request',
    'url' => $url
]);
?> 
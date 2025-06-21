<?php
// Debug script to help diagnose image URL issues in Flutter

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, Application');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the URL from the request
$url = $_GET['url'] ?? '';
$action = $_GET['action'] ?? 'debug';

// Function to extract filename from URL
function extractFilename($url) {
    $parts = explode('/', $url);
    return end($parts);
}

// Function to check if file exists
function checkFileExists($filename) {
    $paths = [
        __DIR__ . '/../storage/app/public/chat_files/' . $filename,
        __DIR__ . '/storage/chat_files/' . $filename
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            return [
                'exists' => true,
                'path' => $path
            ];
        }
    }
    
    return [
        'exists' => false,
        'paths_checked' => $paths
    ];
}

// Function to generate proxy URL
function generateProxyUrl($filename) {
    return 'http://localhost:8000/image-proxy.php?type=chat&file=' . urlencode($filename);
}

// Process based on action
$result = [
    'original_url' => $url,
    'timestamp' => date('Y-m-d H:i:s')
];

if (!empty($url)) {
    $filename = extractFilename($url);
    $result['extracted_filename'] = $filename;
    $result['file_check'] = checkFileExists($filename);
    $result['proxy_url'] = generateProxyUrl($filename);
    
    if ($action === 'redirect' && !empty($filename)) {
        header('Location: ' . generateProxyUrl($filename));
        exit;
    }
}

// Return JSON response
echo json_encode($result, JSON_PRETTY_PRINT);
?> 
<?php
// A simple test script to verify the image proxy

// Get the file parameter
$file = $_GET['file'] ?? '4fdf524d-cdc7-4695-971f-4fc5f3fe3cd2.png';

// Construct the proxy URL
$proxyUrl = 'http://localhost:8000/image-proxy.php?type=chat&file=' . urlencode($file);

echo "<h1>Image Proxy Test</h1>";
echo "<p>Testing proxy URL: $proxyUrl</p>";

// Check if the file exists directly
$filePath = __DIR__ . '/../storage/app/public/chat_files/' . $file;
echo "<p>Checking if file exists at: $filePath</p>";
echo "<p>File exists: " . (file_exists($filePath) ? 'Yes' : 'No') . "</p>";

// Display the image using the proxy
echo "<h2>Image via Proxy:</h2>";
echo "<img src='$proxyUrl' style='max-width: 500px;' />";

// Display the image directly (for comparison)
echo "<h2>Image Direct (may have CORS issues):</h2>";
echo "<img src='/storage/chat_files/$file' style='max-width: 500px;' />";

// Display debug info
echo "<h2>Debug Info:</h2>";
echo "<pre>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Storage Path: " . realpath(__DIR__ . '/../storage/app/public/chat_files') . "\n";
echo "Public Path: " . realpath(__DIR__) . "\n";
echo "</pre>";
?> 
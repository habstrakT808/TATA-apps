<?php
// Mengizinkan akses dari semua origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, Application");

// Jika ini adalah request OPTIONS, selesaikan di sini
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Untuk request selain OPTIONS, lanjutkan ke file yang diminta
$uri = $_SERVER['REQUEST_URI'];
$file = __DIR__ . $uri;

// Jika file ada dan bisa dibaca, tampilkan
if (file_exists($file) && !is_dir($file)) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    // Set Content-Type berdasarkan ekstensi file
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            header('Content-Type: image/jpeg');
            break;
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
        case 'svg':
            header('Content-Type: image/svg+xml');
            break;
        case 'css':
            header('Content-Type: text/css');
            break;
        case 'js':
            header('Content-Type: application/javascript');
            break;
        default:
            header('Content-Type: application/octet-stream');
    }
    
    // Output file
    readfile($file);
    exit;
}

// Jika file tidak ditemukan, kembalikan 404
header('HTTP/1.0 404 Not Found');
echo '404 Not Found'; 
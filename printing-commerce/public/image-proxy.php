<?php
// Mengizinkan akses dari semua origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, Application");

// Jika ini adalah request OPTIONS, selesaikan di sini
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Ambil parameter file
$file = isset($_GET['file']) ? $_GET['file'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : 'user';
$uuid = isset($_GET['uuid']) ? $_GET['uuid'] : null;

// Validasi parameter file untuk keamanan
if (!$file || preg_match('/[^a-zA-Z0-9_\-\.]/', $file)) {
    header('HTTP/1.0 400 Bad Request');
    echo 'Invalid file parameter';
    exit;
}

// Tentukan path file berdasarkan tipe
switch ($type) {
    case 'user':
        $path = __DIR__ . '/assets3/img/user/' . $file;
        break;
    case 'pesanan':
        // Memastikan UUID valid dan aman
        if (!$uuid || preg_match('/[^a-zA-Z0-9_\-]/', $uuid)) {
            header('HTTP/1.0 400 Bad Request');
            echo 'Invalid UUID parameter';
            exit;
        }
        $path = __DIR__ . '/assets3/img/pesanan/' . $uuid . '/catatan_pesanan/' . $file;
        break;
    default:
        $path = __DIR__ . '/assets3/img/user/' . $file;
}

// Debug info - simpan ke log
error_log("Image Proxy Request: $type, File: $file, Path: $path");

// Periksa apakah file ada
if (!file_exists($path)) {
    error_log("File not found: $path");
    // Jika file tidak ditemukan, kembalikan gambar placeholder
    header('Content-Type: image/png');
    $placeholder_path = __DIR__ . '/assets3/img/placeholder.png';
    
    if (file_exists($placeholder_path)) {
        readfile($placeholder_path);
    } else {
        // Jika placeholder tidak ada, kembalikan 404
        header('HTTP/1.0 404 Not Found');
        echo 'File not found';
    }
    exit;
}

// Set header Content-Type berdasarkan ekstensi file
$ext = pathinfo($path, PATHINFO_EXTENSION);
switch (strtolower($ext)) {
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
    default:
        header('Content-Type: application/octet-stream');
}

// Output file
readfile($path); 
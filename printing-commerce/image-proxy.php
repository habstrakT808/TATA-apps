<?php
// image-proxy.php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$type = $_GET['type'] ?? '';
$file = $_GET['file'] ?? '';
$url = $_GET['url'] ?? '';
$folder = $_GET['folder'] ?? '';
$uuid = $_GET['uuid'] ?? '';

// Debug log untuk membantu troubleshooting
error_log("Image proxy request: type=$type, file=$file, url=" . substr($url, 0, 30) . "...");

try {
    if ($type === 'external' && !empty($url)) {
        // Handle external URLs (like Google profile images)
        $decodedUrl = urldecode($url);
        
        // Validate URL
        if (!filter_var($decodedUrl, FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid URL']);
            exit;
        }
        
        // Set timeout and context
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0 (compatible; ImageProxy/1.0)',
                'follow_location' => true,
                'max_redirects' => 3
            ]
        ]);
        
        // Log bahwa kita mencoba fetch image dari URL tertentu
        error_log("Fetching external image from: $decodedUrl");
        
        $imageData = @file_get_contents($decodedUrl, false, $context);
        
        if ($imageData === false) {
            // Return default image if failed
            error_log("Failed to fetch external image, using default");
            $defaultPath = __DIR__ . '/public/assets/images/logotext.png';
            if (file_exists($defaultPath)) {
                $imageData = file_get_contents($defaultPath);
                header('Content-Type: image/png');
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Image not found']);
                exit;
            }
        } else {
            // Detect content type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageData);
            header('Content-Type: ' . $mimeType);
            error_log("External image fetched successfully, type: $mimeType");
        }
        
        header('Cache-Control: public, max-age=3600');
        echo $imageData;
        
    } elseif ($type === 'user' && !empty($file)) {
        // Handle user profile images
        $file = basename($file); // Sanitize filename
        $filePath = __DIR__ . '/public/assets3/img/user/' . $file;
        
        error_log("Looking for user image at: $filePath");
        
        if (file_exists($filePath)) {
            $mimeType = mime_content_type($filePath);
            header('Content-Type: ' . $mimeType);
            header('Cache-Control: public, max-age=3600');
            readfile($filePath);
            error_log("Found and served user image");
        } else {
            // Fallback ke default image
            error_log("User image not found, using default");
            $defaultPath = __DIR__ . '/public/assets/images/logotext.png';
            if (file_exists($defaultPath)) {
                header('Content-Type: image/png');
                readfile($defaultPath);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'File not found']);
            }
        }
    } elseif ($type === 'inspirasi_desain' && !empty($file)) {
        // Handle inspirasi desain images
        $file = basename($file); // Sanitize filename
        
        // Perbaiki path untuk sesuai dengan struktur server
        // Coba berbagai kemungkinan path untuk menemukan file
        $possiblePaths = [
            __DIR__ . '/tataapps/assets/images/inspirasi_desain/' . $file,
            __DIR__ . '/../tataapps/assets/images/inspirasi_desain/' . $file,
            dirname(__DIR__) . '/tataapps/assets/images/inspirasi_desain/' . $file,
            'C:/laragon/www/Project Tata/tataapps/assets/images/inspirasi_desain/' . $file,
        ];
        
        // Tambahkan path absolut dan relatif
        $root = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if (!empty($root)) {
            $possiblePaths[] = $root . '/../tataapps/assets/images/inspirasi_desain/' . $file;
            $possiblePaths[] = $root . '/tataapps/assets/images/inspirasi_desain/' . $file;
            $possiblePaths[] = dirname($root) . '/tataapps/assets/images/inspirasi_desain/' . $file;
        }
        
        // Coba copy file dari direktori asli ke direktori publik jika tidak ditemukan
        $publicPath = __DIR__ . '/public/assets/images/inspirasi_desain/';
        if (!is_dir($publicPath)) {
            @mkdir($publicPath, 0755, true);
        }
        
        $filePath = null;
        foreach ($possiblePaths as $path) {
            error_log("Checking path: $path");
            if (file_exists($path)) {
                $filePath = $path;
                break;
            }
        }
        
        if ($filePath) {
            // Copy file ke public directory agar lebih mudah diakses di kemudian hari
            $publicFilePath = $publicPath . $file;
            if (!file_exists($publicFilePath)) {
                @copy($filePath, $publicFilePath);
            }
            
            $mimeType = mime_content_type($filePath);
            header('Content-Type: ' . $mimeType);
            header('Cache-Control: public, max-age=3600');
            readfile($filePath);
            error_log("Found and served inspirasi desain image from: $filePath");
        } else {
            // Fallback to asset directory if not found
            $fallbackPath = __DIR__ . '/public/assets/images/logotext.png';
            if (file_exists($fallbackPath)) {
                header('Content-Type: image/png');
                readfile($fallbackPath);
                error_log("Used fallback image for inspirasi_desain");
            } else {
                http_response_code(404);
                error_log("Inspirasi desain image not found and fallback not available");
                echo json_encode(['error' => 'Inspirasi desain image not found and no fallback available']);
            }
        }
    } elseif ($type === 'pesanan' && !empty($file) && !empty($uuid) && !empty($folder)) {
        // Handle pesanan images
        $file = basename($file); // Sanitize filename
        $uuid = basename($uuid); // Sanitize UUID
        $folder = basename($folder); // Sanitize folder
        
        $filePath = __DIR__ . "/public/assets3/img/pesanan/$uuid/$folder/$file";
        
        error_log("Looking for pesanan image at: $filePath");
        
        if (file_exists($filePath)) {
            $mimeType = mime_content_type($filePath);
            header('Content-Type: ' . $mimeType);
            header('Cache-Control: public, max-age=3600');
            readfile($filePath);
            error_log("Found and served pesanan image");
        } else {
            http_response_code(404);
            error_log("Pesanan image not found: $filePath");
            echo json_encode(['error' => 'Pesanan image not found']);
        }
    } else {
        error_log("Invalid parameters for image proxy");
        http_response_code(400);
        echo json_encode(['error' => 'Invalid parameters']);
    }
    
} catch (Exception $e) {
    error_log("Error in image proxy: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?> 
<?php
// Test script to check if chat routes are properly registered

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Get all registered routes
$routes = Route::getRoutes();
$chatRoutes = [];

// Filter for chat routes
foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'chat') !== false) {
        $chatRoutes[] = [
            'method' => implode('|', $route->methods()),
            'uri' => $uri,
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    }
}

// Output results
echo "Found " . count($chatRoutes) . " chat routes:\n\n";
foreach ($chatRoutes as $route) {
    echo $route['method'] . " " . $route['uri'] . " => " . $route['action'] . "\n";
}

echo "\n\nTesting specific routes:\n";
echo "POST mobile/chat/create-direct => " . (in_array('mobile/chat/create-direct', array_column($chatRoutes, 'uri')) ? "FOUND" : "NOT FOUND") . "\n";
echo "POST chat/create-direct => " . (in_array('chat/create-direct', array_column($chatRoutes, 'uri')) ? "FOUND" : "NOT FOUND") . "\n";

// Done
echo "\nTest completed.\n"; 
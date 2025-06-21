<?php
// check_routes.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = \Illuminate\Support\Facades\Route::getRoutes();

echo "Checking routes for 'pesanan/create-with-transaction':\n";
echo "================================================\n\n";

$found = false;
foreach ($routes as $route) {
    if (strpos($route->uri(), 'pesanan/create-with-transaction') !== false) {
        $found = true;
        echo "Route found: " . $route->uri() . " [" . implode('|', $route->methods()) . "]\n";
        echo "Controller: " . $route->getActionName() . "\n";
        echo "Middleware: " . implode(', ', $route->middleware()) . "\n\n";
    }
}

if (!$found) {
    echo "No route found with 'pesanan/create-with-transaction' in the URI.\n";
} 
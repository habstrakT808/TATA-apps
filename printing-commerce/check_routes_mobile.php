<?php
// check_routes_mobile.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = \Illuminate\Support\Facades\Route::getRoutes();

echo "Routes with 'mobile' in URI:\n";
echo "==========================\n\n";
echo "METHOD | URI | NAME | ACTION | MIDDLEWARE\n";
echo "---------------------------------------\n";

foreach ($routes as $route) {
    if (strpos($route->uri(), 'mobile') !== false) {
        echo implode('|', $route->methods()) . " | ";
        echo $route->uri() . " | ";
        echo ($route->getName() ?: '-') . " | ";
        echo $route->getActionName() . " | ";
        echo implode(', ', $route->middleware()) . "\n";
    }
} 
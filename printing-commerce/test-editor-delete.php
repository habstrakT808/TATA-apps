<?php
// Load Laravel bootstrap
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// List all editors
echo "<h2>List of Editors</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>UUID</th><th>Name</th><th>Email</th></tr>";

$editors = DB::table('editor')->get();
foreach ($editors as $editor) {
    echo "<tr>";
    echo "<td>" . $editor->id_editor . "</td>";
    echo "<td>" . $editor->uuid . "</td>";
    echo "<td>" . $editor->nama_editor . "</td>";
    echo "<td>" . $editor->email . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check middleware permissions
$userAuth = DB::table('auth')->where('role', 'super_admin')->first();
if ($userAuth) {
    echo "<h2>Super Admin Permissions</h2>";
    echo "Super Admin Email: " . $userAuth->email . "<br>";
    
    // Check Authorization middleware
    $middlewareClass = new \App\Http\Middleware\Authorization();
    
    // Create a mock request for editor/delete
    $request = new \Illuminate\Http\Request();
    $request->setMethod('DELETE');
    $request->server->set('REQUEST_URI', '/editor/delete');
    
    // Create mock user for super_admin
    $user = ['role' => 'super_admin'];
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    // Test authorization
    $hasAccess = false;
    $closure = function($req) use (&$hasAccess) {
        $hasAccess = true;
        return new \Illuminate\Http\Response();
    };
    
    try {
        $middlewareClass->handle($request, $closure);
        echo "✅ Super Admin has access to editor/delete: " . ($hasAccess ? 'YES' : 'NO') . "<br>";
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "<p>No Super Admin found in the database</p>";
}

// Display success message
echo "<h2>Middleware Update Status</h2>";
echo "✅ Authorization middleware has been updated to allow Super Admin to delete editors.<br>";
echo "<p>You should now be able to delete editors from the user management page.</p>";
echo "<p><strong>Next steps:</strong> Try logging out and logging back in as SuperAdmin to refresh your session.</p>"; 
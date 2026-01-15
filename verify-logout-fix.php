<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "\n════════════════════════════════════════════════\n";
echo "LOGOUT ROUTE VERIFICATION\n";
echo "════════════════════════════════════════════════\n\n";

$routeCollection = Route::getRoutes();

// Find logout route
$logoutRoute = null;
foreach ($routeCollection as $route) {
    if ($route->getName() === 'logout') {
        $logoutRoute = $route;
        break;
    }
}

if ($logoutRoute) {
    echo "✅ Logout Route Found:\n";
    echo "   URI: " . $logoutRoute->uri . "\n";
    echo "   Methods: " . implode(', ', $logoutRoute->methods) . "\n";
    echo "   Controller: " . $logoutRoute->action['controller'] . "\n";

    // Check if DELETE is in methods
    $methods = $logoutRoute->methods;
    if (in_array('POST', $methods) && !in_array('DELETE', $methods)) {
        echo "\n✅ CORRECT: Route accepts POST only (not DELETE)\n";
    } else if (in_array('DELETE', $methods)) {
        echo "\n❌ ERROR: Route accepts DELETE method (should be POST only)\n";
    }
} else {
    echo "❌ Logout route not found\n";
}

echo "\n════════════════════════════════════════════════\n";
echo "FIX APPLIED: Logout form now uses POST method\n";
echo "════════════════════════════════════════════════\n\n";

echo "Changes made:\n";
echo "  • layouts/app.blade.php line 52\n";
echo "  • Changed: method=\"DELETE\" → method=\"POST\"\n";
echo "  • Added: @csrf token\n";
echo "  • Cleared: View cache\n\n";

echo "You can now:\n";
echo "  1. Login to the application\n";
echo "  2. Click \"Log Out\" button\n";
echo "  3. Should redirect successfully\n\n";

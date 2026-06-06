<?php

// Migration bootstrap endpoint - call this after deployment
// GET https://extend-system.vercel.app/api/migrate

require __DIR__ . '/../bootstrap/app.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Console\Application;

try {
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    
    // Run migrations with force flag (for production)
    $exitCode = $kernel->call('migrate', [
        '--force' => true,
        '--no-interaction' => true,
    ]);
    
    if ($exitCode === 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Database migrations completed successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Migrations failed with exit code: ' . $exitCode
        ]);
    }
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Migration error: ' . $e->getMessage()
    ]);
}

<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| This project is a decoupled SPA (React) + API (Laravel) architecture.
| Almost everything lives in routes/api.php. This file only needs to:
| 1. Serve a health-check root
| 2. Handle Sanctum's CSRF cookie route (auto-registered by Sanctum)
| 3. Provide a fallback redirect for the Flutterwave hosted payment page
|    (only needed if you use Flutterwave's redirect flow instead of inline)
*/

Route::get('/', function () {
    return response()->json([
        'app' => 'Motorcycle Contract & Sales Management API',
        'status' => 'running',
        'frontend' => config('app.frontend_url'),
    ]);
});

// Optional: Flutterwave redirect-based callback (only if NOT using inline checkout)
Route::get('/payment/callback', function () {
    return redirect(config('app.frontend_url') . '/payments?status=' . request('status'));
});
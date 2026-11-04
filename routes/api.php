<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);

    // Temporary route to prove permissions are enforced on the server
    Route::get('/admin/users', fn () => response()->json(['message' => 'Users endpoint (coming soon)']))
        ->middleware('can:users.manage');
});

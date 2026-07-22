<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Web\ProjectController;
use App\Http\Controllers\Web\DeviceController;

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');

Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');

Route::get('/test-firebase', function () {
    try {
        $messaging = app('firebase.messaging');

        return "✅ Firebase connected successfully!";
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});
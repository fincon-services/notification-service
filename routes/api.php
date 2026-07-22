<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\NotificationController;

Route::post('/register-device', [DeviceController::class, 'register']);
Route::get('/device-token', [DeviceController::class, 'getToken']);
Route::post('/send-notification', [NotificationController::class, 'send']);
Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/mark-read/{id}', [NotificationController::class, 'markAsRead']);

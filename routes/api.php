<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\APIAuthMiddleware;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;


Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::middleware([APIAuthMiddleware::class])->group(function () {
    Route::post('conversations', [ConversationController::class, 'start']);
    Route::get('conversations', [ConversationController::class, 'index']);
    Route::post('conversations/{id}/messages', [MessageController::class, 'send']);
    Route::get('conversations/{id}/messages', [MessageController::class, 'index']);
});


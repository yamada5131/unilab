<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/agent/command_result', [\App\Http\Controllers\AgentCommandController::class, 'updateStatus'])
    ->name('agent.command.status');

<?php

use App\Http\Controllers\AgentCommandController;
use App\Http\Controllers\AgentMetricsController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\ComputerController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::patch('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    Route::post('/computers/{id}/command', [ComputerController::class, 'sendCommand'])->name('computers.command');

    /*
    |------------------------------------------
    | Routes dành cho Agent
    |------------------------------------------
    |
    | Các endpoint Agent gọi: gửi metrics, lấy lệnh,
    | gửi kết quả lệnh...
    |
    */
    // Route::middleware('verify.agent.token')->group(function() {

    // Agent gửi metrics (CPU, RAM, Disk, Process)
    Route::post('/agent/metrics', [AgentMetricsController::class, 'store'])->name('agent.metrics.store');

    // Agent lấy danh sách lệnh pending
    Route::get('/agent/commands', [AgentCommandController::class, 'index'])->name('agent.commands.index');

    // Agent gửi kết quả lệnh
    Route::post('/agent/command_result', [AgentCommandController::class, 'updateStatus'])->name('agent.commands.updateStatus');

    // });
    /*
    |------------------------------------------
    | Routes dành cho Admin / Dashboard
    |------------------------------------------
    */

    // Tạo lệnh (VD: shutdown, install, v.v.)
    Route::post('/commands', [CommandController::class, 'store'])->name('commands.store');

    // Xem danh sách máy, v.v.
    Route::get('/machines', [MachineController::class, 'index'])->name('machines.index');

    // Tuỳ chỉnh thêm route detail:
    // Route::get('/machines/{id}', [MachineController::class, 'show']);

});

require __DIR__.'/auth.php';

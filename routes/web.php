<?php

declare(strict_types=1);

use App\Http\Controllers\CommandController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    // User profile management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Room management
    Route::resource('rooms', RoomController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    // Computer management
    Route::resource('computers', MachineController::class)
        ->only(['store', 'update', 'destroy']);
    Route::post('/computers/{id}/command', [MachineController::class, 'sendCommand'])
        ->name('computers.command');

    // Command management
    Route::controller(CommandController::class)->group(function () {
        // Send command to a specific computer
        Route::post('/commands/computer', 'dispatchToComputer')
            ->name('commands.dispatch.computer');

        // Send command to all computers in a room
        Route::post('/commands/room', 'dispatchToRoom')
            ->name('commands.dispatch.room');

        // Get command status
        Route::get('/commands/{id}', 'show')
            ->name('commands.show');

        // Update command status (endpoint for agents)
        Route::post('/commands/{id}/status', 'updateStatus')
            ->name('commands.update.status');
    });
});

// Authentication routes
require __DIR__.'/auth.php';

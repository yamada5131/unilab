<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        // Fake user data
        $users = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'exemple1@gmail.com',
                'role' => 'Admin',
            ],
        ];

        return Inertia::render('User/Index', []);
    }
}

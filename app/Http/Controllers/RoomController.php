<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request as HttpRequest;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index()
    {
        return Inertia::render('Room/Index', [
            'rooms' => RoomResource::collection(Room::all()),
        ]);
    }

    public function store(HttpRequest $request)
    {
        Room::create($request->validate([
            'name' => 'required|string|max:50',
            'grid_rows' => 'required|integer',
            'grid_cols' => 'required|integer',
        ]));

        return to_route('rooms.index');
    }

    public function show(string $id): Response
    {
        return Inertia::render('Room/Show',
            [
                'room' => RoomResource::make(
                    Room::findOrFail($id)->load('machines')
                ),
            ]
        );
    }

    public function update(HttpRequest $request, string $id)
    {
        Room::findOrFail($id)->update((
            $request->validate([
                'name' => 'required|string|max:50',
                'grid_rows' => 'required|integer',
                'grid_cols' => 'required|integer',
            ])
        ));

        return to_route('rooms.index');
    }

    public function destroy(string $id)
    {
        Room::findOrFail($id)->delete();

        return to_route('rooms.index');
    }
}

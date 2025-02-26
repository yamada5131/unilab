<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request as HttpRequest;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index()
    {
        return Inertia::render('Room/Index', [
            'rooms' => Room::all()->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'grid_rows' => $room->grid_rows,
                    'grid_cols' => $room->grid_cols,
                ];
            }),
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

    public function update(HttpRequest $request, $id)
    {
        $room = Room::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
        ]);

        $room->update($validated);

        return to_route('rooms.index');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return to_route('rooms.index');
    }

    public function show(string $id): Response
    {
        return Inertia::render('Room/Show');
    }
}

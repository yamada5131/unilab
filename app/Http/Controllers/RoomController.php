<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function index(): \Inertia\Response
    {
        $rooms = Room::all();

        return Inertia::render('Room/Index', [
            'rooms' => $rooms,
        ]);
    }

    public function getRooms()
    {
        $rooms = Room::all(); // Fetch all rooms

        return response()->json($rooms);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
        ]);

        Room::create($validated);

        return to_route('rooms.index');
    }

    public function update(Request $request, $id)
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

    public function show($id)
    {
        $room = Room::with('computers')->findOrFail($id);
        // Kiểm tra thuộc tính hardware_specifications của các computers
        // foreach ($room->computers as $computer) {
        //     dd(gettype($computer->hardware_specifications)); // Phải là mảng
        // }
        //
        // $computer = Computer::all()->first(); // Phải dùng Eloquent
        // dd(gettype($computer->hardware_specifications)); // Phải trả về "array"

        return Inertia::render('Room/Show', [
            'room' => $room,
        ]);
    }
}

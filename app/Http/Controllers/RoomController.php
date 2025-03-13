<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
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

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Room::create($validated);

        return redirect()->back();
    }

    public function show(string $id): Response
    {
        $room = Room::findOrFail($id)->load('machines');

        return Inertia::render('Room/Show',
            [
                'room' => RoomResource::make($room),
            ]
        );
    }

    public function update(UpdateRoomRequest $request, string $id): RedirectResponse
    {
        $validated = $request->validated();

        Room::findOrFail($id)->update($validated);

        return redirect()->back();
    }

    public function destroy(string $id): RedirectResponse
    {
        Room::findOrFail($id)->delete();

        return to_route('rooms.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomCollection;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function index()
    {
        // Render the Room/Index page using Inertia
        return Inertia::render('Room/Index', [
            // 'rooms' contains a paginated list of rooms filtered by search input if provided
            'rooms' => new RoomCollection(Room::query()
                // Check if a search query is present in the request, and if so, add a "where" filter on the "name" field
                ->when(FacadesRequest::input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                // Paginate the results, showing 5 rooms per page
                ->paginate(10)
                // Append the current query string parameters to the pagination links
                ->withQueryString()),

            // 'filters' holds the current search filters to maintain state in the UI
            'filters' => FacadesRequest::only(['search']),
        ]);
    }

    public function getRooms()
    {
        $rooms = Room::all(); // Fetch all rooms

        return response()->json($rooms);
    }

    public function store(HttpRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
        ]);

        Room::create($validated);

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

    public function show(Room $room): \Inertia\Response
    {
        $room->load('computers');

        return Inertia::render('Room/Show', [
            'room' => RoomResource::make($room),
        ]);
    }
}

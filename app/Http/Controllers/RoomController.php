<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateRoomAction;
use App\Actions\DeleteRoomAction;
use App\Actions\UpdateRoomAction;
use App\Http\Requests\DeleteRoomRequest;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class RoomController extends Controller
{
    public function index(): Response
    {
        return Inertia::render(
            'Room/Index',
            [
                'rooms' => RoomResource::collection(Room::all()),
            ]);
    }

    public function store(
        StoreRoomRequest $request,
        CreateRoomAction $action
    ): RedirectResponse {
        $action->handle($request->validated());

        return to_route('rooms.index');
    }

    public function show(string $id): Response
    {
        $room = Room::query()->findOrFail($id)->load('machines');

        return Inertia::render(
            'Room/Show',
            [
                'room' => RoomResource::make($room),
            ]
        );
    }

    public function update(
        UpdateRoomRequest $request,
        string $id,
        UpdateRoomAction $action
    ): RedirectResponse {
        $action->handle($id, $request->validated());

        return to_route('rooms.index');
    }

    public function destroy(
        DeleteRoomRequest $request,
        string $id,
        DeleteRoomAction $action
    ): RedirectResponse {
        $action->handle($id);

        return to_route('rooms.index');
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateAndDispatchComputerCommandAction;
use App\Actions\CreateAndDispatchRoomCommandAction;
use App\Actions\UpdateCommandStatusAction;
use App\Http\Requests\CreateComputerCommandRequest;
use App\Http\Requests\CreateRoomCommandRequest;
use App\Http\Requests\UpdateCommandStatusRequest;
use App\Models\Command;
use Illuminate\Http\JsonResponse;

final class CommandController extends Controller
{
    public function dispatchToComputer(
        CreateComputerCommandRequest $request,
        CreateAndDispatchComputerCommandAction $action
    ): JsonResponse {
        $result = $action->handle($request->validated());

        return response()->json(
            [
                'success' => $result['success'],
                'message' => $result['message'],
                'command' => $result['command'],
            ],
            $result['status_code']);
    }

    public function dispatchToRoom(
        CreateRoomCommandRequest $request,
        CreateAndDispatchRoomCommandAction $action
    ): JsonResponse {
        $result = $action->handle($request->validated());

        return response()->json(
            [
                'success' => $result['success'],
                'message' => $result['message'],
                'command' => $result['command'],
            ],
            $result['status_code']
        );
    }

    public function show(string $id): JsonResponse
    {
        $command = Command::query()->findOrFail($id);

        return response()->json([
            'success' => true,
            'command' => $command,
        ]);
    }

    public function updateStatus(
        UpdateCommandStatusRequest $request,
        string $id,
        UpdateCommandStatusAction $action
    ): JsonResponse {
        $command = $action->handle($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Command status updated',
            'command' => $command,
        ]);
    }
}

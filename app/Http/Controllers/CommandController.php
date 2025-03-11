<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class CommandController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request and insert the command into the database
        $command = Command::create($request->validate([
            'machine_id' => 'required|exists:App\Models\Machine,id',
            'command_type' => 'required|string',
        ]));

        // Prepare the command data
        $commandData = [
            'id' => $command->id,
            'machine_id' => $command->machine_id,
            'command_type' => $command->command_type,
        ];

        try {
            Queue::connection('rabbitmq')->pushRaw(json_encode([
                'data' => $commandData,
            ]), "command.machine.{$command->machine_id}");

            return redirect()->back();
            // ->with('success', 'Lệnh đã được gửi thành công');
        } catch (\Exception $e) {
            // Log lỗi chi tiết
            Log::error('Lỗi gửi lệnh: '.$e->getMessage(), [
                'command_id' => $command->id,
                'machine_id' => $command->machine_id,
            ]);

            // Cập nhật trạng thái lệnh trong database
            $command->status = 'failed';
            $command->payload = [
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ];
            $command->save();

            return redirect()->back();
            // ->with('error', 'Không thể gửi lệnh đến máy tính');
        }
    }
}

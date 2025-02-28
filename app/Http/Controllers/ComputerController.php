<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessComputerCommand;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ComputerController extends Controller
{
    /**
     * Store a newly created computer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mac_address' => ['required', 'string', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', 'unique:machines,mac_address'],
            'ip_address' => ['required', 'string', 'ip'],
            'pos_row' => ['required', 'integer', 'min:1'],
            'pos_col' => ['required', 'integer', 'min:1'],
            'room_id' => ['required', 'exists:rooms,id'],
        ]);

        // Add last_seen timestamp to mark as recently added
        $validated['last_seen'] = Carbon::now();

        $machine = Machine::create($validated);

        return redirect()->back()->with('success', 'Máy tính đã được thêm thành công.');
    }

    /**
     * Update the specified computer in storage.
     */
    public function update(Request $request, string $id)
    {
        $machine = Machine::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mac_address' => ['required', 'string', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', 'unique:machines,mac_address,'.$id],
            'ip_address' => ['required', 'string', 'ip'],
            'pos_row' => ['required', 'integer', 'min:1'],
            'pos_col' => ['required', 'integer', 'min:1'],
            'room_id' => ['required', 'exists:rooms,id'],
        ]);

        $machine->update($validated);

        return redirect()->back()->with('success', 'Thông tin máy tính đã được cập nhật.');
    }

    /**
     * Remove the specified computer from storage.
     */
    public function destroy(string $id)
    {
        $machine = Machine::findOrFail($id);
        $machine->delete();

        return redirect()->back()->with('success', 'Máy tính đã được xóa.');
    }

    /**
     * Send a command to the specified computer.
     */
    public function sendCommand(Request $request, string $id)
    {
        $validated = $request->validate([
            'command' => 'required|string',
            'params' => 'array|nullable',
        ]);

        ProcessComputerCommand::dispatch(
            $id,
            $validated['command'],
        )->onQueue($id.'.#');

        return redirect()->back()->with('success', 'Lệnh đã được gửi đến máy tính.');
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMachineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'mac_address' => 'sometimes|required|mac_address|unique:machines,mac_address,'.$this->machine,
            'ip_address' => 'sometimes|required|ip',
            'pos_row' => 'prohibited', // Cấm hoàn toàn việc cập nhật
            'pos_col' => 'prohibited', // Cấm hoàn toàn việc cập nhật
            'room_id' => 'prohibited', // Cấm hoàn toàn việc cập nhật
        ];
    }
}

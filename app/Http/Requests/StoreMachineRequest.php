<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMachineRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'mac_address' => ['required', 'string', 'mac_address', 'unique:\App\Models\Machine,mac_address'],
            'ip_address' => ['required', 'string', 'ip'],
            'pos_row' => ['required', 'integer', 'min:1'],
            'pos_col' => ['required', 'integer', 'min:1'],
            'room_id' => ['required', 'uuid', 'exists:rooms,id'],
        ];
    }
}

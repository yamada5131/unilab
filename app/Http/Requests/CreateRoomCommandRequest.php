<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateRoomCommandRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'room_id' => ['required', 'string', 'uuid', 'exists:rooms,id'],
            'command_type' => ['required', 'string', 'in:SHUTDOWN,RESTART,LOCK,UNLOCK,EXECUTE,UPDATE'],
            'params' => ['sometimes', 'array'],
            'params.*.name' => ['required_with:params', 'string'],
            'params.*.value' => ['required_with:params', 'string'],
            'priority' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'expires_in' => ['sometimes', 'integer', 'min:60', 'max:86400'], // 1 minute to 24 hours
        ];
    }
}

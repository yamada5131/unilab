<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateComputerCommandRequest extends FormRequest
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
            'machine_id' => ['required', 'string', 'uuid', 'exists:machines,id'],
            'command_type' => ['required', 'string', 'in:SHUTDOWN,RESTART,LOCK,UNLOCK,EXECUTE,UPDATE'],
            'params' => ['sometimes', 'array'],
            'params.*.name' => ['required_with:params', 'string'],
            'params.*.value' => ['required_with:params', 'string'],
        ];
    }
}

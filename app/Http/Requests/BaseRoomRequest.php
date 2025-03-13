<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRoomRequest extends FormRequest
{
    /**
     * Get the base validation rules that apply to rooms.
     */
    protected function baseRules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'grid_rows' => 'required|integer|min:1',
            'grid_cols' => 'required|integer|min:1',
        ];
    }
}

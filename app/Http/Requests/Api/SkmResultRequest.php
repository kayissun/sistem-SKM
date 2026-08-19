<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SkmResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'periode_survei_id' => ['nullable', 'integer', 'exists:periode_survei,id'],
            'unit_layanan_id' => ['nullable', 'integer', 'exists:unit_layanan,id'],
        ];
    }
}
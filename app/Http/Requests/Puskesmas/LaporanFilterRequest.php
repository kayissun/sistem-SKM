<?php

namespace App\Http\Requests\Puskesmas;

use Illuminate\Foundation\Http\FormRequest;

class LaporanFilterRequest extends FormRequest
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
            'per_halaman' => ['nullable', 'integer', 'in:10,30,50,100'],
        ];
    }
}
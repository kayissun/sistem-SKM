<?php

namespace App\Http\Requests\Dinkes;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePuskesmasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:puskesmas,rsu'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

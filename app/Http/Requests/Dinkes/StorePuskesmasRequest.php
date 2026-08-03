<?php

namespace App\Http\Requests\Dinkes;

use Illuminate\Foundation\Http\FormRequest;

class StorePuskesmasRequest extends FormRequest
{
    public function authorize(): bool
    {
        // otorisasi akses halaman sudah ditangani middleware 'role:dinkes' di route
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
            'admin_nama' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'unique:users,email'],
        ];
    }
}

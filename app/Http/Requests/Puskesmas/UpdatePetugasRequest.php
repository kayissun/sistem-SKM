<?php

namespace App\Http\Requests\Puskesmas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdatePetugasRequest extends FormRequest
{
    public function authorize(): bool
    {
        // cegah admin-puskesmas mengedit akun milik unit lain lewat manipulasi URL
        $petugas = $this->route('petugas');

        return $petugas && $petugas->puskesmas_id === Auth::user()->puskesmas_id;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->route('petugas'))],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

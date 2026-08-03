<?php

namespace App\Http\Requests\Puskesmas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UnitLayananRequest extends FormRequest
{
    public function authorize(): bool
    {
        $unitLayanan = $this->route('unit_layanan');

        // store(): belum ada model terikat ke route, cukup lolos (halaman sudah dijaga middleware role)
        if (! $unitLayanan) {
            return true;
        }

        // update(): cegah admin-puskesmas mengedit unit layanan milik unit lain lewat manipulasi URL
        return $unitLayanan->puskesmas_id === Auth::user()->puskesmas_id;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

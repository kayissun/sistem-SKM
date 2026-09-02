<?php

namespace App\Http\Requests\Dinkes;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnsurPelayananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:10', 'unique:unsur_pelayanan,kode'],
            'nama_unsur' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
        ];
    }
}

<?php

namespace App\Http\Requests\Dinkes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnsurPelayananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => [
                'required', 'string', 'max:10',
                Rule::unique('unsur_pelayanan', 'kode')->ignore($this->route('unsur_pelayanan')),
            ],
            'nama_unsur' => ['required', 'string', 'max:255'],
            'urutan' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

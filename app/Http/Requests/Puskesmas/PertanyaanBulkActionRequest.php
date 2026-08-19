<?php

namespace App\Http\Requests\Puskesmas;

use Illuminate\Foundation\Http\FormRequest;

class PertanyaanBulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dipilih' => ['required', 'array', 'min:1'],
            'dipilih.*' => ['integer', 'exists:pertanyaan_survei,id'],
            'aksi' => ['required', 'in:hapus'],
        ];
    }
}
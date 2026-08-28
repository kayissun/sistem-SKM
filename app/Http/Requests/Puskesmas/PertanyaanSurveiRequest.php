<?php

namespace App\Http\Requests\Puskesmas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PertanyaanSurveiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pertanyaan = $this->route('pertanyaan');

        if (! $pertanyaan) {
            return true;
        }

        return $pertanyaan->puskesmas_id === Auth::user()->puskesmas_id;
    }

    public function rules(): array
    {
        return [
            'unsur_pelayanan_id' => ['nullable', 'exists:unsur_pelayanan,id'],
            'teks_pertanyaan'    => ['required', 'string', 'max:255'],
            'tipe_input'         => ['required', 'in:skala,teks'],
            'gaya_tampilan'      => ['nullable', 'in:radio,dropdown', 'required_if:tipe_input,skala'],
            'layout_mode'        => ['nullable', 'in:default,stacked,separated'],
            'header_image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'label_skala_1'      => ['nullable', 'string', 'max:100'],
            'label_skala_2'      => ['nullable', 'string', 'max:100'],
            'label_skala_3'      => ['nullable', 'string', 'max:100'],
            'label_skala_4'      => ['nullable', 'string', 'max:100'],
            'urutan'             => ['nullable', 'integer', 'min:1'],
            'is_active'          => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'unsur_pelayanan_id' => $this->input('unsur_pelayanan_id') ?: null,
            'layout_mode'        => $this->input('layout_mode') ?: 'default',
        ]);

        if ($this->input('tipe_input') === 'teks') {
            $this->merge([
                'unsur_pelayanan_id' => null,
                'gaya_tampilan'      => null,
                'label_skala_1'      => null,
                'label_skala_2'      => null,
                'label_skala_3'      => null,
                'label_skala_4'      => null,
            ]);
        }
    }
}
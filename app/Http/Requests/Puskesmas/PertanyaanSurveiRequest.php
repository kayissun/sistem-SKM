<?php

namespace App\Http\Requests\Puskesmas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PertanyaanSurveiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pertanyaan = $this->route('pertanyaan');

        // store(): belum ada model terikat ke route, cukup lolos (halaman sudah dijaga middleware role)
        if (! $pertanyaan) {
            return true;
        }

        // update(): cegah admin-puskesmas mengedit pertanyaan milik unit lain lewat manipulasi URL
        return $pertanyaan->puskesmas_id === Auth::user()->puskesmas_id;
    }

    public function rules(): array
    {
        return [
            'unsur_pelayanan_id' => ['nullable', 'exists:unsur_pelayanan,id'],
            'teks_pertanyaan' => ['required', 'string', 'max:255'],
            'tipe_input' => ['required', 'in:skala,teks'],
            'gaya_tampilan' => ['nullable', 'in:radio,dropdown', 'required_if:tipe_input,skala'],
            'label_skala_1' => ['nullable', 'string', 'max:100'],
            'label_skala_2' => ['nullable', 'string', 'max:100'],
            'label_skala_3' => ['nullable', 'string', 'max:100'],
            'label_skala_4' => ['nullable', 'string', 'max:100'],
            'urutan' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Normalisasi sebelum validasi: pertanyaan tipe teks tidak boleh dikaitkan ke unsur baku
     * (jawaban teks bukan angka, tidak bisa masuk rumus SKM), dan gaya tampilan/label
     * cuma relevan untuk tipe skala. Dipaksa null di sini (server-side), jangan cuma
     * andalkan disabled di frontend.
     */
    protected function prepareForValidation(): void
    {
        // unsur_pelayanan_id dari <select> datang sebagai string kosong kalau "Tidak ada" dipilih,
        // ubah jadi null asli dulu sebelum divalidasi, supaya konsisten tersimpan sebagai NULL.
        $this->merge([
            'unsur_pelayanan_id' => $this->input('unsur_pelayanan_id') ?: null,
        ]);

        if ($this->input('tipe_input') === 'teks') {
            $this->merge([
                'unsur_pelayanan_id' => null,
                'gaya_tampilan' => null,
                'label_skala_1' => null,
                'label_skala_2' => null,
                'label_skala_3' => null,
                'label_skala_4' => null,
            ]);
        }
    }
}

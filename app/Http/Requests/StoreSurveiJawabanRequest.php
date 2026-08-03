<?php

namespace App\Http\Requests;

use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Support\OpsiDataDiri;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StoreSurveiJawabanRequest extends FormRequest
{
    protected ?PeriodeSurvei $periodeAktif = null;

    protected ?Collection $daftarPertanyaan = null;

    /**
     * Dipakai juga untuk mengecek 3 prasyarat sebelum data divalidasi:
     * unit harus aktif, periode survei harus ada yang aktif, dan kuesioner tidak boleh kosong.
     * Kalau salah satu gagal, langsung short-circuit ke response yang sesuai (404 atau redirect
     * dengan pesan) lewat exception, tanpa lanjut ke rules().
     */
    public function authorize(): bool
    {
        $puskesmas = $this->route('puskesmas');

        if (! $puskesmas->is_active) {
            throw new NotFoundHttpException();
        }

        // periode aktif diambil ulang dari server, bukan dari input form,
        // supaya tidak bisa dimanipulasi/telat berubah kalau periode ganti saat isi form
        $this->periodeAktif = PeriodeSurvei::where('is_active', true)->first();

        if (! $this->periodeAktif) {
            throw new HttpResponseException(
                redirect()->back()->with('error', 'Survei sedang tidak dibuka untuk periode ini.')
            );
        }

        $this->daftarPertanyaan = PertanyaanSurvei::where('puskesmas_id', $puskesmas->id)->aktif()->get();

        if ($this->daftarPertanyaan->isEmpty()) {
            throw new HttpResponseException(
                redirect()->back()->with('error', 'Kuesioner belum tersedia untuk unit ini.')
            );
        }

        return true;
    }

    public function rules(): array
    {
        $puskesmas = $this->route('puskesmas');

        $rules = [
            'unit_layanan_id' => [
                'nullable',
                Rule::exists('unit_layanan', 'id')->where('puskesmas_id', $puskesmas->id),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:25'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'usia_rentang' => ['required', Rule::in(OpsiDataDiri::usia())],
            'pendidikan' => ['required', Rule::in(OpsiDataDiri::pendidikan())],
            'pekerjaan' => ['required', Rule::in(OpsiDataDiri::pekerjaan())],
        ];

        foreach ($this->daftarPertanyaan as $pertanyaan) {
            $rules["jawaban.{$pertanyaan->id}"] = $pertanyaan->tipe_input === 'teks'
                ? ['nullable', 'string', 'max:2000']
                : ['required', 'integer', 'between:1,4'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'jawaban.*.required' => 'Semua pertanyaan skala wajib dinilai.',
        ];
    }

    /**
     * Sudah dihitung sekali di authorize() (yang selalu jalan sebelum rules()),
     * dipakai ulang di controller supaya tidak query 2x.
     */
    public function daftarPertanyaanAktif(): Collection
    {
        return $this->daftarPertanyaan;
    }

    public function periodeAktif(): PeriodeSurvei
    {
        return $this->periodeAktif;
    }
}

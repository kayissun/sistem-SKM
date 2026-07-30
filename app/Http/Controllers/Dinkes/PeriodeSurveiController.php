<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use App\Models\PeriodeSurvei;
use Illuminate\Http\Request;

class PeriodeSurveiController extends Controller
{
    public function index()
    {
        $daftarPeriode = PeriodeSurvei::orderByDesc('tanggal_mulai')->paginate(10);

        return view('dinkes.periode-survei.index', compact('daftarPeriode'));
    }

    public function create()
    {
        return view('dinkes.periode-survei.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_active']) {
            // pastikan cuma 1 periode aktif dalam satu waktu
            PeriodeSurvei::where('is_active', true)->update(['is_active' => false]);
        }

        PeriodeSurvei::create($data);

        return redirect()
            ->route('dinkes.periode-survei.index')
            ->with('success', 'Periode survei berhasil dibuat.');
    }

    public function edit(PeriodeSurvei $periode_survei)
    {
        return view('dinkes.periode-survei.edit', ['periode' => $periode_survei]);
    }

    public function update(Request $request, PeriodeSurvei $periode_survei)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_active']) {
            PeriodeSurvei::where('is_active', true)
                ->where('id', '!=', $periode_survei->id)
                ->update(['is_active' => false]);
        }

        $periode_survei->update($data);

        return redirect()
            ->route('dinkes.periode-survei.index')
            ->with('success', 'Periode survei berhasil diperbarui.');
    }

    public function destroy(PeriodeSurvei $periode_survei)
    {
        if ($periode_survei->surveiJawaban()->exists()) {
            return redirect()
                ->route('dinkes.periode-survei.index')
                ->with('error', 'Periode ini sudah punya data survei, tidak bisa dihapus.');
        }

        $periode_survei->delete();

        return redirect()
            ->route('dinkes.periode-survei.index')
            ->with('success', 'Periode survei berhasil dihapus.');
    }
}

@php
    // $nilai: array asosiatif ['U1' => 90.0, 'U2' => 89.6, ...] skala 0-100
    $kodeList = array_keys($nilai);
    $n = count($kodeList);
    $pusat = 130;
    $radiusMaks = 95;
    $sudut = fn ($i) => deg2rad(-90 + $i * (360 / max($n, 1)));

    $titikPadaLevel = function ($persen) use ($n, $pusat, $radiusMaks, $sudut) {
        $titik = [];
        for ($i = 0; $i < $n; $i++) {
            $r = $radiusMaks * $persen;
            $titik[] = [$pusat + $r * cos($sudut($i)), $pusat + $r * sin($sudut($i))];
        }
        return $titik;
    };

    $titikPoligon = [];
    foreach ($kodeList as $i => $kode) {
        $persen = max(0, min(1, ($nilai[$kode] ?? 0) / 100));
        $r = $radiusMaks * $persen;
        $titikPoligon[] = [$pusat + $r * cos($sudut($i)), $pusat + $r * sin($sudut($i))];
    }

    $keString = fn ($titik) => implode(' ', array_map(fn ($p) => $p[0] . ',' . $p[1], $titik));
@endphp

<svg viewBox="0 0 260 260" width="220" height="220" role="img" aria-label="Grafik radar profil unsur pelayanan">
    @foreach ([0.25, 0.5, 0.75, 1] as $level)
        <polygon points="{{ $keString($titikPadaLevel($level)) }}" fill="none" stroke="#E4DEFB" stroke-width="1"/>
    @endforeach

    @for ($i = 0; $i < $n; $i++)
        @php $ujung = $titikPadaLevel(1)[$i]; @endphp
        <line x1="{{ $pusat }}" y1="{{ $pusat }}" x2="{{ $ujung[0] }}" y2="{{ $ujung[1] }}" stroke="#E4DEFB" stroke-width="1"/>
    @endfor

    <polygon points="{{ $keString($titikPoligon) }}" fill="#7C3AED" fill-opacity="0.28" stroke="#6D28D9" stroke-width="2"/>

    @foreach ($titikPoligon as $p)
        <circle cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="2.6" fill="#6D28D9"/>
    @endforeach

    @for ($i = 0; $i < $n; $i++)
        @php
            $labelR = $radiusMaks + 16;
            $lx = $pusat + $labelR * cos($sudut($i));
            $ly = $pusat + $labelR * sin($sudut($i));
        @endphp
        <text x="{{ $lx }}" y="{{ $ly }}" font-size="10" text-anchor="middle" dominant-baseline="middle" fill="#675F7A">{{ $kodeList[$i] }}</text>
    @endfor
</svg>

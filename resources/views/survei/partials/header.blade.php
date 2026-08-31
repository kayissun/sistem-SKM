@php
    $adaFoto = $puskesmas->formHeaderImageUrl();
    $tampilkanInfo = ($showProgress ?? false) && $daftarPertanyaan->isNotEmpty();
    $estimasiMenit = $tampilkanInfo ? max(1, (int) ceil(($daftarPertanyaan->count() * 15 + 60) / 60)) : 0;
@endphp
<div class="skm-header {{ $adaFoto ? 'has-photo' : '' }}">
    @if ($adaFoto)
        <img src="{{ $adaFoto }}" alt="" class="skm-header-photo">
    @endif
    <div class="skm-header-overlay"></div>
    <div class="skm-header-content">
        <h4>{{ $puskesmas->nama }}</h4>
        <div class="skm-hint">Survei Kepuasan Masyarakat</div>

        @if ($tampilkanInfo)
            <ul class="skm-header-chips">
                <li><i class="bi bi-list-check" aria-hidden="true"></i> {{ $daftarPertanyaan->count() }} pertanyaan</li>
                <li><i class="bi bi-clock" aria-hidden="true"></i> ~{{ $estimasiMenit }} menit</li>
                <li><i class="bi bi-shield-lock" aria-hidden="true"></i> Jawaban rahasia</li>
            </ul>
        @endif
    </div>
    @if ($showProgress ?? false)
        <div class="skm-header-progress" role="progressbar" aria-valuemin="1" :aria-valuemax="totalStep" :aria-valuenow="step">
            <div class="skm-header-progress-bar" :style="`width: ${(step / totalStep) * 100}%`"></div>
        </div>
    @endif
</div>
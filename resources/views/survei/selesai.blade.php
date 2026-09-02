@extends('layouts.publik')

@section('title', 'Terima Kasih - ' . $puskesmas->nama)

@section('content')
<style>
:root {
    --purple-900: #180733;
    --purple-800: #2E1065;
    --purple-700: #6D28D9;
    --purple-600: #7C3AED;
    --purple-100: #EDE9FE;
    --purple-50:  #FAF8FF;

    --gold-700:   #A66A0E;
    --gold-600:   #C88719;
    --gold-500:   #E4A63B;
    --gold-100:   #FCF1DC;
    --gold-50:    #FFFBEB;

    --ink-main:   #14102B;
    --ink-muted:  #625B78;
    --surface-0:  #FFFFFF;
}

.skm-selesai {
    max-width: 500px;
    margin: 40px auto;
    padding: 0 16px;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: var(--ink-main);
}

.skm-selesai .skm-card {
    background: var(--surface-0);
    border: 1px solid rgba(109, 40, 217, 0.12);
    border-radius: 24px;
    box-shadow: 0 20px 45px -10px rgba(24, 7, 51, 0.08),
                0 4px 12px rgba(200, 135, 25, 0.04);
    padding: 44px 32px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Accent Bar Atas Ungu-Emas */
.skm-selesai .skm-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 6px;
    background: linear-gradient(90deg, var(--purple-700) 0%, var(--gold-500) 100%);
}

.skm-selesai .skm-visual-wrap {
    margin: 0 auto 24px;
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Visual Ilustrasi / Fallback Ring Icon */
.skm-selesai .skm-visual {
    width: 140px; 
    height: 140px; 
    margin: 0 auto;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--purple-50) 0%, var(--gold-50) 100%);
    border: 2px dashed rgba(228, 166, 59, 0.4);
    display: flex; 
    align-items: center; 
    justify-content: center;
    position: relative;
}

.skm-selesai .skm-visual img {
    width: 100%; 
    height: 100%; 
    object-fit: contain;
}

.skm-selesai .skm-check-badge {
    width: 64px; 
    height: 64px; 
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold-500) 0%, var(--gold-700) 100%);
    color: #fff;
    display: flex; 
    align-items: center; 
    justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 8px 20px rgba(166, 106, 14, 0.3);
    border: 3px solid #fff;
}

.skm-selesai h4 { 
    font-weight: 800; 
    color: var(--purple-900); 
    font-size: 1.5rem;
    margin-bottom: 12px;
    letter-spacing: -0.02em;
}

.skm-selesai p { 
    color: var(--ink-muted); 
    line-height: 1.6; 
    font-size: 0.9375rem;
    margin-bottom: 0;
}

.skm-selesai p strong {
    color: var(--purple-900);
}

.skm-selesai .skm-badge {
    display: inline-flex; 
    align-items: center; 
    gap: 8px;
    background: var(--gold-100); 
    color: var(--gold-700);
    border: 1px solid rgba(200, 135, 25, 0.25);
    font-size: 0.8125rem; 
    font-weight: 600;
    padding: 8px 18px; 
    border-radius: 999px;
    margin: 24px 0 28px;
}

.skm-selesai .skm-btn-outline {
    display: inline-flex; 
    align-items: center; 
    justify-content: center;
    gap: 8px;
    border: 1.5px solid var(--purple-700); 
    color: var(--purple-700);
    background: #fff; 
    border-radius: 999px;
    padding: 12px 28px; 
    font-weight: 700; 
    font-size: 0.9375rem;
    text-decoration: none; 
    min-height: 48px;
    transition: all 0.2s ease;
    width: 100%;
    max-width: 260px;
}

.skm-selesai .skm-btn-outline:hover { 
    background: var(--purple-700); 
    color: #fff; 
    box-shadow: 0 6px 18px rgba(109, 40, 217, 0.25);
    transform: translateY(-1px);
}

.skm-selesai .skm-btn-outline:hover svg path {
    stroke: #fff;
}

.skm-selesai .skm-btn-outline:focus-visible { 
    outline: 3px solid var(--purple-600); 
    outline-offset: 2px; 
}
</style>

<div class="skm-selesai">
    <div class="skm-card">
        <div class="skm-visual-wrap">
            <div class="skm-visual" aria-hidden="true">
                @if (file_exists(public_path('images/ilustrasi-selesai.svg')))
                    <img src="{{ asset('images/ilustrasi-selesai.svg') }}" alt="Selesai">
                @else
                    <div class="skm-check-badge">
                        <i class="fa-solid fa-check"></i>
                    </div>
                @endif
            </div>
        </div>

        <h4>Terima Kasih!</h4>
        <p>Penilaian Anda untuk <strong>{{ $puskesmas->nama }}</strong> sudah berhasil direkam dan akan sangat membantu kami dalam meningkatkan kualitas pelayanan.</p>

        <div class="skm-badge">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M12 2l7 3v6c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V5l7-3z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            Jawaban Anda tersimpan secara rahasia
        </div>

        <div>
            <a href="{{ route('survei.create', $puskesmas) }}" class="skm-btn-outline">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M4 4v5h5M20 20v-5h-5M4.5 9a8 8 0 0114.5-3.5M19.5 15a8 8 0 01-14.5 3.5" stroke="#6d28d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Isi survei lagi
            </a>
        </div>
    </div>
</div>
@endsection
@extends('layouts.publik')

@section('title', 'Terlalu Banyak Percobaan')

@section('content')
<style>
    :root {
        /* 60% — dominant surfaces */
        --surface-0: #FFFFFF;
        --surface-1: #FAF8FF;
        --surface-2: #F3EEFF;

        /* 30% — secondary (purple identity) */
        --purple-900: #180733;
        --purple-800: #2E1065;
        --purple-700: #6D28D9;
        --purple-600: #7C3AED;
        --purple-500: #8B5CF6;
        --purple-100: #EDE9FE;
        --ink: #14102B;
        --ink-muted: #625B78;

        /* 10% — accent (gold identity) */
        --gold-700: #A66A0E;
        --gold-600: #C88719;
        --gold-400: #E4A63B;
        --gold-100: #FCF1DC;

        --gradient-primary: linear-gradient(135deg, #7C3AED 0%, #5B21B6 55%, #2A0B5E 100%);
        --gradient-gold: linear-gradient(135deg, #FCF1DC 0%, #FEEFCE 100%);
        --gradient-gold-border: linear-gradient(135deg, #E4A63B 0%, #C88719 50%, #A66A0E 100%);
    }

    /* Override wrapper layout publik agar sama seperti preview */
    body.bg-light {
        background: var(--gradient-hero-bg) !important;
    }
    .container {
        max-width: 960px !important; /* 900px card + padding */
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 4rem);
    }

    /* Container Card Utama dengan Bingkai Gold Accent */
    .err429-card-gold {
        background: var(--surface-0);
        max-width: 900px;
        width: 100%;
        margin: 0 auto;
        border-radius: 2rem;
        padding: 3rem 2.5rem;
        /* Bingkai ganda dengan efek Gold Accent */
        border: 2px solid transparent;
        background-image: linear-gradient(var(--surface-0), var(--surface-0)), var(--gradient-gold-border);
        background-origin: border-box;
        background-clip: padding-box, border-box;
        box-shadow: 0 20px 45px -15px rgba(200, 135, 25, 0.25),
                    0 10px 30px -10px rgba(46, 16, 101, 0.1);
    }

    /* Gambar Ilustrasi */
    .err429-illustration {
        width: 100%;
        max-width: 320px;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    /* Content Kanan */
    .err429-badge-gold {
        display: inline-block;
        background: var(--gradient-gold);
        color: var(--gold-700);
        border: 1px solid var(--gold-400);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 0.35rem 1rem;
        border-radius: 999px;
        margin-bottom: 1.25rem;
    }

    .err429-title {
        color: var(--purple-900);
        font-weight: 800;
        font-size: 2.15rem;
        margin-bottom: 1rem;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .err429-desc {
        color: var(--ink-muted);
        font-size: 1rem;
        line-height: 1.65;
        margin-bottom: 0.75rem;
    }

    .err429-retry {
        color: var(--purple-800);
        font-size: 1.05rem;
        font-weight: 600;
        margin-bottom: 2.25rem;
    }

    .err429-retry strong {
        color: var(--purple-600);
    }

    /* Tombol Utama */
    .err429-btn-primary {
        background: var(--gradient-primary);
        color: #ffffff;
        border: none;
        border-radius: 14px;
        font-weight: 600;
        padding: 0.85rem 2.5rem;
        display: inline-block;
        text-decoration: none;
        box-shadow: 0 10px 22px -5px rgba(124, 58, 237, 0.4);
        transition: all 0.2s ease;
    }

    .err429-btn-primary:hover {
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 14px 26px -5px rgba(124, 58, 237, 0.5);
    }

    /* Responsive Mobile View */
    @media (max-width: 767.98px) {
        .err429-card-gold {
            padding: 2rem 1.5rem;
            text-align: center;
        }
        .err429-illustration {
            max-width: 240px;
            margin-bottom: 1.5rem;
        }
    }
</style>

<div class="err429-card-gold">
    <div class="row align-items-center g-4 g-lg-5">

        <!-- KOLOM KIRI: GAMBAR ILUSTRASI -->
        <div class="col-md-5 text-center">
            <img src="{{ asset('images/error-429.svg') }}"
                 alt="Ilustrasi Terlalu Banyak Percobaan"
                 class="err429-illustration"
                 onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 200 140\' fill=\'none\'><ellipse cx=\'100\' cy=\'128\' rx=\'62\' ry=\'8\' fill=\'%23EDE9FE\'/><rect x=\'66\' y=\'12\' width=\'68\' height=\'10\' rx=\'5\' fill=\'%232E1065\'/><rect x=\'66\' y=\'118\' width=\'68\' height=\'10\' rx=\'5\' fill=\'%232E1065\'/><path d=\'M74 22 C74 52 96 58 96 70 C96 82 74 88 74 118\' stroke=\'%237C3AED\' stroke-width=\'4\' fill=\'none\'/><path d=\'M126 22 C126 52 104 58 104 70 C104 82 126 88 126 118\' stroke=\'%237C3AED\' stroke-width=\'4\' fill=\'none\'/><path d=\'M92 62 C92 66 97 68 100 70 C103 68 108 66 108 62 Z\' fill=\'%23C88719\'/><path d=\'M90 100 L110 100 C110 93 103 90 100 86 C97 90 90 93 90 100 Z\' fill=\'%23C88719\'/></svg>';">
        </div>

        <!-- KOLOM KANAN: TEKS DOKUMEN & TOMBOL -->
        <div class="col-md-7">
            <span class="err429-badge-gold">Error 429</span>

            <h1 class="err429-title">Mohon Tunggu Sebentar</h1>

            <p class="err429-desc">
                Untuk menjaga kualitas data survei, terdapat batas jumlah pengisian dalam waktu singkat dari perangkat yang sama.
            </p>

            @php
                $retryAfter = method_exists($exception ?? null, 'getHeaders')
                    ? ($exception->getHeaders()['Retry-After'] ?? null)
                    : null;
            @endphp

            @if ($retryAfter)
                <p class="err429-retry">
                    Silakan coba lagi dalam kurang lebih <strong>{{ max(1, ceil($retryAfter / 60)) }} menit</strong>.
                </p>
            @else
                <p class="err429-retry">
                    Silakan coba lagi dalam beberapa menit.
                </p>
            @endif

            <div>
                <a href="javascript:history.back()" class="err429-btn-primary">← Kembali ke Form</a>
            </div>
        </div>

    </div>
</div>
@endsection
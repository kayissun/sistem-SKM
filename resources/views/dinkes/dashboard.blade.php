@extends('layouts.dinkes')
@section('title', 'Dashboard')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">

    <style>
        .sp-welcome {
            background: linear-gradient(135deg, #7C3AED, #2A0B5E);
            border-radius: 16px;
            padding: 26px 30px;
            color: #fff;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }
        .sp-welcome h2 { font-weight: 800; font-size: 1.3rem; margin-bottom: 4px; }
        .sp-welcome p  { margin: 0; color: rgba(255,255,255,.75); font-size: .9rem; }

        .sp-stat-card .icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.05rem;
            margin-bottom: 14px;
        }
        .sp-stat-card .label { font-size: .8rem; color: #635C7A; font-weight: 600; }
        .sp-stat-card .value { font-weight: 800; color: #180733; margin: 4px 0 10px; }
        .sp-stat-card a { font-size: .83rem; font-weight: 700; color: #6D28D9; }
        .sp-stat-card a:hover { color: #2E1065; }

        .sp-quick a {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid rgba(109,40,217,.10);
            background: #fff;
            font-weight: 600; font-size: .88rem; color: #180733;
            transition: .15s;
        }
        .sp-quick a:hover { background: #FAF8FF; border-color: rgba(109,40,217,.25); }
        .sp-quick a i {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: #EDE9FE; color: #6D28D9;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem; flex-shrink: 0;
        }
    </style>

    <div class="sp-welcome">
        <div>
            <h2>Halo, {{ auth()->user()->name ?? 'Admin' }} 👋</h2>
            <p>Berikut ringkasan layanan kesehatan di Kabupaten Purworejo.</p>
        </div>
        <div class="text-white-50 small text-end">
            <i class="fa-regular fa-calendar me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card h-100 sp-stat-card">
                <div class="card-body">
                    <div class="icon" style="background: linear-gradient(135deg,#7C3AED,#2A0B5E)">
                        <i class="fa-solid fa-hospital"></i>
                    </div>
                    <div class="label">Unit Aktif</div>
                    <div class="value fs-2">{{ $jumlahUnit }}</div>
                    <a href="{{ route('dinkes.puskesmas.index') }}">Kelola unit <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 sp-stat-card">
                <div class="card-body">
                    <div class="icon" style="background: linear-gradient(135deg,#C88719,#E4A63B)">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div class="label">Periode Survei Aktif</div>
                    <div class="value fs-5">{{ $periodeAktif->nama ?? 'Belum ada' }}</div>
                    <a href="{{ route('dinkes.periode-survei.index') }}">Kelola periode <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 sp-stat-card">
                <div class="card-body">
                    <div class="icon" style="background: linear-gradient(135deg,#10B981,#047857)">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div class="label">Laporan</div>
                    <div class="value fs-6">Rekap IKM seluruh unit</div>
                    <a href="{{ route('dinkes.laporan.index') }}">Buka laporan <i class="fa-solid fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">Akses Cepat</div>
        <div class="card-body">
            <div class="row g-3 sp-quick">
                <div class="col-md-4">
                    <a href="{{ route('dinkes.unsur-pelayanan.index') }}">
                        <i class="fa-solid fa-list-check"></i> Unsur Pelayanan
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('dinkes.klaster.index') }}">
                        <i class="fa-solid fa-layer-group"></i> Klaster Performa
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('dinkes.aktivitas.index') }}">
                        <i class="fa-solid fa-clock-rotate-left"></i> Log Aktivitas
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($clusters->count())
    <table>
        <tr>
            <th>Unit</th>
            <th>Nilai</th>
            <th>Cluster</th>
            <th>Rekomendasi</th>
        </tr>

        @foreach($clusters as $item)
        <tr>
            <td>{{ $item->puskesmas->nama ?? '-' }}</td>
            <td>{{ $item->nilai_rata2 }}</td>
            <td>{{ $item->cluster_nama ?: $item->cluster }}</td>
            <td>{{ $item->rekomendasi }}</td>
        </tr>
        @endforeach
    </table>
    @else
    <p>Belum ada data clustering</p>
    @endif

@endsection
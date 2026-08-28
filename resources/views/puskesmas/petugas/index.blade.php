@extends('layouts.puskesmas')

@section('title', 'Petugas')

@section('content')
    <div class="sp-page-head">
        <div>
            <h3>Petugas</h3>
            <p>{{ $daftarPetugas->total() }} petugas terdaftar di unit ini.</p>
        </div>
        <a href="{{ route('puskesmas.petugas.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Tambah Petugas
        </a>
    </div>

    <div class="card sp-table-card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th style="width:120px">Status</th>
                        <th style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarPetugas as $petugas)
                        <tr>
                            <td>{{ $loop->iteration + ($daftarPetugas->currentPage() - 1) * $daftarPetugas->perPage() }}</td>
                            <td class="fw-semibold" style="color:#180733">{{ $petugas->name }}</td>
                            <td>{{ $petugas->email }}</td>
                            <td>
                                @if ($petugas->is_active)
                                    <span class="badge-status-active">Aktif</span>
                                @else
                                    <span class="badge-status-inactive">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('puskesmas.petugas.edit', $petugas) }}" class="sp-icon-btn" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('puskesmas.petugas.destroy', $petugas) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Nonaktifkan akun petugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="sp-icon-btn" title="Nonaktifkan" style="color:#DC2626;border-color:rgba(220,38,38,.15)">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fa-regular fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                                Belum ada petugas. Klik tombol <strong>Tambah Petugas</strong> untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 sp-pagination">
        {{ $daftarPetugas->links('pagination::bootstrap-5') }}
    </div>
@endsection

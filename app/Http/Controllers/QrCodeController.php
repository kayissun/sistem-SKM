<?php

namespace App\Http\Controllers;

use App\Models\Puskesmas;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Response;

class QrCodeController extends Controller
{
    /**
     * Tampilkan QR code sebagai gambar inline (dipakai di <img src="...">).
     */
    public function tampil(Puskesmas $puskesmas): Response
    {
        $hasil = $this->buatQr($puskesmas, 300, 10);

        return response($hasil->getString(), 200)
            ->header('Content-Type', $hasil->getMimeType());
    }

    /**
     * Unduh QR code ukuran besar sebagai file PNG.
     */
    public function unduh(Puskesmas $puskesmas): Response
    {
        $hasil = $this->buatQr($puskesmas, 800, 20);

        return response($hasil->getString(), 200)
            ->header('Content-Type', $hasil->getMimeType())
            ->header('Content-Disposition', 'attachment; filename="qr-survei-' . $puskesmas->slug . '.png"');
    }

    private function buatQr(Puskesmas $puskesmas, int $ukuran, int $margin)
    {
        $url = route('survei.create', $puskesmas);

        $builder = new Builder(
            writer: new PngWriter(),
            data: $url,
            size: $ukuran,
            margin: $margin,
        );

        return $builder->build();
    }
}

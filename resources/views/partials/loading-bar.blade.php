{{--
  resources/views/partials/loading-bar.blade.php

  Loading bar tipis di bagian atas halaman (mirip NProgress di banyak
  portal pemerintah). Cukup include partial ini SEKALI di layout utama
  (di dalam <head> untuk CSS, sebelum </body> untuk JS) — otomatis
  berlaku untuk semua halaman yang memakai layout tersebut, termasuk
  welcome -> login -> dashboard, karena Blade adalah multi-page app
  (setiap navigasi = full page load).
--}}

{{-- taruh dua baris ini di dalam <head> --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
<style>
  /* Warna bar disamakan dengan identitas ungu SIPUAS/DTSEN */
  #nprogress .bar {
    background: linear-gradient(90deg, #7C3AED, #C88719) !important;
    height: 3px !important;
  }
  #nprogress .peg {
    box-shadow: 0 0 10px #7C3AED, 0 0 5px #C88719 !important;
  }
  /* sembunyikan spinner bawaan pojok kanan atas, cukup bar saja */
  #nprogress .spinner { display: none; }
</style>

{{-- taruh blok ini sebelum </body> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
<script>
  NProgress.configure({ showSpinner: false, trickleSpeed: 120, minimum: 0.15 });

  // Mulai bar sesegera mungkin saat halaman ini pertama kali dimuat,
  // lalu selesaikan begitu semua resource siap.
  NProgress.start();
  window.addEventListener('load', () => NProgress.done());

  // Jalankan bar setiap kali user KLIK link yang akan berpindah halaman
  // (link internal, bukan anchor #, bukan target="_blank", bukan mailto/tel).
  document.addEventListener('click', function (e) {
    const link = e.target.closest('a');
    if (!link) return;

    const url = link.getAttribute('href') || '';
    const isSameOrigin = link.hostname === window.location.hostname;
    const isHash = url.startsWith('#');
    const isNewTab = link.target === '_blank';
    const isSpecial = url.startsWith('mailto:') || url.startsWith('tel:') || url.startsWith('javascript:');

    if (isSameOrigin && !isHash && !isNewTab && !isSpecial) {
      NProgress.start();
    }
  });

  // Jalankan bar juga saat user SUBMIT form (mis. form login)
  document.addEventListener('submit', function () {
    NProgress.start();
  });
</script>
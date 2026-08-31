@extends('layouts.publik')

@section('title', 'Survei Kepuasan - ' . $puskesmas->nama)

@section('content')
@push('scripts')
<script>
function surveiApp() {
    return {
        mode: 'dua', // 'dua' = dua langkah, 'satu' = satu pertanyaan per layar
        step: 1,
        panelOpen: false,
        a11y: {
            fontScale: 1,
            contrast: false,
            readableFont: false,
            spacing: false,
            linkHighlight: false,
            bigCursor: false,
            reduceMotion: false
        },
        announce: '',
        totalQuestions: {{ $daftarPertanyaan->count() }},

        init() {
            try {
                const savedMode = localStorage.getItem('skm_survei_mode');
                if (savedMode) this.mode = savedMode;
                const savedA11y = localStorage.getItem('skm_survei_a11y');
                if (savedA11y) this.a11y = { ...this.a11y, ...JSON.parse(savedA11y) };
            } catch (e) { /* localStorage tidak tersedia, pakai default */ }

            this.$watch('step', () => this.onStepChange());
            this.$watch('a11y', () => this.saveA11y(), { deep: true });
        },

        get totalStep() {
            return this.mode === 'satu' ? (2 + this.totalQuestions) : 2;
        },

        setMode(m) {
            if (m === this.mode) return;
            if (this.step > 1) {
                const ok = confirm('Ganti tampilan akan mengulang isian dari awal. Lanjutkan?');
                if (!ok) return;
            }
            this.mode = m;
            this.step = 1;
            try { localStorage.setItem('skm_survei_mode', m); } catch (e) {}
            this.onStepChange();
        },

        saveA11y() {
            try { localStorage.setItem('skm_survei_a11y', JSON.stringify(this.a11y)); } catch (e) {}
        },

        cycleFontScale() {
            const steps = [1, 1.15, 1.3];
            const idx = steps.indexOf(this.a11y.fontScale);
            this.a11y.fontScale = steps[(idx + 1) % steps.length];
        },

        next() { if (this.step < this.totalStep) this.step++; },
        prev() { if (this.step > 1) this.step--; },

        checkStep(n) {
            const fields = this.$refs.form.querySelectorAll(`[data-step="${n}"] input, [data-step="${n}"] select, [data-step="${n}"] textarea`);
            for (const f of fields) {
                if (f.required && !f.checkValidity()) { f.reportValidity(); return false; }
            }
            return true;
        },
        goNext(n) { if (this.checkStep(n)) this.next(); },

        onStepChange() {
            this.$nextTick(() => {
                const target = this.$refs.form.querySelector(`[data-step="${this.step}"], [data-step="final"]`);
                if (target) {
                    const heading = target.querySelector('h5, h6, legend');
                    if (heading) {
                        heading.setAttribute('tabindex', '-1');
                        heading.focus();
                    }
                }
                this.announce = `Langkah ${this.step} dari ${this.totalStep}`;
            });
        },

        speak(text) {
            if (!('speechSynthesis' in window)) {
                alert('Perangkat/peramban ini tidak mendukung pembacaan teks.');
                return;
            }
            window.speechSynthesis.cancel();
            const utter = new SpeechSynthesisUtterance(text);
            utter.lang = 'id-ID';
            window.speechSynthesis.speak(utter);
        },

        speakCurrentStep() {
            const target = this.$refs.form.querySelector(`[data-step="${this.step}"], [data-step="final"]`);
            if (target) this.speak(target.innerText);
        }
    }
}
</script>
@endpush

<style>
.skm-survei {
    --surface-0: #fff;
    --surface-1: #faf8ff;
    --surface-2: #f3eeff;
    --purple-900: #180733;
    --purple-800: #2e1065;
    --purple-700: #6d28d9;
    --purple-600: #7c3aed;
    --purple-100: #ede9fe;
    --gold-700: #a66a0e;
    --gold-400: #e4a63b;
    --gold-100: #fcf1dc;
    --ink: #14102b;
    --ink-muted: #625b78;
    --focus-ring: #2e1065;

    max-width: 680px;
    margin: 0 auto 5.5rem;
    font-family: Inter, sans-serif;
    color: var(--ink);
    transition: font-size .15s ease;
}
.skm-survei.a11y-spacing { line-height: 1.85; }
.skm-survei.a11y-spacing p, .skm-survei.a11y-spacing label { margin-bottom: .6rem; }
.skm-survei.a11y-readable, .skm-survei.a11y-readable input, .skm-survei.a11y-readable select, .skm-survei.a11y-readable textarea {
    font-family: Verdana, Tahoma, sans-serif;
    letter-spacing: .01em;
}
.skm-survei.a11y-linkhighlight a,
.skm-survei.a11y-linkhighlight button,
.skm-survei.a11y-linkhighlight label.skm-scale-option {
    text-decoration: underline;
    outline: 2px solid var(--purple-700);
    outline-offset: 2px;
}
.skm-survei.a11y-bigcursor, .skm-survei.a11y-bigcursor * {
    cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'><circle cx='20' cy='20' r='16' fill='none' stroke='%236D28D9' stroke-width='4'/><circle cx='20' cy='20' r='4' fill='%236D28D9'/></svg>") 20 20, auto !important;
}
.skm-survei.a11y-reducemotion, .skm-survei.a11y-reducemotion * {
    transition-duration: .001s !important;
    animation-duration: .001s !important;
}
.skm-survei.a11y-contrast {
    --surface-0: #000;
    --surface-1: #000;
    --surface-2: #1a1a1a;
    --purple-100: #3a2a66;
    --ink: #fff;
    --ink-muted: #e6e0f5;
    --gold-400: #ffd54a;
    --focus-ring: #ffd54a;
}
.skm-survei.a11y-contrast .skm-card { border: 2px solid #fff; }
.skm-survei.a11y-contrast .form-control,
.skm-survei.a11y-contrast .form-select { background: #000; color: #fff; border: 2px solid #fff; }
.skm-survei.a11y-contrast .skm-header { background: #000; border-bottom: 2px solid #fff; }
.skm-survei.a11y-contrast .skm-primary { background: #ffd54a; color: #000; border: 2px solid #fff; }
.skm-survei.a11y-contrast .skm-secondary { background: #000; color: #fff; border: 2px solid #fff; }

.skm-survei *:focus-visible {
    outline: 3px solid var(--focus-ring, #2e1065);
    outline-offset: 2px;
}

.sr-only {
    position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
    overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;
}
.skm-skip-link {
    position: absolute; left: -9999px; top: 0; z-index: 1000;
    background: var(--purple-900, #180733); color: #fff; padding: .75rem 1.25rem;
    border-radius: 0 0 .5rem 0;
}
.skm-skip-link:focus { left: 0; }

.skm-card {
    background: var(--surface-0);
    border: 1px solid var(--purple-100);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(24,7,51,.12);
}
.skm-header {
    position: relative;
    overflow: hidden;
    padding: 20px 24px 22px;
    background: linear-gradient(135deg,#7c3aed,#4c1d95);
    color: #fff;
    min-height: 96px;
    display: flex;
    align-items: flex-end;
}
.skm-header.has-photo { min-height: 190px; }
.skm-header-photo {
    position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0;
}
.skm-header-overlay {
    position: absolute; inset: 0; z-index: 1;
    background: linear-gradient(180deg, rgba(24,7,51,.1) 0%, rgba(24,7,51,.45) 55%, rgba(24,7,51,.88) 100%);
}
.skm-header-content { position: relative; z-index: 2; width: 100%; }
.skm-header h4 { margin: 0 0 2px; font-weight: 600; }
.skm-header .skm-hint { color: var(--purple-100); }

.skm-header-chips {
    list-style: none; display: flex; flex-wrap: wrap; gap: 8px;
    margin: 12px 0 0; padding: 0;
}
.skm-header-chips li {
    display: flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.3);
    backdrop-filter: blur(2px);
    color: #fff; font-size: .75rem; font-weight: 500;
    padding: 5px 10px; border-radius: 999px; white-space: nowrap;
}
.skm-header-chips li i { color: var(--gold-400); }
.skm-survei.a11y-contrast .skm-header-chips li { background: rgba(0,0,0,.5); border-color: #fff; }
.skm-survei.a11y-contrast .skm-header-chips li i { color: #ffd54a; }

.skm-header-progress {
    position: absolute; left: 0; right: 0; bottom: 0; z-index: 3;
    height: 4px; background: rgba(255,255,255,.3); width: 100%;
}
.skm-header-progress-bar { height: 100%; background: var(--gold-400); transition: width .3s ease; }
.skm-survei.a11y-contrast .skm-header-overlay { background: rgba(0,0,0,.75); }

.skm-body { padding: 28px 24px; }
.skm-hint { color: var(--ink-muted); font-size: .875rem; }

.skm-alert { border-radius: 12px; padding: 12px 16px; font-size: .875rem; margin-bottom: 16px; }
.skm-alert-warning { background: var(--gold-100); color: var(--gold-700); border: 1px solid var(--gold-400); }
.skm-alert-danger { background: #fdecec; color: #8a2a2a; border: 1px solid #f3b9b9; }

.skm-field-label { display: block; font-size: .875rem; font-weight: 500; margin-bottom: 6px; }
.skm-required { color: var(--gold-700); }
.skm-survei .form-control, .skm-survei .form-select {
    border: 1.5px solid var(--purple-100); border-radius: 12px; padding: 10px 14px;
    background: var(--surface-0); color: var(--ink);
}
.skm-survei .form-control:focus, .skm-survei .form-select:focus {
    border-color: var(--purple-600); box-shadow: 0 0 0 3px rgba(124,58,237,.15);
}

.skm-steps { display: flex; align-items: center; gap: .5rem; margin-bottom: 1.5rem; }
.skm-steps .skm-step { display: flex; align-items: center; gap: .5rem; }
.skm-steps .skm-dot {
    width: 1.75rem; height: 1.75rem; border-radius: 999px; display: flex; align-items: center; justify-content: center;
    font-size: .8125rem; font-weight: 600; background: var(--purple-100); color: var(--purple-700); flex-shrink: 0;
}
.skm-steps .skm-step.is-active .skm-dot { background: var(--gold-400); color: var(--purple-900); }
.skm-steps .skm-label { font-size: .875rem; color: var(--ink-muted); }
.skm-steps .skm-step.is-active .skm-label { color: var(--purple-800); font-weight: 600; }
.skm-steps .skm-track { flex: 1; height: 2px; background: var(--purple-100); }

.skm-question { text-align: center; padding: 8px 0 4px; }
.skm-question-image { max-height: 140px; object-fit: cover; border-radius: 12px; border: 1px solid var(--purple-100); margin: 12px auto; display: block; }
.skm-question legend, .skm-question .skm-question-label { font-weight: 500; font-size: 1.0625rem; padding: 0; }

.skm-scale { display: flex; flex-direction: column; gap: 10px; margin-top: 16px; text-align: left; }
.skm-scale-input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.skm-scale-option {
    border: 1.5px solid var(--purple-100); padding: 12px; border-radius: 14px; cursor: pointer;
    display: flex; gap: 12px; align-items: center; transition: .15s;
}
.skm-scale-option:hover { border-color: var(--purple-600); }
.skm-scale-input:checked + .skm-scale-option { background: var(--purple-100); border-color: var(--purple-600); }
.skm-scale-num {
    width: 32px; height: 32px; border-radius: 50%; background: var(--surface-2);
    display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0;
}
.skm-scale-input:checked + .skm-scale-option .skm-scale-num { background: var(--gold-400); }

.skm-btn { border-radius: 999px; padding: 12px 18px; font-weight: 600; border: none; cursor: pointer; min-height: 44px; }
.skm-primary { background: linear-gradient(135deg,#7c3aed,#4c1d95); color: #fff; }
.skm-secondary { background: #fff; border: 1px solid var(--purple-100); color: var(--purple-700); }

/* Panel aksesibilitas mengambang */
.skm-a11y-fab {
    position: fixed; right: 20px; bottom: 20px; z-index: 1050;
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg,#7c3aed,#4c1d95); color: #fff; border: none;
    font-size: 1.4rem; box-shadow: 0 10px 25px rgba(24,7,51,.35); cursor: pointer;
}
.skm-a11y-panel {
    position: fixed; right: 20px; bottom: 86px; z-index: 1050;
    width: min(320px, calc(100vw - 40px)); max-height: 70vh; overflow-y: auto;
    background: #fff; border: 1px solid #ede9fe; border-radius: 16px;
    box-shadow: 0 20px 50px rgba(24,7,51,.25); padding: 16px;
}
.skm-a11y-panel h6 { font-weight: 600; margin-bottom: 10px; color: #180733; }
.skm-a11y-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 0; border-bottom: 1px solid #f3eeff; font-size: .875rem;
}
.skm-a11y-row:last-child { border-bottom: none; }
.skm-a11y-toggle {
    width: 42px; height: 24px; border-radius: 999px; border: 1px solid #ede9fe;
    background: #ede9fe; position: relative; cursor: pointer; flex-shrink: 0;
}
.skm-a11y-toggle[aria-pressed="true"] { background: #7c3aed; }
.skm-a11y-toggle span {
    position: absolute; top: 2px; left: 2px; width: 18px; height: 18px; border-radius: 50%;
    background: #fff; transition: transform .15s ease;
}
.skm-a11y-toggle[aria-pressed="true"] span { transform: translateX(18px); }
.skm-mode-choice { display: flex; gap: 8px; margin-bottom: 6px; }
.skm-mode-choice button {
    flex: 1; font-size: .8125rem; padding: 8px 6px; border-radius: 10px;
    border: 1.5px solid #ede9fe; background: #fff; cursor: pointer;
}
.skm-mode-choice button[aria-pressed="true"] { border-color: #7c3aed; background: #ede9fe; font-weight: 600; }
</style>

<a href="#skm-konten" class="skm-skip-link">Lewati ke konten survei</a>

<div class="skm-survei"
     x-data="surveiApp()"
     x-init="init()"
     :class="{
        'a11y-contrast': a11y.contrast,
        'a11y-readable': a11y.readableFont,
        'a11y-spacing': a11y.spacing,
        'a11y-linkhighlight': a11y.linkHighlight,
        'a11y-bigcursor': a11y.bigCursor,
        'a11y-reducemotion': a11y.reduceMotion
     }"
     :style="`zoom: ${a11y.fontScale}`">

    <div id="skm-konten" class="skm-card" tabindex="-1">

        @if (session('error'))
            <div class="skm-body pb-0"><div class="skm-alert skm-alert-danger" role="alert">{{ session('error') }}</div></div>
        @endif

        @if (!$periodeAktif)
            @include('survei.partials.header')
            <div class="skm-body">
                <div class="skm-alert skm-alert-warning" role="alert">
                    Survei sedang tidak dibuka saat ini. Silakan hubungi petugas {{ $puskesmas->nama }}.
                </div>
            </div>
        @elseif ($daftarPertanyaan->isEmpty())
            @include('survei.partials.header')
            <div class="skm-body">
                <div class="skm-alert skm-alert-warning" role="alert">
                    Kuesioner belum tersedia. Silakan coba lagi nanti.
                </div>
            </div>
        @else
            @include('survei.partials.header', ['showProgress' => true])

            <div class="skm-body">
                {{-- pengumuman langkah untuk pembaca layar --}}
                <div class="sr-only" aria-live="polite" x-text="announce"></div>

                <form method="POST" action="{{ route('survei.store', $puskesmas) }}" x-ref="form">
                    @csrf
                    <input type="hidden" name="mode_tampilan" :value="mode">

                    {{-- ============ MODE: DUA LANGKAH ============ --}}
                    <template x-if="mode === 'dua'">
                        <div>
                            <div class="skm-steps" aria-hidden="true">
                                <div class="skm-step" :class="step >= 1 ? 'is-active' : ''">
                                    <span class="skm-dot">1</span><span class="skm-label">Data Diri</span>
                                </div>
                                <div class="skm-track"></div>
                                <div class="skm-step" :class="step >= 2 ? 'is-active' : ''">
                                    <span class="skm-dot">2</span><span class="skm-label">Penilaian</span>
                                </div>
                            </div>

                            <div x-show="step === 1" data-step="1">
                                @include('survei.partials.data-diri')
                                <button type="button" class="skm-btn skm-primary w-100" @click="goNext(1)">
                                    Selanjutnya
                                </button>
                            </div>

                            <div x-show="step === 2" data-step="2" x-cloak>
                                <h5 tabindex="-1">Penilaian layanan</h5>
                                <p class="skm-hint mb-3">Skala 1 = sangat tidak baik, 4 = sangat baik.</p>
                                @include('survei.partials.semua-pertanyaan')
                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" class="skm-btn skm-secondary" @click="prev()">
                                        Kembali
                                    </button>
                                    <button type="submit" class="skm-btn skm-primary flex-fill">Kirim penilaian</button>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- ============ MODE: SATU PERTANYAAN PER LAYAR ============ --}}
                    <template x-if="mode === 'satu'">
                        <div>
                            <div x-show="step === 1" data-step="1">
                                @include('survei.partials.data-diri')
                                <button type="button" class="skm-btn skm-primary w-100" @click="goNext(1)">
                                    Lanjut
                                </button>
                            </div>

                            @foreach ($daftarPertanyaan as $i => $pertanyaan)
                                @php $stepN = $i + 2; @endphp
                                <div x-show="step === {{ $stepN }}" data-step="{{ $stepN }}" x-cloak>
                                    <div class="skm-question">
                                        <div class="skm-hint">Pertanyaan {{ $i + 1 }} dari {{ $daftarPertanyaan->count() }}</div>

                                        @if ($pertanyaan->header_image)
                                            <img src="{{ $pertanyaan->headerImageUrl() }}" alt="Gambar pertanyaan {{ $i + 1 }}" class="skm-question-image">
                                        @endif

                                        @if ($pertanyaan->tipe_input === 'teks')
                                            <h5 tabindex="-1" class="mt-2">{{ $pertanyaan->teks_pertanyaan }}</h5>
                                            <label class="sr-only" for="satu_teks_{{ $pertanyaan->id }}">{{ $pertanyaan->teks_pertanyaan }}</label>
                                            <textarea id="satu_teks_{{ $pertanyaan->id }}" name="jawaban[{{ $pertanyaan->id }}]" class="form-control mt-3" rows="3"
                                                      placeholder="Tulis masukan Anda di sini (opsional)">{{ old('jawaban.' . $pertanyaan->id) }}</textarea>
                                        @elseif ($pertanyaan->gaya_tampilan === 'dropdown')
                                            <h5 tabindex="-1" class="mt-2">{{ $pertanyaan->teks_pertanyaan }}</h5>
                                            <label class="sr-only" for="satu_dd_{{ $pertanyaan->id }}">{{ $pertanyaan->teks_pertanyaan }}</label>
                                            <select id="satu_dd_{{ $pertanyaan->id }}" name="jawaban[{{ $pertanyaan->id }}]" class="form-select mt-3" required>
                                                <option value="">-- Pilih --</option>
                                                @foreach ($pertanyaan->labelSkala() as $nilai => $label)
                                                    <option value="{{ $nilai }}" @selected(old('jawaban.' . $pertanyaan->id) == $nilai)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <fieldset>
                                                <legend tabindex="-1">{{ $pertanyaan->teks_pertanyaan }}</legend>
                                                <div class="skm-scale">
                                                    @foreach ($pertanyaan->labelSkala() as $nilai => $label)
                                                        <input type="radio" name="jawaban[{{ $pertanyaan->id }}]"
                                                               id="satu_p{{ $pertanyaan->id }}_{{ $nilai }}"
                                                               class="skm-scale-input" value="{{ $nilai }}"
                                                               @checked(old('jawaban.' . $pertanyaan->id) == $nilai)
                                                               @change="next()" required>
                                                        <label class="skm-scale-option" for="satu_p{{ $pertanyaan->id }}_{{ $nilai }}">
                                                            <span class="skm-scale-num" aria-hidden="true">{{ $nilai }}</span>
                                                            <span>{{ $label }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </fieldset>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-2 mt-4">
                                        <button type="button" class="skm-btn skm-secondary w-50" @click="prev()">Kembali</button>
                                        @if ($pertanyaan->tipe_input === 'teks' || $pertanyaan->gaya_tampilan === 'dropdown')
                                            <button type="button" class="skm-btn skm-primary w-50" @click="goNext({{ $stepN }})">Lanjut</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div x-show="step === totalStep" data-step="final" x-cloak class="text-center">
                                <h5 tabindex="-1">Selesai</h5>
                                <p class="skm-hint">Klik kirim untuk menyimpan jawaban</p>
                                <div class="d-flex gap-2 mt-4">
                                    <button type="button" class="skm-btn skm-secondary w-50" @click="prev()">Kembali</button>
                                    <button type="submit" class="skm-btn skm-primary w-50">Kirim Survei</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            </div>
        @endif
    </div>

    {{-- ============ TOMBOL & PANEL AKSESIBILITAS ============ --}}
    <button type="button" class="skm-a11y-fab"
            @click="panelOpen = !panelOpen"
            :aria-expanded="panelOpen.toString()"
            aria-controls="skm-a11y-panel"
            aria-label="Buka pengaturan aksesibilitas dan tampilan">
        <i class="bi bi-universal-access" aria-hidden="true"></i>
    </button>

    <div id="skm-a11y-panel" class="skm-a11y-panel" x-show="panelOpen" x-cloak
         role="dialog" aria-label="Pengaturan aksesibilitas dan tampilan"
         @keydown.escape.window="panelOpen = false">

        <h6>Mode tampilan</h6>
        <div class="skm-mode-choice" role="group" aria-label="Pilih mode tampilan survei">
            <button type="button" :aria-pressed="(mode === 'dua').toString()" @click="setMode('dua')">Dua langkah</button>
            <button type="button" :aria-pressed="(mode === 'satu').toString()" @click="setMode('satu')">Satu per layar</button>
        </div>
        <p class="skm-hint mb-3">Ganti tampilan mengulang isian dari awal.</p>

        <h6>Aksesibilitas</h6>

        <div class="skm-a11y-row">
            <span>Ukuran teks</span>
            <button type="button" class="skm-btn skm-secondary" style="padding:4px 12px;min-height:32px;" @click="cycleFontScale()">
                <span x-text="Math.round(a11y.fontScale * 100) + '%'"></span>
            </button>
        </div>

        <div class="skm-a11y-row">
            <span id="lbl-contrast">Kontras tinggi</span>
            <button type="button" class="skm-a11y-toggle" role="switch" aria-labelledby="lbl-contrast"
                    :aria-pressed="a11y.contrast.toString()" @click="a11y.contrast = !a11y.contrast"><span></span></button>
        </div>

        <div class="skm-a11y-row">
            <span id="lbl-font">Font mudah dibaca</span>
            <button type="button" class="skm-a11y-toggle" role="switch" aria-labelledby="lbl-font"
                    :aria-pressed="a11y.readableFont.toString()" @click="a11y.readableFont = !a11y.readableFont"><span></span></button>
        </div>

        <div class="skm-a11y-row">
            <span id="lbl-spacing">Jarak baris lebih lega</span>
            <button type="button" class="skm-a11y-toggle" role="switch" aria-labelledby="lbl-spacing"
                    :aria-pressed="a11y.spacing.toString()" @click="a11y.spacing = !a11y.spacing"><span></span></button>
        </div>

        <div class="skm-a11y-row">
            <span id="lbl-links">Sorot tombol & tautan</span>
            <button type="button" class="skm-a11y-toggle" role="switch" aria-labelledby="lbl-links"
                    :aria-pressed="a11y.linkHighlight.toString()" @click="a11y.linkHighlight = !a11y.linkHighlight"><span></span></button>
        </div>

        <div class="skm-a11y-row">
            <span id="lbl-cursor">Kursor lebih besar</span>
            <button type="button" class="skm-a11y-toggle" role="switch" aria-labelledby="lbl-cursor"
                    :aria-pressed="a11y.bigCursor.toString()" @click="a11y.bigCursor = !a11y.bigCursor"><span></span></button>
        </div>

        <div class="skm-a11y-row">
            <span id="lbl-motion">Kurangi animasi</span>
            <button type="button" class="skm-a11y-toggle" role="switch" aria-labelledby="lbl-motion"
                    :aria-pressed="a11y.reduceMotion.toString()" @click="a11y.reduceMotion = !a11y.reduceMotion"><span></span></button>
        </div>

        <div class="skm-a11y-row" style="border-bottom:none;">
            <span>Bacakan langkah ini</span>
            <button type="button" class="skm-btn skm-secondary" style="padding:4px 12px;min-height:32px;" @click="speakCurrentStep()">
                <i class="bi bi-volume-up" aria-hidden="true"></i> Dengarkan
            </button>
        </div>
    </div>
</div>
@endsection
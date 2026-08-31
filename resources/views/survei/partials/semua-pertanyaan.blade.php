@foreach ($daftarPertanyaan as $pertanyaan)
    <div class="skm-question" style="text-align:left;">
        @if ($pertanyaan->header_image)
            <img src="{{ $pertanyaan->headerImageUrl() }}" alt="Gambar pertanyaan {{ $loop->iteration }}" class="skm-question-image" style="margin-left:0;">
        @endif

        @if ($pertanyaan->tipe_input === 'teks')
            <label class="skm-question-label" for="dua_teks_{{ $pertanyaan->id }}">{{ $loop->iteration }}. {{ $pertanyaan->teks_pertanyaan }}</label>
            <textarea id="dua_teks_{{ $pertanyaan->id }}" name="jawaban[{{ $pertanyaan->id }}]" class="form-control mt-2" rows="3"
                      placeholder="Tulis masukan Anda di sini (opsional)">{{ old('jawaban.' . $pertanyaan->id) }}</textarea>
        @elseif ($pertanyaan->gaya_tampilan === 'dropdown')
            <label class="skm-question-label" for="dua_dd_{{ $pertanyaan->id }}">{{ $loop->iteration }}. {{ $pertanyaan->teks_pertanyaan }}</label>
            <select id="dua_dd_{{ $pertanyaan->id }}" name="jawaban[{{ $pertanyaan->id }}]" class="form-select mt-2" required>
                <option value="">-- Pilih --</option>
                @foreach ($pertanyaan->labelSkala() as $nilai => $label)
                    <option value="{{ $nilai }}" @selected(old('jawaban.' . $pertanyaan->id) == $nilai)>{{ $label }}</option>
                @endforeach
            </select>
        @else
            <fieldset>
                <legend class="skm-question-label">{{ $loop->iteration }}. {{ $pertanyaan->teks_pertanyaan }}</legend>
                <div class="skm-scale">
                    @foreach ($pertanyaan->labelSkala() as $nilai => $label)
                        <input type="radio" name="jawaban[{{ $pertanyaan->id }}]"
                               id="dua_p{{ $pertanyaan->id }}_{{ $nilai }}"
                               class="skm-scale-input" value="{{ $nilai }}"
                               @checked(old('jawaban.' . $pertanyaan->id) == $nilai)
                               required>
                        <label class="skm-scale-option" for="dua_p{{ $pertanyaan->id }}_{{ $nilai }}">
                            <span class="skm-scale-num" aria-hidden="true">{{ $nilai }}</span>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endif
    </div>
    @if (!$loop->last)
        <hr style="border-color: var(--purple-100); margin: 1.25rem 0;">
    @endif
@endforeach
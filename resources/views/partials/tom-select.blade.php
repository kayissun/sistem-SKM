{{-- Tom Select: ganti semua <select> native dengan dropdown bergaya ungu sistem --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* ====== Tom Select — tema ungu sistem SKM ====== */
    .ts-wrapper .ts-control {
        border: 1px solid #D4D0E8;
        border-radius: 10px;
        background: #FFFFFF;
        color: #180733;
        min-height: calc(1.5em + .5rem + 2px);
        padding: .25rem .75rem;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    select.form-select-sm + .ts-wrapper .ts-control,
    .ts-wrapper.form-select-sm .ts-control {
        padding: .125rem .5rem;
        border-radius: 8px;
    }
    .ts-wrapper .ts-control:hover { border-color: #A78BFA; }
    .ts-wrapper.focus .ts-control {
        border-color: #7C3AED !important;
        box-shadow: 0 0 0 .2rem rgba(124, 58, 237, .15) !important;
        outline: none;
    }
    .ts-wrapper .ts-control input {
        display: none !important;
    }
    .ts-wrapper .ts-control > .items > .item { color: #180733; }
    .ts-wrapper .ts-control::after {
        border-top-color: #6D28D9 !important; /* caret default ungu */
    }
    .ts-dropdown {
        border: 1px solid rgba(109, 40, 217, .12);
        border-radius: 12px;
        margin-top: .35rem;
        box-shadow: 0 12px 28px -12px rgba(46, 16, 101, .25);
        overflow: hidden;
        background: #FFFFFF;
        z-index: 1060;
    }
    .ts-dropdown .ts-dropdown-content {
        max-height: 280px;
        padding: .35rem;
    }
    .ts-dropdown [data-selectable].option {
        color: #180733;
        border-radius: 8px;
        padding: .5rem .75rem;
        transition: background-color .1s ease, color .1s ease;
    }
    .ts-dropdown [data-selectable].option:hover,
    .ts-dropdown .active {
        background: #EDE9FE !important;
        color: #6D28D9 !important;
    }
    .ts-dropdown .optgroup-header {
        background: #FAF8FF;
        color: #625B78;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: .5rem .75rem .25rem;
    }
    .ts-dropdown .no-results {
        color: #625B78;
        font-style: italic;
    }
    /* Placeholder & item disabled */
    .ts-wrapper .ts-control > .items > .placeholder {
        color: #625B78;
        opacity: .75;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.popular.min.js"></script>
<script>
    (function () {
        function initTomSelect(root) {
            (root || document).querySelectorAll('select:not(.ts-native):not([data-no-ts])').forEach(function (sel) {
                if (sel.tomselect) return;
                new TomSelect(sel, {
                    create: false,
                    maxOptions: null,
                    allowEmptyOption: true,
                    search: false,
                    placeholder: sel.getAttribute('placeholder') || undefined,
                    plugins: {},
                    onInitialize: function () {
                        sel.classList.add('ts-native');
                    }
                });
            });
        }
        window.initTomSelect = initTomSelect;
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { initTomSelect(); });
        } else {
            initTomSelect();
        }
        // Untuk konten dinamis (modal, form baru), panggil window.initTomSelect(container)
        document.addEventListener('shown.bs.modal', function (e) { initTomSelect(e.target); });
    })();
</script>

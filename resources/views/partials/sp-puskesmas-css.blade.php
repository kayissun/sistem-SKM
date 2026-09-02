<style>
    /* ===== Shared component styles for Puskesmas views (matches Dinkes theme) ===== */

    /* --- Page header --- */
    .sp-pagehead {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid rgba(24, 7, 51, .08);
    }
    .sp-pagehead .eyebrow {
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #A66A0E;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sp-pagehead .eyebrow::before {
        content: '';
        width: 18px;
        height: 3px;
        border-radius: 2px;
        background: linear-gradient(135deg, #7C3AED 0%, #5B21B6 55%, #2A0B5E 100%);
        display: inline-block;
    }
    .sp-pagehead h1 {
        font-weight: 800;
        font-size: 1.5rem;
        color: #180733;
        margin: 0;
        letter-spacing: -.01em;
    }
    .sp-pagehead .meta {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }
    .sp-pagehead .meta-item {
        font-size: .82rem;
        color: #625B78;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .sp-pagehead .meta-item i { color: #7C3AED; }

    /* --- Page sub-header (for index pages) --- */
    .sp-page-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
    .sp-page-head h3 { font-weight: 800; color: #180733; margin: 0; }
    .sp-page-head p { margin: 2px 0 0; color: #635C7A; font-size: .88rem; }

    /* --- Stat cards --- */
    .sp-stat-card {
        border: 1px solid rgba(24, 7, 51, .06);
        border-radius: 16px;
        transition: box-shadow .18s, transform .18s;
    }
    .sp-stat-card:hover {
        box-shadow: 0 10px 26px rgba(46, 16, 101, .10);
        transform: translateY(-2px);
    }
    .sp-stat-card .icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.05rem;
        margin-bottom: 14px;
    }
    .sp-stat-card .label {
        font-size: .78rem;
        color: #625B78;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .sp-stat-card .value { font-weight: 800; color: #180733; margin: 4px 0 10px; }
    .sp-stat-card a {
        font-size: .83rem; font-weight: 700; color: #6D28D9;
        text-decoration: none;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .sp-stat-card a:hover { color: #2E1065; gap: 8px; }
    .sp-stat-card a i { transition: margin .15s; }

    /* --- Section card (quick access, etc) --- */
    .sp-section-card {
        border: 1px solid rgba(24, 7, 51, .06);
        border-radius: 16px;
        overflow: hidden;
    }
    .sp-section-card .card-header {
        background: #FAF8FF;
        border-bottom: 1px solid rgba(24, 7, 51, .06);
        font-weight: 800;
        color: #180733;
        font-size: .95rem;
        padding: 14px 20px;
    }

    /* --- Quick access links --- */
    .sp-quick a {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1px solid rgba(109, 40, 217, .10);
        background: #fff;
        font-weight: 600; font-size: .88rem; color: #14102B;
        text-decoration: none;
        transition: .15s;
    }
    .sp-quick a:hover { background: #FAF8FF; border-color: rgba(109, 40, 217, .25); }
    .sp-quick a i {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: #EDE9FE; color: #6D28D9;
        display: flex; align-items: center; justify-content: center;
        font-size: .9rem; flex-shrink: 0;
    }

    /* --- Filter card --- */
    .sp-filter-card { border-radius: 14px; }
    .sp-filter-card .input-group-text { background: #fff; border-right: none; color: #9CA3AF; }
    .sp-filter-card .form-control, .sp-filter-card .form-select { border-left: none; }
    .sp-filter-card .input-group:focus-within .input-group-text,
    .sp-filter-card .input-group:focus-within .form-control { border-color: #A78BFA; }

    /* --- Table card --- */
    .sp-table-card { border-radius: 14px; overflow: hidden; border: 1px solid #E4DEF7; }
    .sp-table-card .table-responsive { scrollbar-width: thin; scrollbar-color: #C4B5FD transparent; }
    .sp-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
    .sp-table-card .table-responsive::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 99px; }
    .sp-table-card .table-responsive::-webkit-scrollbar-track { background: transparent; }
    .sp-table-card .table { margin-bottom: 0; font-size: .82rem; }
    .sp-table-card tbody tr:hover { background: #FAF8FF; }
    .sp-table-card td, .sp-table-card th { vertical-align: middle; padding: .65rem .85rem; border-bottom: 1px solid #E4DEF7; border-right: 1px solid #E4DEF7; }
    .sp-table-card td:last-child, .sp-table-card th:last-child { border-right: none; }
    .sp-table-card thead th { font-size: .72rem; border-bottom: 2px solid #E4DEF7; background: #FAF8FF; }
    .sp-table-card tbody tr:last-child td { border-bottom: none; }

    /* --- Badge status (sesuai aturan warna putih-ungu-emas) --- */
    .badge-status-active   { background: #FCF1DC; color: #A66A0E; border: 1px solid #F0DFB2; font-weight: 600; padding: .4em .75em; border-radius: 99px; }
    .badge-status-inactive { background: #F3F1FA; color: #6B6480; border: 1px solid #E4DEF7; font-weight: 600; padding: .4em .75em; border-radius: 99px; }
    .badge-tl { font-weight: 600; padding: .35em .7em; border-radius: 99px; font-size: .75rem; }

    /* --- Icon button --- */
    .sp-icon-btn {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; border: 1px solid rgba(109,40,217,.15);
        background: #fff; color: #6D28D9;
        transition: .15s;
        text-decoration: none;
    }
    .sp-icon-btn:hover { background: #6D28D9; color: #fff; border-color: #6D28D9; }

    /* --- Bulk action bar --- */
    .sp-bulkbar {
        display: none;
        align-items: center;
        gap: 12px;
        background: #FAF8FF;
        border: 1px solid #E4DEF7;
        border-radius: 12px;
        padding: 10px 16px;
        margin-bottom: 14px;
    }

    /* --- Modal --- */
    .sp-modal .modal-content { border: none; border-radius: 18px; overflow: hidden; }
    .sp-modal .modal-header {
        background: linear-gradient(135deg,#7C3AED,#2A0B5E);
        color: #fff;
        border: none;
        padding: 20px 26px;
    }
    .sp-modal .modal-header .modal-title { font-weight: 800; font-size: 1.05rem; }
    .sp-modal .modal-header .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    .sp-modal .modal-body { padding: 26px; }
    .sp-modal .modal-footer { border: none; padding: 16px 26px 24px; }

    /* --- Section label (for forms) --- */
    .sp-section-label {
        font-size: .72rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
        color: #6D28D9; margin-bottom: 12px;
    }

    /* --- Pagination --- */
    .sp-pagination { display: flex; justify-content: flex-end; }
    .sp-pagination nav { margin: 0; }
    .sp-pagination .pagination { gap: 4px; margin: 0; }
    .sp-pagination .page-link {
        border: 1px solid rgba(109,40,217,.12);
        color: #180733;
        border-radius: 8px !important;
        font-size: .82rem;
        font-weight: 600;
        padding: .4rem .7rem;
    }
    .sp-pagination .page-link:hover { background: #F3EEFF; color: #2E1065; }
    .sp-pagination .page-item.active .page-link {
        background: linear-gradient(135deg,#7C3AED,#2A0B5E);
        border-color: transparent;
        color: #fff;
    }
    .sp-pagination .page-item.disabled .page-link { color: #C4BFD6; background: #fff; border-color: rgba(109,40,217,.08); }

    /* --- Empty state --- */
    .sp-empty-state {
        text-align: center;
        padding: 46px 20px;
        color: #625B78;
    }
    .sp-empty-state i {
        font-size: 1.8rem;
        color: #8B5CF6;
        margin-bottom: 10px;
        display: block;
    }

    /* --- Cluster badge --- */
    .sp-badge-cluster {
        display: inline-block;
        padding: 3px 11px;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 700;
    }
    .sp-badge-cluster.c-tinggi { background: #DCFCE7; color: #15803D; }
    .sp-badge-cluster.c-sedang { background: #FCF1DC; color: #A66A0E; }
    .sp-badge-cluster.c-rendah { background: #FEE2E2; color: #B91C1C; }

    /* --- Rekomendasi box --- */
    .rekomendasi-box { background: linear-gradient(135deg,#FFF9EA,#FFFDF5); border-left: 4px solid #C88719; border-radius: 8px; padding: 12px 16px; }

    /* --- Unsur info card --- */
    .unsur-info-card {
        background: linear-gradient(135deg,#FAF8FF,#F3EEFF);
        border: 1px solid rgba(109,40,217,.12);
        border-radius: 12px;
        padding: 16px;
    }

    /* --- Upload zone --- */
    .upload-zone {
        border: 2px dashed #C4B5FD;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        background: #FAF8FF;
        transition: .2s;
        cursor: pointer;
    }
    .upload-zone:hover { border-color: #7C3AED; background: #F3EEFF; }
    .upload-zone.has-files { border-color: #10B981; background: #ECFDF5; }
    .upload-zone i { font-size: 2rem; color: #7C3AED; }
    .preview-img {
        width: 80px; height: 80px; object-fit: cover;
        border-radius: 10px; border: 2px solid #E4DEF7;
    }

    /* --- Form card (for create/edit forms) --- */
    .sp-form-card {
        border: 1px solid rgba(24, 7, 51, .06);
        border-radius: 16px;
        overflow: hidden;
    }
    .sp-form-card .card-header {
        background: #FAF8FF;
        border-bottom: 1px solid rgba(24, 7, 51, .06);
        font-weight: 800;
        color: #180733;
        font-size: .95rem;
        padding: 14px 20px;
    }
    .sp-form-card .card-body { padding: 24px; }

    /* --- Form switches --- */
    .form-switch .form-check-input:checked { background-color: #6D28D9; border-color: #6D28D9; }

    /* --- Form select / dropdown: purple theme --- */
    .form-select {
        border-color: #D4D0E8;
        color: #180733;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236D28D9' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right .75rem center;
        background-size: 16px 12px;
    }
    .form-select:hover { border-color: #A78BFA; }
    .form-select:focus {
        border-color: #7C3AED !important;
        box-shadow: 0 0 0 .2rem rgba(124,58,237,.15) !important;
        outline: none;
    }

    /* --- Form control: purple focus --- */
    .form-control:hover { border-color: #A78BFA; }
    .form-control:focus {
        border-color: #7C3AED !important;
        box-shadow: 0 0 0 .2rem rgba(124,58,237,.15) !important;
        outline: none;
    }

    /* --- Rekomendasi box --- */
    .rekomendasi-box { background: linear-gradient(135deg,#FFF9EA,#FFFDF5); border-left: 4px solid #C88719; border-radius: 8px; padding: 12px 16px; }

    /* --- Export buttons (PDF ungu, Excel emas) --- */
    .sp-btn-pdf {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: .8rem; font-weight: 600; padding: 8px 14px;
        border-radius: 10px; border: 1px solid rgba(109,40,217,.2);
        background: #fff; color: #6D28D9; text-decoration: none; transition: .15s;
    }
    .sp-btn-pdf:hover { background: #6D28D9; border-color: #6D28D9; color: #fff; }
    .sp-btn-excel {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: .8rem; font-weight: 600; padding: 8px 14px;
        border-radius: 10px; border: 1px solid #F0DFB2;
        background: #FCF1DC; color: #A66A0E; text-decoration: none; transition: .15s;
    }
    .sp-btn-excel:hover { background: #C88719; border-color: #C88719; color: #fff; }

    /* --- Badge chip (unsur / tipe, sesuai palette) --- */
    .sp-badge-chip-light {
        display: inline-block; font-size: .7rem; font-weight: 600;
        background: #EDE9FE; color: #6D28D9;
        border: 1px solid #DDD6FE; border-radius: 8px; padding: 4px 10px;
    }
    .sp-badge-chip-gold {
        display: inline-block; font-size: .7rem; font-weight: 600;
        background: #FCF1DC; color: #A66A0E;
        border: 1px solid #F0DFB2; border-radius: 8px; padding: 4px 10px;
    }
    .sp-badge-chip-muted {
        display: inline-block; font-size: .7rem; font-weight: 600;
        background: #F3F1FA; color: #6B6480;
        border: 1px solid #E4DEF7; border-radius: 8px; padding: 4px 10px;
    }
</style>

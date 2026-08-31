<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SKM — Survei Kepuasan Masyarakat</title>
  <meta name="description" content="Sistem digital Survei Kepuasan Masyarakat untuk Dinas Kesehatan dan jaringan Fasilitas Kesehatan Kabupaten Purworejo — kuesioner 9 unsur, isi via QR code, IKM real-time.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fraunces:ital,wght@0,600;1,500;1,600&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">

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
 
      /* 10% — accent (used sparingly, never as a background field) */
      --gold-700: #A66A0E;
      --gold-600: #C88719;
      --gold-400: #E4A63B;
      --gold-100: #FCF1DC;
 
      --gradient-primary: linear-gradient(135deg, #7C3AED 0%, #5B21B6 55%, #2A0B5E 100%);
      --gradient-hero-bg: radial-gradient(120% 100% at 50% 0%, #EEE9FF 0%, #FAF8FF 55%, #F5F1FF 100%);
      --gradient-card: linear-gradient(180deg, rgba(255,255,255,.94) 0%, rgba(255,255,255,.72) 100%);
      --gradient-gold: linear-gradient(135deg, #E4A63B 0%, #C88719 100%);
 
      --pattern-purple: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='72'%20height='72'%3E%3Cg%20fill='none'%20stroke='%236D28D9'%20stroke-opacity='0.10'%20stroke-width='1.1'%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(45%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(90%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(135%2036%2036)'/%3E%3Ccircle%20cx='36'%20cy='36'%20r='3.5'/%3E%3C/g%3E%3C/svg%3E");
      --pattern-gold: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='72'%20height='72'%3E%3Cg%20fill='none'%20stroke='%23E4A63B'%20stroke-opacity='0.16'%20stroke-width='1.1'%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(45%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(90%2036%2036)'/%3E%3Cellipse%20cx='36'%20cy='36'%20rx='15'%20ry='25'%20transform='rotate(135%2036%2036)'/%3E%3Ccircle%20cx='36'%20cy='36'%20r='3.5'/%3E%3C/g%3E%3C/svg%3E");
 
      --radius-lg: 24px;
      --radius-md: 16px;
      --shadow-sm: 0 4px 6px -1px rgba(24,7,51,.05);
      --shadow-md: 0 12px 24px -8px rgba(46,16,101,.18);
      --shadow-lg: 0 24px 40px -10px rgba(46,16,101,.22), 0 8px 12px -6px rgba(46,16,101,.08);
    }
 
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
 
    body {
      margin: 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: var(--ink);
      background: var(--surface-0);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }
 
    a { text-decoration: none; color: inherit; }
    img, svg { display: block; max-width: 100%; }
 
    .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
 
    em.stamp { font-family: 'Fraunces', serif; font-style: italic; font-weight: 600; }
 
    .mono { font-family: 'JetBrains Mono', monospace; }
 
    /* ---------- Announcement bar (10% aksen) ---------- */
    .announce {
      background: var(--purple-900);
      color: rgba(255,255,255,.92);
      font-size: .82rem;
      font-weight: 600;
    }
    .announce .container {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 9px 24px;
      text-align: center;
      flex-wrap: wrap;
    }
    .announce .dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: var(--gold-400);
      flex-shrink: 0;
      box-shadow: 0 0 0 3px rgba(228,166,59,.22);
    }
    .announce a { color: var(--gold-400); font-weight: 700; }
    .announce a:hover { text-decoration: underline; }

    /* Warna bar disamakan dengan identitas ungu SKM */
    #nprogress .bar {
      background: linear-gradient(90deg, #7C3AED, #C88719) !important;
      height: 3px !important;
    }
    #nprogress .peg {
      box-shadow: 0 0 10px #7C3AED, 0 0 5px #C88719 !important;
    }
    /* sembunyikan spinner bawaan pojok kanan atas, cukup bar saja */
    #nprogress .spinner { display: none; }
 
    /* ---------- Navbar ---------- */
    .navbar {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(250,248,255,.9);
      backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(46,16,101,.08);
      transition: box-shadow .25s ease, border-color .25s ease;
    }
    .navbar.is-scrolled {
      box-shadow: 0 8px 24px -12px rgba(24,7,51,.18);
      border-bottom-color: rgba(46,16,101,.14);
    }
    .navbar-inner {
      display: flex; align-items: center; justify-content: space-between;
      gap: 16px; padding: 14px 24px;
    }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand img { width: 40px; height: auto; border-radius: 10px; }
    .brand-text { display: flex; flex-direction: column; line-height: 1.2; }
    .brand-text strong { font-weight: 800; font-size: 1.05rem; color: var(--purple-900); letter-spacing: .01em; }
    .brand-text span { font-size: .72rem; color: var(--ink-muted); font-weight: 600; letter-spacing: .02em; }
 
    .nav-links { display: flex; align-items: center; gap: 4px; font-weight: 600; font-size: .92rem; color: var(--ink-muted); }
    .nav-links a { padding: 8px 14px; border-radius: 99px; transition: color .2s, background .2s; }
    .nav-links a:hover { color: var(--purple-700); background: var(--purple-100); }
 
    /* ---------- Buttons ---------- */
    .btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 10px;
      font-weight: 700; font-size: .94rem; border-radius: 99px; padding: 13px 26px;
      transition: all .25s ease; cursor: pointer; border: 2px solid transparent; white-space: nowrap;
    }
    .btn i { font-size: .85em; }
    .btn-primary { background: var(--gradient-primary); color: #fff; box-shadow: 0 10px 20px -6px rgba(46,16,101,.45); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 16px 26px -6px rgba(46,16,101,.55); }
    .btn-outline { border-color: rgba(109,40,217,.22); color: var(--purple-700); background: var(--surface-0); box-shadow: var(--shadow-sm); }
    .btn-outline:hover { border-color: var(--purple-700); background: var(--purple-100); transform: translateY(-2px); }
    .btn-sm { padding: 9px 20px; font-size: .86rem; }
 
    /* ---------- Hero ---------- */
    .hero {
      position: relative;
      padding: 44px 0 24px;
      background: var(--gradient-hero-bg);
      border-bottom: 1px solid rgba(109,40,217,.06);
      overflow: hidden;
    }
    .hero::before {
      content: ''; position: absolute; inset: 0;
      background-image: var(--pattern-purple); background-size: 72px 72px; opacity: .7;
      pointer-events: none;
      mask-image: radial-gradient(70% 70% at 78% 30%, #000 0%, transparent 75%);
    }
    .hero .container { width: 100%; position: relative; z-index: 1; }
    .hero-grid { display: grid; grid-template-columns: 1.05fr .95fr; gap: 40px; align-items: center; }
 
    .eyebrow {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--surface-0); color: var(--purple-700);
      font-size: .78rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
      padding: 7px 16px 7px 12px; border-radius: 99px; margin-bottom: 18px;
      border: 1px solid rgba(109,40,217,.15); box-shadow: var(--shadow-sm);
    }
    .eyebrow i { width: 18px; height: 18px; border-radius: 50%; background: var(--gradient-gold); color: #fff; display: flex; align-items: center; justify-content: center; font-size: .62rem; }
 
    .hero h1 { font-size: clamp(1.9rem, 3.1vw, 2.85rem); font-weight: 800; line-height: 1.18; letter-spacing: -.02em; margin: 0 0 16px; color: var(--purple-900); }
    .hero h1 .accent { font: inherit; color: inherit; }
    .hero p.lead { font-size: 1.02rem; color: var(--ink-muted); max-width: 500px; margin: 0 0 26px; font-weight: 400; }
    .hero-cta { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 22px; }
 
    .hero-badges { display: flex; flex-wrap: wrap; gap: 10px; }
    .law-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--surface-0); border: 1px solid rgba(109,40,217,.14);
      border-radius: 10px; padding: 8px 12px 8px 10px; font-size: .78rem; font-weight: 600; color: var(--purple-800);
      box-shadow: var(--shadow-sm);
    }
    .law-badge i { color: var(--purple-600); font-size: .85em; }
    .law-badge .accent-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--gold-500,#C88719); }
 
    .trust-row {
      display: flex; align-items: center; gap: 18px; flex-wrap: wrap;
      margin-top: 22px; padding-top: 18px; border-top: 1px dashed rgba(109,40,217,.18);
      font-size: .8rem; color: var(--ink-muted); font-weight: 500;
    }
    .trust-row span { display: inline-flex; align-items: center; gap: 7px; }
    .trust-row i { color: var(--purple-700); }
 
    .hero-illustration { position: relative; width: 100%; max-width: 420px; margin: 0 auto; display: flex; justify-content: center; align-items: center; }
    .hero-illustration img {
      width: 100%; height: auto; display: block;
      filter: drop-shadow(0 20px 30px rgba(46,16,101,.16));
      animation: float-y 6.5s ease-in-out infinite;
    }
    @keyframes float-y {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-14px); }
    }
 
    /* ---------- Stat strip ---------- */
    .stat-strip { padding: 26px 0; border-bottom: 1px solid rgba(109,40,217,.08); background: var(--surface-0); }
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); }
    .stat-item {
      display: flex; align-items: center; gap: 14px; justify-content: center; text-align: left;
      padding: 8px 18px; border-left: 1px solid rgba(109,40,217,.1);
    }
    .stat-item:first-child { border-left: none; }
    .stat-item i {
      width: 40px; height: 40px; flex-shrink: 0; border-radius: 12px;
      background: var(--purple-100); color: var(--purple-700);
      display: flex; align-items: center; justify-content: center; font-size: 1rem;
    }
    .stat-item strong { display: block; font-size: .95rem; font-weight: 700; color: var(--purple-900); }
    .stat-item small { display: block; font-size: .78rem; color: var(--ink-muted); font-weight: 500; }
 
    /* ---------- Section shared ---------- */
    section { padding: 88px 0; }
    .section-alt { background: var(--surface-1); }
    .section-head { max-width: 620px; margin: 0 auto 52px; text-align: center; }
    .section-head.left { margin: 0 0 40px; text-align: left; }
    .section-head .eyebrow { margin-bottom: 16px; }
    .section-head h2 { font-size: clamp(1.8rem, 2.8vw, 2.25rem); font-weight: 800; margin: 0 0 14px; color: var(--purple-900); letter-spacing: -.01em; }
    .section-head p { color: var(--ink-muted); margin: 0; font-size: 1.03rem; }
 
    /* ---------- Features ---------- */
    .features-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
    .feature-card {
      background: var(--gradient-card); backdrop-filter: blur(8px);
      border-radius: var(--radius-md); padding: 30px 24px;
      border: 1px solid rgba(255,255,255,.9); box-shadow: var(--shadow-sm);
      transition: all .25s ease;
    }
    .feature-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-md); border-color: rgba(109,40,217,.2); }
    .feature-icon {
      width: 50px; height: 50px; border-radius: 14px; background: var(--gradient-primary);
      display: flex; align-items: center; justify-content: center; margin-bottom: 20px;
      box-shadow: 0 8px 16px -4px rgba(46,16,101,.35); color: #fff; font-size: 1.15rem;
    }
    .feature-card h3 { font-size: 1.08rem; font-weight: 700; margin: 0 0 10px; color: var(--purple-900); }
    .feature-card p { font-size: .92rem; color: var(--ink-muted); margin: 0; line-height: 1.6; }
 
    /* ---------- Kenapa Penting (image + checklist) ---------- */
    .why-grid { display: grid; grid-template-columns: .85fr 1.15fr; gap: 56px; align-items: center; }
    .why-media { position: relative; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); aspect-ratio: 4/5; }
    .why-media img { width: 100%; height: 100%; object-fit: cover; }
    .why-media::after {
      content: ''; position: absolute; inset: 0;
      background: linear-gradient(0deg, rgba(24,7,51,.55) 0%, transparent 45%);
    }
    .why-media .tag {
      position: absolute; left: 20px; bottom: 20px; z-index: 1;
      color: #fff; font-weight: 700; font-size: .95rem;
      display: flex; align-items: center; gap: 8px;
    }
    .why-media .tag i { color: var(--gold-400); }
 
    .why-list { display: flex; flex-direction: column; gap: 22px; }
    .why-row { display: flex; gap: 16px; }
    .why-row .num {
      flex-shrink: 0; width: 38px; height: 38px; border-radius: 10px;
      background: var(--purple-100); color: var(--purple-700);
      display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: .95rem;
    }
    .why-row h3 { margin: 0 0 4px; font-size: 1.03rem; font-weight: 700; color: var(--purple-900); }
    .why-row p { margin: 0; font-size: .92rem; color: var(--ink-muted); }
 
    /* ---------- How it works ---------- */
    .how-wrap { background: var(--surface-0); border-radius: 32px; padding: 48px 32px; box-shadow: var(--shadow-sm); border: 1px solid rgba(109,40,217,.06); }
 
    .how-grid { display: grid; gap: 40px; position: relative; }
    .how-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
    .how-grid::before {
      content: ''; position: absolute; top: 30px; left: calc(100% / 6); right: calc(100% / 6);
      height: 0; border-top: 2px dashed rgba(109,40,217,.25); z-index: 0;
    }
    .how-step { text-align: center; padding: 16px; position: relative; z-index: 1; }
    .how-step .num {
      width: 60px; height: 60px; border-radius: 50%; background: var(--gradient-primary); color: #fff;
      display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin: 0 auto 22px;
      box-shadow: 0 10px 20px -5px rgba(46,16,101,.4); border: 4px solid var(--surface-0);
    }
    .how-step h3 { font-size: 1.02rem; font-weight: 700; margin: 0 0 8px; color: var(--purple-900); }
    .how-step p { font-size: .88rem; color: var(--ink-muted); margin: 0; }
 
    /* ---------- Landasan Hukum ---------- */
    .law-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
    .law-card {
      background: var(--surface-0); border: 1px solid rgba(109,40,217,.1); border-radius: var(--radius-md);
      padding: 28px 24px; box-shadow: var(--shadow-sm); position: relative; overflow: hidden;
    }
    .law-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--gradient-gold); }
    .law-card .kicker { font-size: .74rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--gold-700); margin-bottom: 10px; }
    .law-card h3 { font-size: 1.05rem; font-weight: 800; color: var(--purple-900); margin: 0 0 10px; }
    .law-card p { font-size: .88rem; color: var(--ink-muted); margin: 0 0 14px; }
    .law-card .status { font-size: .78rem; font-weight: 600; color: var(--purple-700); display: flex; align-items: center; gap: 6px; }
    .law-card .status i { font-size: .7rem; color: var(--gold-600); }
 
    /* ---------- CTA Banner ---------- */
    .cta-banner {
      position: relative; background: var(--gradient-primary); border-radius: var(--radius-lg);
      padding: 60px 52px; display: flex; align-items: center; justify-content: space-between;
      gap: 32px; flex-wrap: wrap; color: #fff; box-shadow: var(--shadow-lg); overflow: hidden;
    }
    .cta-banner::before { content: ''; position: absolute; inset: 0; background-image: var(--pattern-gold); background-size: 72px 72px; mask-image: radial-gradient(60% 100% at 100% 0%, #000 0%, transparent 70%); }
    .cta-banner > * { position: relative; z-index: 1; }
    .cta-banner h2 { font-size: 1.75rem; font-weight: 800; margin: 0 0 12px; }
    .cta-banner p { color: rgba(255,255,255,.82); margin: 0; max-width: 460px; font-size: 1.02rem; }
    .cta-banner .btn-primary { background: #fff; color: var(--purple-700); box-shadow: 0 10px 20px rgba(0,0,0,.15); }
    .cta-banner .btn-primary:hover { background: var(--surface-1); color: var(--purple-800); }
 
    /* ---------- FAQ ---------- */
    .faq-list { max-width: 780px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
    .faq-item { background: var(--surface-0); border: 1px solid rgba(109,40,217,.1); border-radius: 14px; overflow: hidden; }
    .faq-item summary {
      cursor: pointer; list-style: none; padding: 18px 22px; font-weight: 700; color: var(--purple-900);
      display: flex; align-items: center; justify-content: space-between; gap: 12px; font-size: .96rem;
    }
    .faq-item summary::-webkit-details-marker { display: none; }
    .faq-item summary .icon {
      width: 28px; height: 28px; border-radius: 8px; background: var(--purple-100); color: var(--purple-700);
      display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: transform .2s ease;
    }
    .faq-item[open] summary .icon { transform: rotate(45deg); background: var(--gradient-gold); color: #fff; }
    .faq-item .faq-body { padding: 0 22px 20px; color: var(--ink-muted); font-size: .9rem; line-height: 1.7; }
 
    /* ---------- Footer ---------- */
    footer { background: var(--purple-900); color: rgba(255,255,255,.62); padding: 52px 0 28px; position: relative; overflow: hidden; }
    footer::before { content: ''; position: absolute; inset: 0; background-image: var(--pattern-gold); background-size: 72px 72px; opacity: .5; }
    .footer-inner { position: relative; z-index: 1; display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 32px; padding-bottom: 32px; border-bottom: 1px solid rgba(255,255,255,.1); }
    .footer-brand .brand-text strong { color: #fff; }
    .footer-brand .brand-text span { color: rgba(255,255,255,.5); }
    .footer-brand p { font-size: .88rem; max-width: 320px; margin: 14px 0 0; line-height: 1.7; }
    .footer-col h4 { color: #fff; font-size: .85rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; margin: 0 0 16px; }
    .footer-col ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 10px; }
    .footer-col a, .footer-col li { font-size: .9rem; display: flex; align-items: center; gap: 10px; }
    .footer-col a:hover { color: #fff; }
    .footer-col i { width: 16px; color: var(--gold-600); text-align: center; }
    .footer-bottom { position: relative; z-index: 1; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding-top: 24px; font-size: .82rem; }
 
    /* ---------- Responsive ---------- */
    @media (max-width: 980px) {
      .why-grid { grid-template-columns: 1fr; }
      .why-media { aspect-ratio: 16/9; }
    }
    @media (max-width: 900px) {
      .hero-grid { grid-template-columns: 1fr; gap: 24px; }
      .hero-illustration { order: 1; max-width: 260px; margin: 0 auto; }
      .hero-text { order: 2; text-align: center; }
      .hero p.lead { margin-left: auto; margin-right: auto; }
      .hero-cta, .hero-badges, .trust-row { justify-content: center; }
      .stat-grid { grid-template-columns: repeat(2, 1fr); }
      .stat-item { border-left: none; border-top: 1px solid rgba(109,40,217,.1); padding-top: 18px; }
      .stat-item:nth-child(1), .stat-item:nth-child(2) { border-top: none; padding-top: 8px; }
      .features-grid { grid-template-columns: repeat(2, 1fr); }
      .law-grid { grid-template-columns: 1fr; }
      .how-grid.cols-3, .how-grid.cols-4 { grid-template-columns: 1fr; gap: 32px; }
      .how-grid::before { display: none; }
      .nav-links { display: none; }
      .footer-inner { grid-template-columns: 1fr; }
    }
    @media (max-width: 560px) {
      .features-grid { grid-template-columns: 1fr; }
      .stat-grid { grid-template-columns: 1fr; }
      .stat-item { justify-content: flex-start; border: none !important; padding-top: 12px; }
      .cta-banner { padding: 40px 28px; flex-direction: column; text-align: center; }
      .footer-bottom { flex-direction: column; text-align: center; }
    }
    @media (prefers-reduced-motion: reduce) {
      html { scroll-behavior: auto; }
      .feature-card, .btn, .navbar { transition: none; }
      .hero-text, .hero-illustration { animation: none; }
      .hero-illustration img { animation: none; }
    }
  </style>
</head>
<body>

  <div class="announce">
    <div class="container">
      <span class="dot"></span>
      Formulir SKM 2026 kini aktif untuk 9 unsur pelayanan terbaru —
      <a href="#cara-kerja">lihat cara mengisi</a>
    </div>
  </div>

  <nav class="navbar" id="navbar">
    <div class="container navbar-inner">
      <a href="/" class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Purworejo">
        <span class="brand-text">
          <strong>SKM</strong>
          <span>Dinkesda Kab. Purworejo</span>
        </span>
      </a>
      <div class="nav-links">
        <a href="#fitur">Fitur Utama</a>
        <a href="#kenapa">Kenapa SKM</a>
        <a href="#cara-kerja">Cara Kerja</a>
        <a href="#landasan">Landasan Hukum</a>
        <a href="#faq">FAQ</a>
      </div>
      <a href="{{ route('login') }}" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-right-to-bracket"></i>
        Login
      </a>
    </div>
  </nav>

  <header class="hero">
    <div class="container hero-grid">
      <div class="hero-text" data-aos="fade-right" data-aos-duration="700">
        <span class="eyebrow">
          <i class="fa-solid fa-landmark"></i>
          Sistem Informasi
        </span>
        <h1>Survei Kepuasan Masyarakat <em class="accent">Dinas Kesehatan Daerah</em> Kabupaten Purworejo</h1>
        <p class="lead">
          Susun kuesioner presisi untuk tiap unit, kumpulkan masukan warga melalui QR Code, dan pantau Indeks Kepuasan Masyarakat secara real-time — satu sistem untuk seluruh Fasilitas Kesehatan.
        </p>
        <div class="hero-cta">
          <a href="{{ route('login') }}" class="btn btn-primary">
            Masuk Sekarang
            <i class="fa-solid fa-arrow-right"></i>
          </a>
          <a href="#cara-kerja" class="btn btn-outline">Lihat Cara Kerja</a>
        </div>
        <div class="hero-badges">
          <span class="law-badge"><i class="fa-solid fa-scale-balanced"></i> Permenpan RB No. 14/2017</span>
          <span class="law-badge"><i class="fa-solid fa-list-check"></i> 9 Unsur Pelayanan</span>
          <span class="law-badge"><i class="fa-solid fa-building-shield"></i> Dinkesda Purworejo</span>
        </div>
        <div class="trust-row">
          <span><i class="fa-solid fa-lock"></i> HTTPS Terenkripsi SSL/TLS</span>
          <span><i class="fa-solid fa-circle-check"></i> Anonim &amp; rahasia bagi responden</span>
        </div>
      </div>

      <div class="hero-illustration" data-aos="fade-left" data-aos-duration="800" data-aos-delay="120">
        <img src="{{ asset('images/ilustrasi.svg') }}" alt="Ilustrasi survei kepuasan masyarakat">
      </div>
    </div>
  </header>

  <div class="stat-strip">
    <div class="container stat-grid">
      <div class="stat-item" data-aos="fade-up" data-aos-duration="500">
        <i class="fa-solid fa-building-columns"></i>
        <div><strong>1 Dinas Kesehatan</strong><small>Mengawasi seluruh unit layanan</small></div>
      </div>
      <div class="stat-item" data-aos="fade-up" data-aos-duration="500" data-aos-delay="80">
        <i class="fa-solid fa-hospital"></i>
        <div><strong>Fasilitas Kesehatan</strong><small>Terhubung dalam satu jaringan</small></div>
      </div>
      <div class="stat-item" data-aos="fade-up" data-aos-duration="500" data-aos-delay="160">
        <i class="fa-solid fa-list-check"></i>
        <div><strong>9 Unsur Pelayanan</strong><small>Mengacu Permenpan RB No. 14/2017</small></div>
      </div>
      <div class="stat-item" data-aos="fade-up" data-aos-duration="500" data-aos-delay="240">
        <i class="fa-solid fa-bolt"></i>
        <div><strong>Real-time</strong><small>Nilai IKM terhitung otomatis</small></div>
      </div>
    </div>
  </div>

  <section id="fitur">
    <div class="container">
      <div class="section-head" data-aos="fade-up">
        <span class="eyebrow"><i class="fa-solid fa-star"></i> Fitur Unggulan</span>
        <h2>Semua kebutuhan pengelolaan SKM dalam satu platform</h2>
        <p>Sistem dirancang modern untuk mempermudah unit kesehatan hingga dinas kesehatan dalam menyusun dan merekap survei.</p>
      </div>
      <div class="features-grid">
        <div class="feature-card" data-aos="fade-up" data-aos-duration="500">
          <div class="feature-icon"><i class="fa-solid fa-list-check"></i></div>
          <h3>Kuesioner Fleksibel</h3>
          <p>Atur kuesioner mandiri untuk tiap unit layanan — radio button, rating, atau saran bebas — terhubung ke 9 unsur resmi.</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
          <div class="feature-icon"><i class="fa-solid fa-qrcode"></i></div>
          <h3>Pindai QR Code</h3>
          <p>Responden cukup memindai QR Code yang ditempel pada loket atau meja pelayanan tanpa unduh aplikasi.</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
          <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
          <h3>Laporan Real-time</h3>
          <p>Indeks Kepuasan Masyarakat (IKM) terhitung otomatis dan siap diekspor dalam format dokumen PDF atau Excel.</p>
        </div>
        <div class="feature-card" data-aos="fade-up" data-aos-duration="500" data-aos-delay="300">
          <div class="feature-icon"><i class="fa-solid fa-users-gear"></i></div>
          <h3>Multi-Level Akses</h3>
          <p>Dinas Kesehatan Daerah memantau secara menyeluruh, sementara tiap Fasilitas Kesehatan mengelola data secara mandiri.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="kenapa" class="section-alt">
    <div class="container">
      <div class="section-head" data-aos="fade-up">
        <span class="eyebrow"><i class="fa-solid fa-circle-question"></i> Kenapa SIPUAS?</span>
        <h2>Jawaban atas kebutuhan mutu layanan kesehatan yang terukur</h2>
        <p>Dari loket puskesmas hingga meja pimpinan dinas, SIPUAS menyatukan data kepuasan warga menjadi satu acuan yang sama.</p>
      </div>
      <div class="why-grid">
        <div class="why-media" data-aos="fade-right" data-aos-duration="700">
          <img src="{{ asset('images/petugas-loket.jpg') }}" alt="Petugas layanan kesehatan Puskesmas">
          <span class="tag"><i class="fa-solid fa-hand-holding-heart"></i> Melayani warga Purworejo</span>
        </div>
        <div class="why-list">
          <div class="why-row" data-aos="fade-up" data-aos-duration="600" data-aos-delay="0" data-aos-easing="ease-out-back">
            <div class="num">01</div>
            <div><h3>Evaluasi Mutu Berkelanjutan</h3><p>Pantau performa tiap unit dari waktu ke waktu, bukan sekadar potret tahunan sekali survei.</p></div>
          </div>
          <div class="why-row" data-aos="fade-up" data-aos-duration="600" data-aos-delay="150" data-aos-easing="ease-out-back">
            <div class="num">02</div>
            <div><h3>Kepatuhan Regulasi</h3><p>Sesuai amanat Permenpan RB No. 14/2017, laporan IKM siap disampaikan ke instansi pengawas.</p></div>
          </div>
          <div class="why-row" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300" data-aos-easing="ease-out-back">
            <div class="num">03</div>
            <div><h3>Deteksi Dini Keluhan</h3><p>Unsur "penanganan pengaduan" yang bernilai rendah langsung menyalakan notifikasi ke admin unit.</p></div>
          </div>
          <div class="why-row" data-aos="fade-up" data-aos-duration="600" data-aos-delay="450" data-aos-easing="ease-out-back">
            <div class="num">04</div>
            <div><h3>Transparansi ke Publik</h3><p>Hasil IKM dapat dipublikasikan sebagai bentuk akuntabilitas layanan kesehatan daerah.</p>            </div>
          </div>
        </div>
    </div>
  </section>

  <section id="cara-kerja" class="container">
    <div class="how-wrap" data-aos="fade-up">
      <div class="section-head">
        <span class="eyebrow"><i class="fa-solid fa-route"></i> Alur Kerja</span>
        <h2>Cara Mengisi Survei</h2>
        <p>Langkah mudah untuk memberikan penilaian terhadap layanan kesehatan.</p>
      </div>

      <div>
            <div class="how-grid cols-3">
              <div class="how-step" data-aos="zoom-in" data-aos-duration="450">
                <div class="num"><i class="fa-solid fa-qrcode"></i></div>
                <h3>1. Pindai QR Code</h3>
                <p>Masyarakat memindai QR code kuesioner yang tersedia di loket layanan kesehatan.</p>
              </div>
              <div class="how-step" data-aos="zoom-in" data-aos-duration="450" data-aos-delay="100">
                <div class="num"><i class="fa-solid fa-star"></i></div>
                <h3>2. Isi Penilaian</h3>
                <p>Memberikan tanggapan &amp; nilai kepuasan sesuai layanan yang baru saja diterima.</p>
              </div>
              <div class="how-step" data-aos="zoom-in" data-aos-duration="450" data-aos-delay="200">
                <div class="num"><i class="fa-solid fa-paper-plane"></i></div>
                <h3>3. Kirim &amp; Selesai</h3>
                <p>Jawaban tersimpan otomatis dan anonim — tanpa perlu login atau data pribadi.</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <section id="landasan" class="section-alt">
    <div class="container">
      <div class="section-head" data-aos="fade-up">
        <span class="eyebrow"><i class="fa-solid fa-gavel"></i> Landasan Hukum</span>
        <h2>Dasar Regulasi SKM</h2>
        <p>Pelaksanaan SKM digital ini berpijak pada peraturan resmi yang mengatur mutu pelayanan publik.</p>
      </div>
      <div class="law-grid">
        <div class="law-card" data-aos="fade-up" data-aos-duration="500">
          <div class="kicker">Peraturan Menteri PANRB</div>
          <h3>Permenpan RB No. 14 Tahun 2017</h3>
          <p>Tentang Pedoman Survei Kepuasan Masyarakat — dasar penetapan 9 unsur pelayanan wajib yang dipakai SKM.</p>
          <div class="status"><i class="fa-solid fa-circle"></i> Berlaku nasional</div>
        </div>
        <div class="law-card" data-aos="fade-up" data-aos-duration="500" data-aos-delay="100">
          <div class="kicker">Peraturan Bupati Purworejo</div>
          <h3>Perbup ttg Pelayanan Publik</h3>
          <p>Mengatur pelaksanaan survei kepuasan masyarakat digital di lingkup OPD, termasuk Dinas Kesehatan Daerah.</p>
          <div class="status"><i class="fa-solid fa-circle"></i> Berlaku di Kab. Purworejo</div>
        </div>
        <div class="law-card" data-aos="fade-up" data-aos-duration="500" data-aos-delay="200">
          <div class="kicker">Keputusan Kepala Dinas</div>
          <h3>SK Penetapan Pelaksana</h3>
          <p>Menetapkan unit pelaksana dan penanggung jawab pengelolaan SKM di tiap Fasilitas Kesehatan.</p>
          <div class="status"><i class="fa-solid fa-circle"></i> Ditetapkan Dinkesda</div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="cta-banner" data-aos="zoom-in" data-aos-duration="600">
        <div>
          <h2>Siap mengelola SKM secara digital?</h2>
          <p>Login ke sistem untuk mengatur kuesioner, mengunduh QR Code, dan memantau perkembangan nilai IKM unit Anda.</p>
        </div>
        <a href="{{ route('login') }}" class="btn btn-primary">
          Login
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>

  <section id="faq" class="section-alt">
    <div class="container">
      <div class="section-head" data-aos="fade-up">
        <span class="eyebrow"><i class="fa-solid fa-comments"></i> Pertanyaan Umum</span>
        <h2>Yang sering ditanyakan tentang SKM</h2>
      </div>
      <div class="faq-list">
        <details class="faq-item" open data-aos="fade-up" data-aos-duration="450">
          <summary>Apa itu SKM? <span class="icon"><i class="fa-solid fa-plus"></i></span></summary>
          <div class="faq-body">SKM adalah sistem digital Survei Kepuasan Masyarakat milik Dinas Kesehatan Daerah Kabupaten Purworejo, digunakan seluruh Fasilitas Kesehatan untuk mengumpulkan dan merekap penilaian warga terhadap layanan kesehatan.</div>
        </details>
        <details class="faq-item" data-aos="fade-up" data-aos-duration="450" data-aos-delay="60">
          <summary>Siapa yang wajib mengisi survei ini? <span class="icon"><i class="fa-solid fa-plus"></i></span></summary>
          <div class="faq-body">Setiap warga yang baru saja menerima layanan di loket, poli, atau unit kesehatan yang telah memasang QR Code SKM dipersilakan mengisi, namun sifatnya sukarela.</div>
        </details>
        <details class="faq-item" data-aos="fade-up" data-aos-duration="450" data-aos-delay="120">
          <summary>Bagaimana cara mengakses formulir survei? <span class="icon"><i class="fa-solid fa-plus"></i></span></summary>
          <div class="faq-body">Cukup pindai QR Code yang tersedia di loket atau ruang tunggu menggunakan kamera ponsel — formulir terbuka langsung di peramban tanpa perlu memasang aplikasi tambahan.</div>
        </details>
        <details class="faq-item" data-aos="fade-up" data-aos-duration="450" data-aos-delay="180">
          <summary>Apakah jawaban responden bersifat rahasia? <span class="icon"><i class="fa-solid fa-plus"></i></span></summary>
          <div class="faq-body">Ya. Pengisian bersifat anonim, tidak meminta nama atau data pribadi responden, dan hasilnya hanya diagregasi untuk kebutuhan penghitungan IKM per unit.</div>
        </details>
        <details class="faq-item" data-aos="fade-up" data-aos-duration="450" data-aos-delay="240">
          <summary>Bagaimana skor IKM dihitung? <span class="icon"><i class="fa-solid fa-plus"></i></span></summary>
          <div class="faq-body">Skor dihitung otomatis oleh sistem berdasarkan rata-rata tertimbang dari 9 unsur pelayanan sesuai Permenpan RB No. 14 Tahun 2017, lalu dikonversi ke skala IKM 25–100.</div>
        </details>
      </div>
    </div>
  </section>

  <footer>
    <div class="container">
      <div class="footer-inner">
        <div class="footer-brand">
          <a href="/" class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Purworejo">
            <span class="brand-text">
              <strong>SKM</strong>
              <span>Survei Kepuasan Masyarakat</span>
            </span>
          </a>
          <p>Sistem digital resmi Dinas Kesehatan Daerah Kabupaten Purworejo untuk memantau mutu layanan kesehatan di seluruh Fasilitas Kesehatan.</p>
        </div>
        <div class="footer-col">
          <h4>Navigasi</h4>
          <ul>
            <li><a href="#fitur"><i class="fa-solid fa-chevron-right"></i>Fitur Utama</a></li>
            <li><a href="#kenapa"><i class="fa-solid fa-chevron-right"></i>Kenapa SKM</a></li>
            <li><a href="#cara-kerja"><i class="fa-solid fa-chevron-right"></i>Cara Kerja</a></li>
            <li><a href="#landasan"><i class="fa-solid fa-chevron-right"></i>Landasan Hukum</a></li>
            <li><a href="{{ route('login') }}"><i class="fa-solid fa-chevron-right"></i>Login</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Jaringan Layanan</h4>
          <ul>
            <li><i class="fa-solid fa-building-columns"></i>Dinkesda Purworejo</li>
            <li><i class="fa-solid fa-hospital"></i>Puskesmas</li>
            <li><i class="fa-solid fa-house-medical"></i>RSU</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} SKM &middot; Dinas Kesehatan Daerah Kabupaten Purworejo</span>
        <span>Sesuai Permenpan RB No. 14 Tahun 2017</span>
      </div>
    </div>
  </footer>

  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    const navbar = document.getElementById('navbar');
    if (navbar) {
      window.addEventListener('scroll', () => {
        navbar.classList.toggle('is-scrolled', window.scrollY > 8);
      }, { passive: true });
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (window.AOS) {
      AOS.init({
        duration: 600,
        easing: 'ease-out-cubic',
        once: true,
        offset: 60,
        disable: prefersReducedMotion
      });
    }
  </script>

  <!-- loading-bar -->
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

</body>
</html>
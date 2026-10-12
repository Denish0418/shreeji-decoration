<?php

require_once 'includes/session.php';
require_once 'config/db.php';
$settingsQuery = mysqli_query(
  $conn,
  "SELECT * FROM settings LIMIT 1"
);

$settings = mysqli_fetch_assoc(
  $settingsQuery
);

$wholesaleStepsQuery = mysqli_query($conn, "SELECT * FROM wholesale_steps ORDER BY display_order ASC, id ASC");
$wholesaleSteps = [];
while ($row = mysqli_fetch_assoc($wholesaleStepsQuery)) {
    $wholesaleSteps[] = $row;
}

$wholesaleBenefitsQuery = mysqli_query($conn, "SELECT * FROM wholesale_benefits ORDER BY display_order ASC, id ASC");
$wholesaleBenefits = [];
while ($row = mysqli_fetch_assoc($wholesaleBenefitsQuery)) {
    $wholesaleBenefits[] = $row;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Wholesale Division – Shreeji Decoration</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #0D0800;
      color: #fff
    }

   

    /* ── HERO ── */
    .ws-hero {
      background: linear-gradient(135deg, #0D0800 0%, #2E1200 45%, #180A00 100%);
      padding: 90px 0 70px;
      position: relative;
      overflow: hidden
    }

    .ws-hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse 65% 75% at 85% 45%, rgba(200, 167, 106, .07), transparent 60%),
        radial-gradient(ellipse 40% 60% at 5% 90%, rgba(232, 119, 46, .05), transparent 55%)
    }

    .hero-grid-lines {
      position: absolute;
      inset: 0;
      pointer-events: none;
      background-image: repeating-linear-gradient(0deg, rgba(200, 167, 106, .025) 0, rgba(200, 167, 106, .025) 1px, transparent 1px, transparent 56px),
        repeating-linear-gradient(90deg, rgba(200, 167, 106, .025) 0, rgba(200, 167, 106, .025) 1px, transparent 1px, transparent 56px)
    }

    .page-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(200, 167, 106, .1);
      border: 1px solid rgba(200, 167, 106, .25);
      color: #C9A063;
      font-size: .7rem;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      padding: 5px 18px;
      border-radius: 50px;
      margin-bottom: 22px
    }

    .ws-hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.2rem, 5vw, 3.6rem);
      color: #fff;
      font-weight: 700;
      line-height: 1.12;
      margin-bottom: 18px
    }

    .ws-hero h1 em {
      color: #C9A063;
      font-style: italic
    }

    .ws-hero .lead-text {
      color: rgba(255, 255, 255, .58);
      font-size: .97rem;
      line-height: 1.8;
      max-width: 500px;
      margin-bottom: 30px
    }

    .hero-btns {
      display: flex;
      gap: 12px;
      flex-wrap: wrap
    }

    .btn-wa-hero {
      background: #25D366;
      color: #fff;
      padding: 12px 26px;
      border-radius: 50px;
      font-weight: 700;
      font-size: .87rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all .25s;
      border: none
    }

    .btn-wa-hero:hover {
      background: #1DB954;
      color: #fff;
      transform: translateY(-2px)
    }

    .btn-outline-gold {
      border: 1.5px solid rgba(200, 167, 106, .4);
      color: #C9A063;
      background: transparent;
      padding: 11px 26px;
      border-radius: 50px;
      font-weight: 600;
      font-size: .87rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all .25s
    }

    .btn-outline-gold:hover {
      background: rgba(200, 167, 106, .1);
      border-color: #C9A063;
      color: #C9A063
    }

    .kpi-strip {
      display: flex;
      background: rgba(255, 255, 255, .03);
      border: 1px solid rgba(200, 167, 106, .18);
      border-radius: 14px;
      overflow: hidden;
      margin-top: 40px
    }

    .kpi-cell {
      flex: 1;
      text-align: center;
      padding: 18px 10px;
      border-right: 1px solid rgba(200, 167, 106, .12)
    }

    .kpi-cell:last-child {
      border-right: none
    }

    .kpi-num {
      display: block;
      font-family: 'Playfair Display', serif;
      font-size: 1.9rem;
      font-weight: 700;
      background: linear-gradient(135deg, #C9A063, #B8863F);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text
    }

    .kpi-lbl {
      display: block;
      color: rgba(255, 255, 255, .4);
      font-size: .7rem;
      margin-top: 3px
    }

    /* hero right grid */
    .hero-cards-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px
    }

    .hc {
      background: rgba(255, 255, 255, .04);
      border: 1px solid rgba(200, 167, 106, .13);
      border-radius: 14px;
      padding: 20px 16px;
      transition: all .3s
    }

    .hc:hover {
      border-color: rgba(200, 167, 106, .35);
      background: rgba(200, 167, 106, .06);
      transform: translateY(-4px)
    }

    .hc.wide {
      grid-column: 1/-1;
      display: flex;
      align-items: center;
      gap: 16px;
      background: linear-gradient(135deg, rgba(200, 167, 106, .08), rgba(232, 119, 46, .05));
      border-color: rgba(200, 167, 106, .22)
    }

    .hc-emoji {
      font-size: 1.8rem;
      margin-bottom: 8px
    }

    .hc h6 {
      color: #fff;
      font-size: .88rem;
      font-weight: 700;
      margin-bottom: 4px
    }

    .hc p {
      color: rgba(255, 255, 255, .43);
      font-size: .74rem;
      line-height: 1.55;
      margin: 0
    }

    .hc-tag {
      display: inline-block;
      background: rgba(200, 167, 106, .15);
      color: #C9A063;
      font-size: .63rem;
      font-weight: 700;
      padding: 2px 9px;
      border-radius: 50px;
      margin-top: 7px
    }

    /* ── SECTION SHARED ── */
    .section-dark {
      background: #0D0800;
      padding: 76px 0
    }

    .section-mid {
      background: linear-gradient(160deg, #130900, #0D0800);
      padding: 76px 0
    }

    .chip {
      display: inline-block;
      background: rgba(200, 167, 106, .1);
      border: 1px solid rgba(200, 167, 106, .22);
      color: #C9A063;
      font-size: .68rem;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      padding: 5px 16px;
      border-radius: 50px;
      margin-bottom: 12px
    }

    .sec-h2 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.6rem, 3.5vw, 2.4rem);
      color: #fff;
      font-weight: 700;
      margin-bottom: 8px
    }

    .sec-h2 em {
      color: #C9A063;
      font-style: italic
    }

    .sec-line {
      width: 48px;
      height: 3px;
      background: linear-gradient(90deg, #C9A063, #B8863F);
      border-radius: 3px;
      margin: 0 auto 44px
    }

    /* HOW IT WORKS */
    .step-box {
      background: rgba(255, 255, 255, .03);
      border: 1px solid rgba(200, 167, 106, .12);
      border-radius: 16px;
      padding: 30px 22px;
      text-align: center;
      height: 100%;
      position: relative;
      transition: all .3s
    }

    .step-box:hover {
      border-color: rgba(200, 167, 106, .32);
      background: rgba(200, 167, 106, .05);
      transform: translateY(-5px)
    }

    .step-no {
      position: absolute;
      top: -14px;
      left: 50%;
      transform: translateX(-50%);
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: linear-gradient(135deg, #C9A063, #B8863F);
      color: #0D0800;
      font-weight: 800;
      font-size: .78rem;
      display: flex;
      align-items: center;
      justify-content: center
    }

    .step-ico {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: rgba(200, 167, 106, .1);
      border: 1px solid rgba(200, 167, 106, .2);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #C9A063;
      font-size: 1.35rem;
      margin: 14px auto 14px
    }

    .step-box h6 {
      color: #fff;
      font-weight: 700;
      font-size: .92rem;
      margin-bottom: 7px
    }

    .step-box p {
      color: rgba(255, 255, 255, .43);
      font-size: .78rem;
      line-height: 1.65;
      margin: 0
    }

    /* CTA TWIN */
    .cta-pair {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 52px
    }

    .cta-tile {
      border-radius: 20px;
      padding: 38px 28px;
      position: relative;
      overflow: hidden;
      text-align: center
    }

    .cta-tile.green-tile {
      background: linear-gradient(135deg, #064e3b, #065f46);
      border: 1px solid rgba(37, 211, 102, .18)
    }

    .cta-tile.gold-tile {
      background: linear-gradient(135deg, #2A1A00, #3D2200);
      border: 1px solid rgba(200, 167, 106, .22)
    }

    .cta-tile::after {
      content: '';
      position: absolute;
      top: -50px;
      right: -50px;
      width: 160px;
      height: 160px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .03)
    }

    .cta-tile .t-ico {
      font-size: 2.8rem;
      margin-bottom: 14px;
      position: relative;
      z-index: 1
    }

    .cta-tile h5 {
      font-family: 'Playfair Display', serif;
      color: #fff;
      font-size: 1.2rem;
      margin-bottom: 9px;
      position: relative;
      z-index: 1
    }

    .cta-tile p {
      color: rgba(255, 255, 255, .58);
      font-size: .82rem;
      line-height: 1.7;
      margin-bottom: 22px;
      position: relative;
      z-index: 1
    }

    .btn-green {
      background: #25D366;
      color: #fff;
      padding: 11px 28px;
      border-radius: 50px;
      font-weight: 700;
      font-size: .84rem;
      text-decoration: none;
      display: inline-block;
      transition: all .2s
    }

    .btn-green:hover {
      background: #1DB954;
      color: #fff;
      transform: translateY(-2px)
    }

    .btn-gold {
      background: linear-gradient(135deg, #C9A063, #B8863F);
      color: #0D0800;
      padding: 11px 28px;
      border-radius: 50px;
      font-weight: 700;
      font-size: .84rem;
      text-decoration: none;
      display: inline-block;
      transition: all .2s
    }

    .btn-gold:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(232, 119, 46, .4);
      color: #0D0800
    }

    /* BENEFITS */
    .ben-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
      gap: 18px
    }

    .ben-card {
      background: rgba(255, 255, 255, .03);
      border: 1px solid rgba(200, 167, 106, .11);
      border-radius: 16px;
      padding: 26px 22px;
      display: flex;
      gap: 16px;
      align-items: flex-start;
      transition: all .3s
    }

    .ben-card:hover {
      border-color: rgba(200, 167, 106, .28);
      background: rgba(200, 167, 106, .05)
    }

    .ben-ico {
      width: 48px;
      height: 48px;
      flex-shrink: 0;
      border-radius: 12px;
      background: linear-gradient(135deg, rgba(200, 167, 106, .14), rgba(232, 119, 46, .08));
      border: 1px solid rgba(200, 167, 106, .18);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #C9A063;
      font-size: 1.15rem
    }

    .ben-card h6 {
      color: #fff;
      font-weight: 700;
      font-size: .9rem;
      margin-bottom: 5px
    }

    .ben-card p {
      color: rgba(255, 255, 255, .43);
      font-size: .78rem;
      line-height: 1.65;
      margin: 0
    }

    /* CONTACT CARDS */
    .con-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 16px
    }

    .con-card {
      background: rgba(255, 255, 255, .03);
      border: 1px solid rgba(200, 167, 106, .12);
      border-radius: 16px;
      padding: 24px 20px;
      transition: all .3s
    }

    .con-card:hover {
      border-color: rgba(200, 167, 106, .32);
      transform: translateY(-4px)
    }

    .con-icon {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: linear-gradient(135deg, #C9A063, #B8863F);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #0D0800;
      font-size: 1.1rem;
      margin-bottom: 14px
    }

    .con-icon.green-icon {
      background: #25D366
    }

    .con-card h6 {
      color: #C9A063;
      font-size: .72rem;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      margin-bottom: 7px
    }

    .con-card p {
      color: rgba(255, 255, 255, .65);
      font-size: .83rem;
      line-height: 1.75;
      margin: 0
    }

    .con-card a {
      color: rgba(255, 255, 255, .65);
      text-decoration: none;
      transition: color .2s
    }

    .con-card a:hover {
      color: #C9A063
    }

    /* SWITCH BANNER */
    .switch-bar {
      background: linear-gradient(135deg, #FDF8F0, #F5EDD8);
      padding: 52px 0;
      border-top: 1px solid rgba(200, 167, 106, .2)
    }

    .switch-bar h4 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      color: #2A1800;
      margin-bottom: 8px
    }

    .switch-bar p {
      color: #6B4A20;
      font-size: .88rem;
      margin-bottom: 22px
    }

    .btn-dark-gold {
      background: #2A1800;
      color: #C9A063;
      padding: 12px 28px;
      border-radius: 50px;
      font-weight: 700;
      font-size: .87rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all .2s
    }

    .btn-dark-gold:hover {
      background: #C9A063;
      color: #0D0800
    }

    /* FOOTER */
    .main-footer {
      background: #080500
    }

    .footer-top {
      padding: 52px 0 32px;
      border-bottom: 1px solid rgba(200, 167, 106, .1)
    }

    .footer-head {
      color: #C9A063;
      font-size: .72rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      font-weight: 700;
      margin-bottom: 14px
    }

    .footer-links {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 8px
    }

    .footer-links a {
      color: rgba(255, 255, 255, .38);
      font-size: .83rem;
      text-decoration: none;
      transition: color .2s
    }

    .footer-links a:hover {
      color: #C9A063
    }

    .footer-bottom {
      padding: 16px 0;
      background: rgba(0, 0, 0, .3)
    }

    .newsletter-form {
      display: flex;
      border: 1px solid rgba(200, 167, 106, .18);
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 16px
    }

    .newsletter-form input {
      flex: 1;
      background: transparent;
      border: none;
      padding: 9px 14px;
      color: #fff;
      font-size: .8rem;
      outline: none;
      font-family: inherit
    }

    .newsletter-form input::placeholder {
      color: rgba(255, 255, 255, .28)
    }

    .btn-saffron-sm {
      background: linear-gradient(135deg, #C9A063, #B8863F);
      color: #0D0800;
      border: none;
      padding: 9px 16px;
      font-weight: 700;
      font-size: .75rem;
      white-space: nowrap;
      cursor: pointer;
      font-family: inherit
    }

    .pay-badges {
      display: flex;
      flex-wrap: wrap;
      gap: 6px
    }

    .pay-badges span {
      background: rgba(255, 255, 255, .04);
      border: 1px solid rgba(255, 255, 255, .07);
      color: rgba(255, 255, 255, .4);
      font-size: .68rem;
      padding: 3px 10px;
      border-radius: 6px
    }

    .soc-row {
      display: flex;
      gap: 8px;
      margin-top: 16px
    }

    .soc-ico {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .05);
      border: 1px solid rgba(255, 255, 255, .07);
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(255, 255, 255, .45);
      font-size: .85rem;
      text-decoration: none;
      transition: all .2s
    }

    .soc-ico:hover {
      background: rgba(200, 167, 106, .15);
      border-color: #C9A063;
      color: #C9A063
    }

    .whatsapp-float {
      position: fixed;
      bottom: 24px;
      right: 24px;
      width: 52px;
      height: 52px;
      background: #25D366;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 1.5rem;
      box-shadow: 0 6px 24px rgba(37, 211, 102, .4);
      z-index: 999;
      text-decoration: none;
      transition: transform .2s
    }

    .whatsapp-float:hover {
      transform: scale(1.1);
      color: #fff
    }

    .btt {
      position: fixed;
      bottom: 24px;
      right: 84px;
      width: 44px;
      height: 44px;
      background: linear-gradient(135deg, #C9A063, #B8863F);
      color: #0D0800;
      border: none;
      border-radius: 50%;
      display: none;
      align-items: center;
      justify-content: center;
      font-size: .95rem;
      cursor: pointer;
      z-index: 999;
      box-shadow: 0 4px 16px rgba(200, 167, 106, .3)
    }

    .btt.show {
      display: flex
    }

    @media(max-width:767px) {
      .cta-pair {
        grid-template-columns: 1fr
      }

      .hero-cards-grid {
        grid-template-columns: 1fr
      }

      .kpi-strip {
        flex-wrap: wrap
      }

      .kpi-cell {
        flex: 1 1 45%;
        border-right: none;
        border-bottom: 1px solid rgba(200, 167, 106, .1)
      }
    }

    /* WHOLESALE REGISTRATION FORM */
    .ws-form-card {
      background: linear-gradient(160deg, rgba(200,167,106,.06), rgba(232,119,46,.03));
      border: 1px solid rgba(200, 167, 106, .18);
      border-radius: 24px;
      padding: 44px;
    }

    @media (max-width: 767px) {
      .ws-form-card { padding: 26px 20px; }
    }

    .ws-input-group {
      display: flex;
      align-items: center;
      background: #fff;
      border: 1.5px solid transparent;
      border-radius: 14px;
      padding: 0 16px;
      transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .ws-input-group:focus-within {
      border-color: #B8863F;
      box-shadow: 0 0 0 4px rgba(232, 119, 46, .15);
      transform: translateY(-2px);
    }

    .ws-input-group i {
      color: #8F6428;
      font-size: 1rem;
      margin-right: 12px;
      flex-shrink: 0;
    }

    .ws-input-group textarea i {
      margin-top: 14px;
      align-self: flex-start;
    }

    .ws-input-group input,
    .ws-input-group textarea {
      border: none;
      outline: none;
      background: transparent;
      width: 100%;
      padding: 15px 0;
      font-size: .95rem;
      color: #2A1800;
      font-family: 'Poppins', sans-serif;
    }

    .ws-input-group textarea {
      padding: 15px 0;
      resize: vertical;
    }

    .ws-input-group input::placeholder,
    .ws-input-group textarea::placeholder {
      color: #9a8b78;
    }

    .ws-submit-btn {
      background: linear-gradient(135deg, #C9A063, #B8863F);
      color: #0D0800;
      padding: 15px 46px;
      border-radius: 50px;
      font-weight: 700;
      font-size: .95rem;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all .25s ease;
    }

    .ws-submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(232, 119, 46, .4);
    }
  </style>
</head>

<body>
<?php include 'includes/header.php'; ?>
  <?php

  if (isset($_GET['success'])) {

    echo '

<div class="alert alert-success text-center m-0">

Wholesale request submitted successfully.

</div>

';
  }

  ?>

  

  

  <!-- HERO -->
  <section class="ws-hero">
    <div class="hero-grid-lines"></div>
    <div class="container position-relative" style="z-index:1">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <nav aria-label="breadcrumb" style="margin-bottom:18px">
            <span style="font-size:.73rem;color:rgba(255,255,255,.35)">
              <a href="index.php" style="color:rgba(255,255,255,.4);text-decoration:none">Home</a>
              <span style="margin:0 8px;color:rgba(255,255,255,.2)">/</span>
              <span style="color:rgba(255,255,255,.65)">Wholesale</span>
            </span>
          </nav>
          <div class="page-badge"><i class="bi bi-building-check"></i> For Dealers &amp; Businesses</div>
          <h1><?= $settings['wholesale_hero_title'] ?? "Gujarat's #1 Wholesale Decoration Supplier"; ?></h1>
          <p class="lead-text"><?= htmlspecialchars($settings['wholesale_hero_subtitle'] ?? 'Serving 500+ active dealers across India with premium decoration products, competitive bulk pricing, and 48-hour dispatch — all year round.'); ?></p>
          <div class="hero-btns">
            <a href="https://wa.me/919876543210" target="_blank" class="btn-wa-hero">
              <i class="bi bi-whatsapp"></i>WhatsApp Now
            </a>
            <a href="#how-it-works" class="btn-outline-gold">
              <i class="bi bi-info-circle"></i>How It Works
            </a>
          </div>
          <div class="kpi-strip">
            <?php for ($i = 1; $i <= 4; $i++) {
                $num = $settings['wholesale_stat' . $i . '_num'] ?? '';
                $label = $settings['wholesale_stat' . $i . '_label'] ?? '';
                if ($num === '' && $label === '') continue;
            ?>
              <div class="kpi-cell"><span class="kpi-num"><?= htmlspecialchars($num); ?></span><span class="kpi-lbl"><?= htmlspecialchars($label); ?></span></div>
            <?php } ?>
          </div>
        </div>
        <div class="col-lg-6 d-none d-lg-block">
          <div class="hero-cards-grid">
            <div class="hc">
              <div class="hc-emoji">💒</div>
              <h6>Wedding Decor</h6>
              <p>Mandap kits, phoolon ki chaadar, stage &amp; entry decor</p>
              <span class="hc-tag">MOQ 50 units</span>
            </div>
            <div class="hc">
              <div class="hc-emoji">🪔</div>
              <h6>Festival Supplies</h6>
              <p>Diwali, Navratri, Ganpati — all season stock</p>
              <span class="hc-tag">MOQ 100 units</span>
            </div>
            <div class="hc">
              <div class="hc-emoji">🎉</div>
              <h6>Party &amp; Balloons</h6>
              <p>Birthday, anniversary &amp; theme kits in bulk</p>
              <span class="hc-tag">MOQ 100 units</span>
            </div>
            <div class="hc">
              <div class="hc-emoji">💡</div>
              <h6>LED &amp; Lighting</h6>
              <p>Fairy lights, strips, string lights &amp; diyas</p>
              <span class="hc-tag">MOQ 50 units</span>
            </div>
            <div class="hc wide">
              <div style="font-size:2rem;flex-shrink:0">📦</div>
              <div>
                <h6>200+ Product Categories</h6>
                <p>Full wholesale catalogue covering all Indian festivals &amp; ceremonies — request yours today</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section class="section-dark" id="how-it-works">
    <div class="container">
      <div class="text-center">
        <div class="chip">Simple Process</div>
        <h2 class="sec-h2">How It <em>Works</em></h2>
        <div class="sec-line"></div>
      </div>
      <div class="row g-4">
        <?php foreach ($wholesaleSteps as $i => $step) { ?>
        <div class="col-md-3 col-sm-6">
          <div class="step-box">
            <div class="step-no"><?= (int) $step['step_number'] ?: ($i + 1); ?></div>
            <div class="step-ico"><i class="bi <?= htmlspecialchars($step['icon'] ?: 'bi-check-circle'); ?>"></i></div>
            <h6><?= htmlspecialchars($step['title']); ?></h6>
            <p><?= htmlspecialchars($step['description']); ?></p>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </section>

  <!-- CTA + BENEFITS -->
  <section class="section-mid">
    <div class="container">

      <div class="cta-pair">
        <div class="cta-tile green-tile">
          <div class="t-ico">💬</div>
          <h5><?= htmlspecialchars($settings['wholesale_group_heading'] ?? 'Join WhatsApp Group'); ?></h5>
          <p><?= htmlspecialchars($settings['wholesale_group_text'] ?? 'Get real-time stock updates, flash sale alerts, and new festival collections before anyone else.'); ?></p>
          <a href="<?= htmlspecialchars($settings['wholesale_group_link'] ?? 'https://chat.whatsapp.com/'); ?>" target="_blank" class="btn-green"><i class="bi bi-whatsapp me-2"></i>Join Group</a>
        </div>
        <div class="cta-tile gold-tile">
          <div class="t-ico">📖</div>
          <h5><?= htmlspecialchars($settings['wholesale_catalogue_heading'] ?? 'Download Catalogue'); ?></h5>
          <p><?= htmlspecialchars($settings['wholesale_catalogue_text'] ?? '200+ products with MOQs, wholesale pricing tiers, and product photos — free on request.'); ?></p>
          <?php if (!empty($settings['wholesale_catalogue'])) { ?>
            <a href="uploads/catalogue/<?= htmlspecialchars($settings['wholesale_catalogue']); ?>" target="_blank" class="btn-gold"><i class="bi bi-download me-2"></i>View Catalogue</a>
          <?php } else { ?>
            <a href="<?= htmlspecialchars($settings['whatsapp'] ?? '#'); ?>" target="_blank" class="btn-gold"><i class="bi bi-whatsapp me-2"></i>Request via WhatsApp</a>
          <?php } ?>
        </div>
      </div>

      <div class="text-center mb-5">
        <div class="chip">Why Partner With Us</div>
        <h2 class="sec-h2">Wholesale <em>Benefits</em></h2>
        <div class="sec-line"></div>
      </div>
      <div class="ben-grid">
        <?php foreach ($wholesaleBenefits as $benefit) { ?>
        <div class="ben-card">
          <div class="ben-ico"><i class="bi <?= htmlspecialchars($benefit['icon'] ?: 'bi-check-circle'); ?>"></i></div>
          <div>
            <h6><?= htmlspecialchars($benefit['title']); ?></h6>
            <p><?= htmlspecialchars($benefit['description']); ?></p>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  <section class="section-dark">
    <div class="container">
      <div class="text-center mb-5">
        <div class="chip">Reach Our Team</div>
        <h2 class="sec-h2">Contact <em>Wholesale</em></h2>
        <div class="sec-line"></div>
      </div>
      <div class="con-grid">
        <div class="con-card">
          <div class="con-icon"><i class="bi bi-geo-alt-fill"></i></div>
          <h6>Visit Store</h6>
          <p><?= nl2br(htmlspecialchars($settings['address'] ?? 'Ahmedabad, Gujarat')); ?></p>
        </div>
        <div class="con-card">
          <div class="con-icon"><i class="bi bi-telephone-fill"></i></div>
          <h6>Call Us</h6>
          <p>
            <?php if (!empty($settings['phone'])) { ?>
              <a href="tel:<?= htmlspecialchars($settings['phone']); ?>"><?= htmlspecialchars($settings['phone']); ?></a> (Retail)
            <?php } ?>
            <?php if (!empty($settings['wholesale_phone'])) { ?>
              <br /><a href="tel:<?= htmlspecialchars($settings['wholesale_phone']); ?>"><?= htmlspecialchars($settings['wholesale_phone']); ?></a> (Wholesale)
            <?php } ?>
          </p>
        </div>
        <div class="con-card">
          <div class="con-icon green-icon"><i class="bi bi-whatsapp"></i></div>
          <h6>WhatsApp</h6>
          <p><a href="<?= htmlspecialchars($settings['whatsapp'] ?? '#'); ?>" target="_blank"><?= htmlspecialchars($settings['phone'] ?? ''); ?></a><br />9 AM – 9 PM · 7 days</p>
        </div>
        <div class="con-card">
          <div class="con-icon"><i class="bi bi-envelope-fill"></i></div>
          <h6>Email</h6>
          <p>
            <?php if (!empty($settings['wholesale_email'])) { ?>
              <a href="mailto:<?= htmlspecialchars($settings['wholesale_email']); ?>"><?= htmlspecialchars($settings['wholesale_email']); ?></a><br />
            <?php } ?>
            <?php if (!empty($settings['email'])) { ?>
              <a href="mailto:<?= htmlspecialchars($settings['email']); ?>"><?= htmlspecialchars($settings['email']); ?></a>
            <?php } ?>
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="section-mid">

    <div class="container">

      <div class="text-center mb-5">

        <div class="chip">
          Wholesale Registration
        </div>

        <h2 class="sec-h2">

          Become a <em>Wholesale Partner</em>

        </h2>

        <div class="sec-line"></div>

      </div>

      <form
        method="POST"
        action="api/wholesale-api.php"
        class="ws-form-card">

        <div class="row g-4">

          <div class="col-md-6">
            <div class="ws-input-group">
              <i class="bi bi-shop"></i>
              <input
                type="text"
                name="business_name"
                placeholder="Business Name"
                required>
            </div>
          </div>

          <div class="col-md-6">
            <div class="ws-input-group">
              <i class="bi bi-person"></i>
              <input
                type="text"
                name="owner_name"
                placeholder="Owner Name"
                required>
            </div>
          </div>

          <div class="col-md-6">
            <div class="ws-input-group">
              <i class="bi bi-telephone"></i>
              <input
                type="tel"
                name="phone"
                placeholder="Phone Number"
                required>
            </div>
          </div>

          <div class="col-md-6">
            <div class="ws-input-group">
              <i class="bi bi-envelope"></i>
              <input
                type="email"
                name="email"
                placeholder="Email Address"
                required>
            </div>
          </div>

          <div class="col-md-12">
            <div class="ws-input-group">
              <i class="bi bi-geo-alt"></i>
              <input
                type="text"
                name="city"
                placeholder="City"
                required>
            </div>
          </div>

          <div class="col-md-12">
            <div class="ws-input-group">
              <i class="bi bi-chat-left-text"></i>
              <textarea
                name="message"
                rows="5"
                placeholder="Tell us about your business..."
                required></textarea>
            </div>
          </div>

          <div class="col-md-12 text-center mt-2">

            <button
              type="submit"
              class="ws-submit-btn">

              <i class="bi bi-send-fill"></i>
              Submit Wholesale Request

            </button>

          </div>

        </div>

      </form>

    </div>

  </section>

  <!-- SWITCH TO RETAIL -->
  <div class="switch-bar">
    <div class="container text-center">
      <h4>🛍️ Looking for Personal Purchases?</h4>
      <p>Our retail store has 200+ beautifully curated products for weddings, birthdays, festivals and more.</p>
      <a href="retail.php" class="btn-dark-gold"><i class="bi bi-bag-heart-fill"></i>Browse Retail Store</a>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="main-footer">
    <div class="footer-top">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-4">
            <div class="d-flex align-items-center gap-2 mb-3">
              <?php if (!empty($settings['footer_logo'])) { ?>
                <img src="uploads/site-content/<?= htmlspecialchars($settings['footer_logo']); ?>"
                  alt="<?= htmlspecialchars($settings['website_name'] ?? ''); ?>"
                  style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
              <?php } else { ?>
                <div class="brand-icon" style="width:44px;height:44px;border-radius:50%;overflow:hidden;flex-shrink:0;"><img src="uploads/site-content/<?= !empty($settings['logo']) ? htmlspecialchars($settings['logo']) : 'logo.jpg' ?>" alt="Shreeji Decoration" style="width:100%;height:100%;object-fit:cover;display:block;"></div>
              <?php } ?>
              <div>
                <div class="brand-name"><?= htmlspecialchars($settings['website_name'] ?? 'Shreeji Decoration'); ?></div>
                <div class="brand-tagline" style="color:#C9A063"><?= htmlspecialchars($settings['footer_tagline'] ?? 'सजाएँ हर पल, हर जश्न'); ?></div>
              </div>
            </div>
            <p class="small" style="color:rgba(255,255,255,.38);line-height:1.75"><?= htmlspecialchars($settings['footer_text'] ?? 'Premium Indian decorations since 2009. Serving individuals, families and businesses across India.'); ?></p>
            <div class="soc-row">
               <a href="<?= $settings['instagram']; ?>"
          target="_blank"
          class="text-white">

          <i class="bi bi-instagram"></i>

        </a>

        <a href="<?= $settings['facebook']; ?>"
          target="_blank"
          class="text-white">

          <i class="bi bi-facebook"></i>

        </a>

        <a href="<?= $settings['whatsapp']; ?>"
          target="_blank"
          class="text-white">

          <i class="bi bi-whatsapp"></i>

        </a>
              
            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <p class="footer-head">Quick Links</p>
            <ul class="footer-links">
              <li><a href="index.php">Home</a></li>
              <li><a href="index.php#about">About Us</a></li>
              <li><a href="wholesale.php">Wholesale</a></li>
              <li><a href="retail.php">Retail Shop</a></li>
              <li><a href="index.php#gallery">Gallery</a></li>
              <li><a href="faq.php">FAQ</a></li>
              <li><a href="privacy-policy.php">Privacy Policy</a></li>
              <li><a href="terms.php">Terms & Conditions</a></li>
              <li><a href="refund-policy.php">Refund Policy</a></li>
              <li><a href="index.php#contact">Contact</a></li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <p class="footer-head">Occasions</p>
            <ul class="footer-links">
              <?php
              $footerCategoriesQuery = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC LIMIT 8");
              if ($footerCategoriesQuery && mysqli_num_rows($footerCategoriesQuery) > 0) {
                  while ($footerCat = mysqli_fetch_assoc($footerCategoriesQuery)) {
              ?>
                <li><a href="retail.php?category=<?= urlencode($footerCat['name']); ?>"><?= htmlspecialchars($footerCat['name']); ?></a></li>
              <?php
                  }
              } else {
              ?>
                <li><a href="retail.php">All Products</a></li>
              <?php } ?>
            </ul>
          </div>
          <div class="col-lg-4 col-md-4">
            <p class="footer-head"><?= htmlspecialchars($settings['footer_newsletter_heading'] ?? 'Newsletter'); ?></p>
            <p class="small mb-3" style="color:rgba(255,255,255,.35)"><?= htmlspecialchars($settings['footer_newsletter_text'] ?? 'Get seasonal offers, new arrivals & festival deals.'); ?></p>
            <div class="newsletter-form">
              <input type="email" placeholder="Your email address" />
              <button class="btn-saffron-sm">Subscribe</button>
            </div>
            <p class="footer-head mt-3">Payment Methods</p>
            <div class="pay-badges">
              <?php
              $paymentMethods = !empty($settings['footer_payment_methods'])
                  ? explode(',', $settings['footer_payment_methods'])
                  : ['UPI', 'NetBanking', 'Cards', 'NEFT', 'COD'];
              foreach ($paymentMethods as $method) {
                  echo '<span>' . htmlspecialchars(trim($method)) . '</span>';
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-bottom text-center">
      <div class="container">
        <p class="mb-0 small" style="color:rgba(255,255,255,.3)">© <?= date('Y'); ?> <?= htmlspecialchars($settings['website_name'] ?? 'Shreeji Decoration'); ?>. All Rights Reserved. | Made with ❤️ in Ahmedabad, India</p>
      </div>
    </div>
  </footer>

  <a href="https://wa.me/919876543210" class="whatsapp-float" target="_blank"><i class="bi bi-whatsapp"></i></a>
  <button class="btt" id="btt"><i class="bi bi-chevron-up"></i></button>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.addEventListener('scroll', () => {
      document.getElementById('btt').classList.toggle('show', window.scrollY > 300);
      document.getElementById('mainNav').style.boxShadow = window.scrollY > 50 ? '0 4px 30px rgba(0,0,0,.6)' : 'none';
    });
    document.getElementById('btt').addEventListener('click', () => window.scrollTo({
      top: 0,
      behavior: 'smooth'
    }));
  </script>
</body>

</html>
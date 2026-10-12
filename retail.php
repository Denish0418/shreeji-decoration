<?php

require_once 'includes/session.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$customerLoggedIn = isset($_SESSION['customer_mobile']);
require_once 'config/db.php';
$settingsQuery = mysqli_query(
    $conn,
    "SELECT * FROM settings LIMIT 1"
);

$settings = mysqli_fetch_assoc($settingsQuery);

$retailTrustQuery = mysqli_query($conn, "SELECT * FROM retail_trust_items ORDER BY display_order ASC, id ASC");
$retailTrustItems = [];
while ($row = mysqli_fetch_assoc($retailTrustQuery)) {
    $retailTrustItems[] = $row;
}

$retailFeaturesQuery = mysqli_query($conn, "SELECT * FROM retail_features ORDER BY display_order ASC, id ASC");
$retailFeatures = [];
while ($row = mysqli_fetch_assoc($retailFeaturesQuery)) {
    $retailFeatures[] = $row;
}

$categories = mysqli_query(
    $conn,
    "SELECT * FROM categories ORDER BY id ASC"
);
$settingsQuery =
    mysqli_query(
        $conn,
        "SELECT * FROM settings LIMIT 1"
    );

$settings =
    mysqli_fetch_assoc(
        $settingsQuery
    );
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Retail Store – Shreeji Decoration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/customer-login.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #FDF8F0;
            color: #2A1800
        }




        /* ══════════════════════════
       HERO BANNER
    ══════════════════════════ */
        .rt-hero {
            background: linear-gradient(135deg, #2A0E00 0%, #5A1A00 45%, #380E00 100%);
            padding: 90px 0 70px;
            position: relative;
            overflow: hidden
        }

        .rt-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 60% 70% at 85% 40%, rgba(200, 167, 106, .09), transparent 60%),
                radial-gradient(ellipse 45% 60% at 5% 85%, rgba(232, 119, 46, .06), transparent 55%)
        }

        .hero-grid {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image: repeating-linear-gradient(0deg, rgba(200, 167, 106, .03) 0, rgba(200, 167, 106, .03) 1px, transparent 1px, transparent 56px),
                repeating-linear-gradient(90deg, rgba(200, 167, 106, .03) 0, rgba(200, 167, 106, .03) 1px, transparent 1px, transparent 56px)
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

        .rt-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 5vw, 3.6rem);
            color: #fff;
            font-weight: 700;
            line-height: 1.12;
            margin-bottom: 18px
        }

        .rt-hero h1 em {
            color: #C9A063;
            font-style: italic
        }

        .rt-hero .lead-text {
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

        .btn-shop-now {
            background: linear-gradient(135deg, #C9A063, #B8863F);
            color: #0D0800;
            padding: 12px 28px;
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

        .btn-shop-now:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(232, 119, 46, .4);
            color: #0D0800
        }

        .btn-outline-light {
            border: 1.5px solid rgba(255, 255, 255, .3);
            color: rgba(255, 255, 255, .8);
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

        .btn-outline-light:hover {
            border-color: #C9A063;
            color: #C9A063
        }

        .kpi-strip {
            display: flex;
            background: rgba(255, 255, 255, .04);
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

        .hero-preview-tile {
            background: linear-gradient(160deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
            border: 1px solid rgba(200, 167, 106, .22);
            border-radius: 18px;
            padding: 26px 18px;
            text-align: center;
            transition: transform .25s ease, border-color .25s ease, background .25s ease;
        }

        .hero-preview-tile:hover {
            transform: translateY(-5px);
            border-color: #C9A063;
            background: linear-gradient(160deg, rgba(200,167,106,.14), rgba(232,119,46,.06));
        }

        .hero-preview-tile .hpt-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: linear-gradient(135deg, #C9A063, #B8863F);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            color: #0D0800;
        }

        .hero-preview-tile .hpt-label {
            font-size: .86rem;
            font-weight: 700;
            color: #F5EBD8;
            letter-spacing: .02em;
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

        /* Hero category pills */
        .hero-cats {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 32px
        }

        .hcat {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(200, 167, 106, .18);
            color: rgba(255, 255, 255, .75);
            font-size: .78rem;
            padding: 7px 16px;
            border-radius: 50px;
            transition: all .25s;
            text-decoration: none
        }

        .hcat:hover {
            background: rgba(200, 167, 106, .15);
            border-color: #C9A063;
            color: #C9A063
        }

        .hcat span {
            font-size: 1rem
        }

        /* ══════════════════════════
       TRUST BAR
    ══════════════════════════ */
        .trust-bar {
            background: #fff;
            border-top: 3px solid #C9A063;
            border-bottom: 1px solid rgba(200, 167, 106, .2);
            padding: 18px 0
        }

        .trust-items {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            flex-wrap: wrap
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 28px;
            border-right: 1px solid rgba(200, 167, 106, .2)
        }

        .trust-item:last-child {
            border-right: none
        }

        .trust-item i {
            font-size: 1.2rem;
            color: #C9A063
        }

        .trust-item strong {
            font-size: .84rem;
            color: #2A1800;
            font-weight: 700;
            display: block;
            line-height: 1.2
        }

        .trust-item small {
            font-size: .7rem;
            color: #6B4A20
        }

        /* ══════════════════════════
       FEATURED CATEGORIES
    ══════════════════════════ */
        .cat-section {
            padding: 72px 0 48px;
            background: #FDF8F0
        }




        .chip {
            display: inline-block;
            background: rgba(200, 167, 106, .12);
            border: 1px solid rgba(200, 167, 106, .25);
            color: #A07840;
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
            color: #2A1800;
            font-weight: 700;
            margin-bottom: 8px
        }

        .sec-h2 em {
            color: #B8863F;
            font-style: italic
        }

        .sec-line {
            width: 48px;
            height: 3px;
            background: linear-gradient(90deg, #C9A063, #B8863F);
            border-radius: 3px
        }

        .sec-line.center {
            margin: 0 auto 44px
        }

        .occ-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 25px;
        }

        @media(max-width:1200px) {

            .occ-grid {
                grid-template-columns: repeat(4, 1fr);
            }

        }

        @media(max-width:992px) {

            .occ-grid {
                grid-template-columns: repeat(3, 1fr);
            }

        }

        @media(max-width:768px) {

            .occ-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media(max-width:480px) {

            .occ-grid {
                grid-template-columns: 1fr;
            }

        }

        .occ-card {
            border-radius: 20px;
            padding: 28px 20px;
            text-align: center;
            cursor: pointer;
            transition: all .3s;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: block
        }

        .occ-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: inherit;
            opacity: .9;
            border-radius: inherit;
            z-index: 0
        }

        .occ-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, .15)
        }

        .occ-emoji {
            font-size: 2.4rem;
            margin-bottom: 12px;
            position: relative;
            z-index: 1
        }

        .occ-card h6 {
            font-family: 'Playfair Display', serif;
            font-size: .95rem;
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
            z-index: 1
        }

        .occ-card p {
            font-size: .73rem;
            line-height: 1.55;
            margin: 0;
            position: relative;
            z-index: 1
        }

        .occ-count {
            display: inline-block;
            font-size: .65rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 50px;
            margin-top: 9px;
            position: relative;
            z-index: 1
        }

        /* Category colour themes */
        .oc1, .oc2, .oc3, .oc4, .oc5, .oc6 {
            background: linear-gradient(160deg, #FFFFFF, #FDF8F0);
            border: 1px solid rgba(200, 167, 106, .25);
        }

        .oc1 h6, .oc2 h6, .oc3 h6, .oc4 h6, .oc5 h6, .oc6 h6 {
            color: #2A1800;
        }

        .oc1 p, .oc2 p, .oc3 p, .oc4 p, .oc5 p, .oc6 p {
            color: #7A6B55;
        }

        .oc1 .occ-count, .oc2 .occ-count, .oc3 .occ-count,
        .oc4 .occ-count, .oc5 .occ-count, .oc6 .occ-count {
            background: rgba(200, 167, 106, .14);
            color: #A07840;
        }

        .occ-card:hover {
            border-color: #C9A063;
            background: linear-gradient(160deg, #FFFDF8, #FBF1DE);
        }

        .occ-emoji {
            width: 56px;
            height: 56px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: linear-gradient(135deg, #C9A063, #B8863F);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            position: relative;
            z-index: 1;
        }

        /* ══════════════════════════
       FILTER + PRODUCT GRID
    ══════════════════════════ */
        .products-section {
            padding: 20px 0 80px;
            background: #FDF8F0
        }

        .filter-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 36px;
            align-items: center
        }

        .filter-label {
            font-size: .75rem;
            font-weight: 700;
            color: #6B4A20;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-right: 6px
        }

        .filter-btn {
            background: #fff;
            border: 1.5px solid rgba(200, 167, 106, .3);
            color: #6B4A20;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: .8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .22s;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 6px
        }

        .filter-btn:hover {
            border-color: #C9A063;
            color: #A07840;
            background: #FFFAF2
        }

        .filter-btn.active {
            background: #2A1800;
            border-color: #2A1800;
            color: #C9A063
        }

        /* Product cards */
        .prod-card {
            background: #fff;
            border: 1px solid rgba(200, 167, 106, .2);
            border-radius: 18px;
            overflow: hidden;
            transition: all .3s;
            height: 100%
        }




        .btn-add-cart:hover {
            background: #ff8c32;
            transform: translateY(-2px);
        }

        .disabled-btn {
            background: #d1d5db !important;
            color: #666 !important;
            cursor: not-allowed;
        }

        .prod-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .prod-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(200, 167, 106, .18);
            border-color: #C9A063
        }

        .prod-img {
            height: 210px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem
        }

        /* gradient backgrounds per category */
        .pi-wedding {
            background: linear-gradient(135deg, #FFE4EE, #FFB3CC)
        }

        .pi-festival {
            background: linear-gradient(135deg, #FFF3C8, #FFD966)
        }

        .pi-birthday {
            background: linear-gradient(135deg, #FFE0F0, #FF99CC)
        }

        .pi-puja {
            background: linear-gradient(135deg, #FFF0D0, #FFD080)
        }

        .pi-home {
            background: linear-gradient(135deg, #D0F0FF, #80CCFF)
        }

        .pi-navratri {
            background: linear-gradient(135deg, #FFD6F5, #FF80E5)
        }

        .prod-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity .25s
        }

        .prod-card:hover .prod-overlay {
            opacity: 1
        }

        .btn-quickview {
            background: #fff;
            color: #2A1800;
            border: none;
            padding: 9px 20px;
            border-radius: 50px;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all .2s;
            font-family: inherit
        }

        .btn-quickview:hover {
            background: #C9A063;
            color: #0D0800
        }

        .prod-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            font-size: .65rem;
            font-weight: 700;
            padding: 3px 11px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: .4px
        }

        .badge-sale {
            background: #B8863F;
            color: #fff
        }

        .badge-new {
            background: #C9A063;
            color: #0D0800
        }

        .badge-hot {
            background: #DC2626;
            color: #fff
        }

        .badge-best {
            background: #7C3AED;
            color: #fff
        }

        .wish-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .9);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9CA3AF;
            cursor: pointer;
            font-size: .9rem;
            transition: all .2s
        }

        .wish-btn:hover,
        .wish-btn.active {
            background: #FEE2E2;
            color: #EF4444
        }

        .prod-body {
            padding: 18px 16px
        }

        .prod-cat {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #9CA3AF;
            margin-bottom: 5px
        }

        .prod-name {
            font-size: .92rem;
            font-weight: 700;
            color: #2A1800;
            margin-bottom: 5px;
            line-height: 1.4
        }

        .prod-desc {
            font-size: .76rem;
            color: #6B4A20;
            line-height: 1.55;
            margin-bottom: 10px
        }

        .prod-stars {
            color: #F59E0B;
            font-size: .78rem;
            margin-bottom: 10px
        }

        .prod-stars span {
            color: #9CA3AF;
            font-size: .72rem;
            margin-left: 4px
        }

        .prod-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px
        }

        .prod-price {
            font-size: 1.1rem;
            font-weight: 800;
            color: #B8863F
        }

        .prod-mrp {
            font-size: .78rem;
            color: #9CA3AF;
            text-decoration: line-through;
            margin-left: 5px
        }

        .prod-discount {
            font-size: .7rem;
            font-weight: 700;
            color: #16A34A;
            background: #DCFCE7;
            padding: 2px 7px;
            border-radius: 50px;
            margin-left: 5px
        }

        .prod-stock {
            font-size: .68rem;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 50px
        }

        .stock-in {
            background: #DCFCE7;
            color: #166534
        }

        .stock-low {
            background: #FEF9C3;
            color: #854D0E;
        }

        .stock-out {
            background: #FFD6D6;
            color: #D62828;
        }

        .btn-add-cart {
            width: 88%;
            margin: 12px auto 0;

            background: #2A1800;
            color: #C9A063;

            border: none;

            padding: 9px 12px;

            border-radius: 10px;

            font-size: .80rem;
            font-weight: 700;

            cursor: pointer;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;

            transition: all .25s;

            font-family: inherit;
        }

        .btn-add-cart:hover {
            background: #C9A063;
            color: #0D0800
        }

        .btn-add-cart.added {
            background: #16A34A;
            color: #fff
        }

        /* ══════════════════════════
       FEATURED BANNER
    ══════════════════════════ */
        .featured-banner {
            background: linear-gradient(135deg, #0D0800 0%, #2E1200 50%, #1A0900 100%);
            padding: 72px 0;
            position: relative;
            overflow: hidden
        }

        .featured-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 55% 70% at 75% 50%, rgba(200, 167, 106, .07), transparent 60%)
        }

        .feat-card {
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(200, 167, 106, .15);
            border-radius: 18px;
            padding: 24px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
            transition: all .3s
        }

        .feat-card:hover {
            border-color: rgba(200, 167, 106, .32);
            background: rgba(200, 167, 106, .06)
        }

        .feat-ico {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(200, 167, 106, .15), rgba(232, 119, 46, .08));
            border: 1px solid rgba(200, 167, 106, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem
        }

        .feat-card h6 {
            color: #fff;
            font-weight: 700;
            font-size: .9rem;
            margin-bottom: 4px
        }

        .feat-card p {
            color: rgba(255, 255, 255, .43);
            font-size: .77rem;
            line-height: 1.6;
            margin: 0
        }

        .chip-inv {
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

        .sec-h2-inv {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 3.5vw, 2.4rem);
            color: #fff;
            font-weight: 700;
            margin-bottom: 8px
        }

        .sec-h2-inv em {
            color: #C9A063;
            font-style: italic
        }

        .sec-line-inv {
            width: 48px;
            height: 3px;
            background: linear-gradient(90deg, #C9A063, #B8863F);
            border-radius: 3px;
            margin-bottom: 40px
        }

        /* ══════════════════════════
       SWITCH TO WHOLESALE
    ══════════════════════════ */
        .switch-bar {
            background: linear-gradient(135deg, #0D0800, #2E1200);
            padding: 52px 0
        }

        .switch-bar h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 8px
        }

        .switch-bar p {
            color: rgba(255, 255, 255, .55);
            font-size: .88rem;
            margin-bottom: 22px
        }

        .btn-ws-switch {
            background: linear-gradient(135deg, #C9A063, #B8863F);
            color: #0D0800;
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

        .btn-ws-switch:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(232, 119, 46, .4);
            color: #0D0800
        }

        /* ══════════════════════════
       FOOTER
    ══════════════════════════ */
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

        /* CART TOAST */
        .cart-toast {
            position: fixed;
            bottom: 90px;
            left: 20px;
            background: #2A1800;
            color: #C9A063;
            padding: 13px 20px;
            border-radius: 12px;
            font-size: .84rem;
            font-weight: 600;
            border-left: 3px solid #C9A063;
            box-shadow: 0 8px 28px rgba(0, 0, 0, .25);
            z-index: 9999;
            transform: translateX(-120%);
            transition: transform .4s cubic-bezier(.34, 1.56, .64, 1);
            display: flex;
            align-items: center;
            gap: 8px
        }

        .cart-toast.show {
            transform: translateX(0)
        }

        /* CART COUNTER */
        .cart-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            margin-left: 12px;
        }

        .cart-bubble {
            position: relative;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .08);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .25s;
        }

        .cart-bubble:hover {
            background: rgba(200, 167, 106, .12);
            border-color: #C9A063;
        }

        .cart-icon {
            color: #fff;
            font-size: 1.1rem;
        }

        .cart-bubble .bubble {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 19px;
            height: 19px;
            background: #B8863F;
            color: #fff;
            font-size: .58rem;
            font-weight: 800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-dropdown {
            position: absolute;
            top: 58px;
            right: 0;
            width: 320px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .18);
            overflow: hidden;
            display: none;
            z-index: 99999;
        }

        .cart-dropdown.show {
            display: block;
            animation: cartAnim .25s ease;
        }

        @keyframes cartAnim {

            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }

        .cart-head {
            padding: 16px 18px;
            background: #2A1800;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .82rem;
            font-weight: 700;
        }

        .cart-items {
            max-height: 300px;
            overflow-y: auto;
            padding: 14px;
        }

        .empty-cart {
            text-align: center;
            padding: 30px 10px;
            color: #9CA3AF;
        }

        .empty-cart i {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
        }

        .cart-product {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: .8rem;
        }

        .cart-item-card {
            display: flex;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid #F3E7D3;
            align-items: center;
        }

        .cart-item-image {
            width: 70px;
            height: 70px;
            border-radius: 14px;
            overflow: hidden;
            background: #FFF7EA;
            flex-shrink: 0;
            border: 1px solid #F3E7D3;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-name {
            font-size: .92rem;
            font-weight: 700;
            color: #2A1800;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .cart-item-price {
            font-size: 1rem;
            font-weight: 800;
            color: #B8863F;
            margin-bottom: 3px;
        }

        .cart-item-qty {
            font-size: .75rem;
            color: #8B7355;
            font-weight: 500;
        }

        .cart-remove-btn {
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 10px;
            background: #FFF1F1;
            color: #DC2626;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .2s;
        }

        .cart-remove-btn:hover {
            background: #DC2626;
            color: #fff;
        }

        .cart-items {
            max-height: 360px;
            overflow-y: auto;
            padding: 16px;
        }

        .cart-items::-webkit-scrollbar {
            width: 6px;
        }

        .cart-items::-webkit-scrollbar-thumb {
            background: #E2C48D;
            border-radius: 20px;
        }

        .cart-footer {
            padding: 14px;
            border-top: 1px solid #eee;
        }

        .view-cart-btn {
            width: 100%;
            background: #2A1800;
            color: #C9A063;
            border: none;
            padding: 11px;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
        }

        .cart-bubble .bubble {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 18px;
            height: 18px;
            background: #B8863F;
            color: #fff;
            font-size: .58rem;
            font-weight: 800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1
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

        /* hidden filter items */
        .prod-item {
            transition: opacity .3s, transform .3s
        }

        .prod-item.hidden {
            display: none
        }

        @media(max-width:767px) {
            .trust-item {
                flex: 1 1 45%;
                border-right: none;
                border-bottom: 1px solid rgba(200, 167, 106, .15);
                padding: 10px 16px
            }

            .trust-items {
                flex-wrap: wrap
            }

            .kpi-strip {
                flex-wrap: wrap
            }

            .kpi-cell {
                flex: 1 1 45%;
                border-right: none;
                border-bottom: 1px solid rgba(200, 167, 106, .1)
            }

            .hero-cats {
                gap: 7px
            }
        }

        .cart-item-qty-control {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }

        .cart-item-qty-control button {
            width: 28px;
            height: 28px;
            border: none;
            border-radius: 8px;
            background: #2A1800;
            color: #C9A063;
            font-weight: 700;
            cursor: pointer;
        }

        .cart-item-qty-control span {
            font-size: .9rem;
            font-weight: 700;
            min-width: 20px;
            text-align: center;
        }

        .qty-wrap {
            width: 88%;
            margin: 12px auto 0;
        }

        .qty-controller {

            width: 100%;
            height: 46px;

            background: #2A1800;

            border-radius: 10px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 10px;
        }

        .qty-controller button {

            width: 34px;
            height: 34px;

            border: none;
            border-radius: 10px;

            background: #6a3400;
            color: #fff;

            font-size: 20px;
            font-weight: 700;

            display: flex;
            align-items: center;
            justify-content: center;

            cursor: pointer;
        }

        .qty-controller span {
            color: #fff;
            font-size: 18px;
            font-weight: 700;
        }

        .price-filter {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }

        #priceSort {
            padding: 12px 18px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        #productGrid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .prod-item {
            width: 100%;
        }

        .prod-card {
            width: 100%;
            height: 100%;
        }

        @media(max-width:1200px) {
            #productGrid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media(max-width:768px) {
            #productGrid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:576px) {
            #productGrid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>


    <?php include 'includes/header.php'; ?>

   

        <div class="modal fade"
            id="customerLoginModal"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
            tabindex="-1">

            <style>
                #customerLoginModal .modal-content {
                    border: none;
                    border-radius: 22px;
                    overflow: hidden;
                    box-shadow: 0 25px 60px rgba(0,0,0,.25);
                }
                #customerLoginModal .login-modal-header {
                    background: linear-gradient(135deg, #B8863F, #8F6428);
                    padding: 32px 30px 60px;
                    text-align: center;
                    position: relative;
                }
                #customerLoginModal .login-modal-icon {
                    width: 62px;
                    height: 62px;
                    border-radius: 50%;
                    background: rgba(255,255,255,.18);
                    border: 2px solid rgba(255,255,255,.4);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 14px;
                    font-size: 1.6rem;
                    color: #fff;
                }
                #customerLoginModal .login-modal-header h5 {
                    font-family: 'Playfair Display', serif;
                    font-weight: 700;
                    color: #fff;
                    font-size: 1.5rem;
                    margin-bottom: 6px;
                }
                #customerLoginModal .login-modal-header p {
                    color: rgba(255,255,255,.85);
                    font-size: .85rem;
                    margin: 0;
                }
                #customerLoginModal .modal-body {
                    padding: 0 32px 32px;
                    margin-top: -32px;
                }
                #customerLoginModal .login-card {
                    background: #fff;
                    border-radius: 18px;
                    padding: 28px 24px;
                    box-shadow: 0 10px 30px rgba(0,0,0,.08);
                }
                #customerLoginModal label {
                    font-size: .8rem;
                    font-weight: 600;
                    color: #6b5a4a;
                    text-transform: uppercase;
                    letter-spacing: .04em;
                    margin-bottom: 6px;
                    display: block;
                }
                #customerLoginModal .login-input-group {
                    display: flex;
                    align-items: center;
                    border: 1.5px solid #eadfd2;
                    border-radius: 12px;
                    padding: 0 14px;
                    transition: border-color .2s ease, box-shadow .2s ease;
                    background: #FDF8F0;
                }
                #customerLoginModal .login-input-group:focus-within {
                    border-color: #B8863F;
                    box-shadow: 0 0 0 3px rgba(232,119,46,.12);
                    background: #fff;
                }
                #customerLoginModal .login-input-group i {
                    color: #8F6428;
                    font-size: 1rem;
                    margin-right: 10px;
                }
                #customerLoginModal .login-input-group input {
                    border: none;
                    background: transparent;
                    padding: 12px 0;
                    width: 100%;
                    outline: none;
                    font-size: .95rem;
                }
                #customerLoginModal .btn-send-otp,
                #customerLoginModal .btn-verify-otp {
                    background: linear-gradient(135deg, #B8863F, #8F6428);
                    color: #fff;
                    border: none;
                    border-radius: 12px;
                    padding: 14px;
                    font-weight: 600;
                    font-size: .95rem;
                    width: 100%;
                    transition: transform .2s ease, box-shadow .2s ease;
                }
                #customerLoginModal .btn-send-otp:hover,
                #customerLoginModal .btn-verify-otp:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 8px 20px rgba(232,119,46,.35);
                    color: #fff;
                }
                #customerLoginModal .btn-close {
                    position: absolute;
                    top: 18px;
                    right: 18px;
                    background-color: rgba(255,255,255,.25);
                    border-radius: 50%;
                    padding: 8px;
                    opacity: 1;
                    filter: brightness(0) invert(1);
                }
                #customerLoginModal .otp-hint {
                    font-size: .8rem;
                    color: #8a7862;
                    margin-top: 6px;
                }
            </style>

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <div class="login-modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="login-modal-icon">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <h5>Customer Login</h5>
                        <p>Sign in to track orders &amp; checkout faster</p>
                    </div>

                    <div class="modal-body">

                        <div class="login-card">

                        <div id="loginStep">

                            <div class="mb-3">

                                <label>

                                    Full Name

                                </label>

                                <div class="login-input-group">
                                    <i class="bi bi-person"></i>
                                    <input
                                        type="text"
                                        id="customerName"
                                        placeholder="Enter your full name">
                                </div>

                            </div>

                            <div class="mb-3">

                                <label>

                                    Phone Number

                                </label>

                                <div class="login-input-group">
                                    <i class="bi bi-telephone"></i>
                                    <input
                                        type="text"
                                        id="customerPhone"
                                        placeholder="10-digit mobile number">
                                </div>

                            </div>

                            <div class="mb-3">

                                <label>

                                    Email Address

                                </label>

                                <div class="login-input-group">
                                    <i class="bi bi-envelope"></i>
                                    <input
                                        type="email"
                                        id="customerEmail"
                                        placeholder="you@example.com">
                                </div>

                            </div>

                            <button
                                class="btn-send-otp"
                                id="sendOtpBtn">

                                <span id="sendOtpText">

                                    <i class="bi bi-send-fill me-1"></i> Send OTP

                                </span>

                                <span
                                    id="sendOtpLoader"
                                    style="display:none;">


                                    <span
                                        class="spinner-border spinner-border-sm me-2">
                                    </span>

                                    Sending OTP...

                                </span>

                            </button>

                        </div>
                        <div id="otpMessage"></div>
                        <div
                            id="otpStep"
                            style="display:none;">

                            <div class="mb-3">

                                <label>

                                    Enter OTP

                                </label>

                                <div class="login-input-group">
                                    <i class="bi bi-shield-lock"></i>
                                    <input
                                        type="text"
                                        maxlength="6"
                                        id="customerOtp"
                                        placeholder="6-digit code">
                                </div>
                                <p class="otp-hint">Check your email for the verification code</p>

                            </div>

                            <button
                                class="btn-verify-otp"
                                id="verifyOtpBtn">

                                <span id="verifyOtpText">

                                    <i class="bi bi-check2-circle me-1"></i> Verify OTP

                                </span>

                                <span
                                    id="verifyOtpLoader"
                                    style="display:none;">

                                    <span class="spinner-border spinner-border-sm me-2"></span>

                                    Verifying...

                                </span>

                            </button>

                        </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ===========================
     QUICK VIEW MODAL
=========================== -->

        <div class="modal fade"
            id="quickViewModal"
            tabindex="-1"
            aria-hidden="true">

            <div class="modal-dialog modal-xl modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header">

                        <h4 class="modal-title">

                            Product Details

                        </h4>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <!-- Product Image -->

                            <div class="col-lg-5">

                                <img
                                    id="qvImage"
                                    src=""
                                    class="img-fluid rounded shadow w-100">

                            </div>

                            <!-- Product Details -->

                            <div class="col-lg-7">

                                <h2 id="qvName"
                                    class="fw-bold mb-3">
                                </h2>

                                <div class="mb-2">

                                    <span class="badge bg-success">

                                        In Stock

                                    </span>

                                </div>

                                <h3 class="text-danger mb-3">

                                    ₹<span id="qvPrice"></span>

                                </h3>

                                <div class="mb-3">

                                    <strong>

                                        Category :

                                    </strong>

                                    <span id="qvCategory"></span>

                                </div>

                                <div class="mb-3">

                                    <strong>

                                        GST :

                                    </strong>

                                    <span id="qvGST"></span>%

                                </div>

                                <div class="mb-3">

                                    <strong>

                                        Description

                                    </strong>

                                    <p id="qvDescription"
                                        class="mt-2">
                                    </p>

                                </div>

                                <!-- Rating -->

                                <div class="mb-4">

                                    <h5>

                                        Customer Rating

                                    </h5>

                                    <h3 class="text-warning">

                                        ⭐
                                        <span id="qvAverageRating">

                                            0

                                        </span>
                                        /5

                                    </h3>

                                    <small>

                                        (<span id="qvReviewCount">

                                            0

                                        </span> Reviews)

                                    </small>

                                </div>

                                <!-- Add To Cart -->

                                <button
                                    class="btn btn-dark btn-lg"
                                    id="qvAddCart">

                                    <i class="bi bi-cart-plus"></i>

                                    Add To Cart

                                </button>

                            </div>

                        </div>

                        <hr>

                        <h4 class="mb-3">

                            Customer Reviews

                        </h4>

                        <div id="qvReviews">

                            <div class="text-center text-muted">

                                No Reviews Found

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    

    <!-- ══════════════ HERO ══════════════ -->
    <section class="rt-hero">
        <div class="hero-grid"></div>
        <div class="container position-relative" style="z-index:1">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <nav aria-label="breadcrumb" style="margin-bottom:18px">
                        <span style="font-size:.73rem;color:rgba(255,255,255,.35)">
                            <a href="index.php" style="color:rgba(255,255,255,.4);text-decoration:none">Home</a>
                            <span style="margin:0 8px;color:rgba(255,255,255,.2)">/</span>
                            <span style="color:rgba(255,255,255,.65)">Retail Store</span>
                        </span>
                    </nav>
                    <div class="page-badge"><i class="bi bi-bag-heart-fill"></i> <?= htmlspecialchars($settings['retail_hero_badge'] ?? 'For Everyone — Individuals & Families'); ?></div>
                    <h1><?= $settings['retail_hero_title'] ?? 'Shop Beautiful Indian Decorations'; ?></h1>
                    <p class="lead-text"><?= htmlspecialchars($settings['retail_hero_subtitle'] ?? '200+ handpicked decoration products for weddings, festivals, birthdays, puja & every celebration in between — delivered to your door.'); ?></p>
                    <div class="hero-btns">
                        <a href="#products" class="btn-shop-now">
                            <i class="bi bi-bag-heart"></i>Shop Now
                        </a>
                        <a href="index.php#contact" class="btn-outline-light">
                            <i class="bi bi-chat-dots"></i>Custom Order
                        </a>
                    </div>



                    <div class="kpi-strip">
                        <?php

                        $productCount =
                            mysqli_fetch_assoc(
                                mysqli_query(
                                    $conn,
                                    "SELECT COUNT(*) total
FROM products"
                                )
                            );

                        ?>

                        <div class="kpi-cell">

                            <span class="kpi-num">
                                <?= $productCount['total']; ?>+
                            </span>

                            <span class="kpi-lbl">
                                Products
                            </span>

                        </div>
                        <?php

                        $customers =
                            mysqli_fetch_assoc(
                                mysqli_query(
                                    $conn,
                                    "SELECT COUNT(*) total
FROM orders
WHERE status='Delivered'"
                                )
                            );

                        ?>

                        <div class="kpi-cell">

                            <span class="kpi-num">
                                <?= $customers['total']; ?>+
                            </span>

                            <span class="kpi-lbl">
                                Happy Customers
                            </span>

                        </div>
                        <?php

                        $rating =
                            mysqli_fetch_assoc(
                                mysqli_query(
                                    $conn,
                                    "SELECT AVG(rating) avg_rating
FROM product_reviews"
                                )
                            );

                        ?>

                        <div class="kpi-cell">

                            <span class="kpi-num">

                                <?= round(
                                    $rating['avg_rating'],
                                    1
                                ); ?>★

                            </span>

                            <span class="kpi-lbl">
                                Avg Rating
                            </span>

                        </div>
                        <div class="kpi-cell"><span class="kpi-num"><?= htmlspecialchars($settings['retail_kpi4_num'] ?? 'Free'); ?></span><span class="kpi-lbl"><?= htmlspecialchars($settings['retail_kpi4_label'] ?? 'Delivery ₹999+'); ?></span></div>
                    </div>
                </div>

                <!-- Right: top picks preview -->
                <div class="col-lg-5 d-none d-lg-block">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div class="hero-preview-tile">
                            <div class="hpt-icon"><i class="bi bi-flower2"></i></div>
                            <div class="hpt-label">Wedding</div>
                        </div>
                        <div class="hero-preview-tile">
                            <div class="hpt-icon"><i class="bi bi-stars"></i></div>
                            <div class="hpt-label">Festival</div>
                        </div>
                        <div class="hero-preview-tile">
                            <div class="hpt-icon"><i class="bi bi-balloon-heart"></i></div>
                            <div class="hpt-label">Birthday</div>
                        </div>
                        <div class="hero-preview-tile">
                            <div class="hpt-icon"><i class="bi bi-house-heart"></i></div>
                            <div class="hpt-label">Home Decor</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TRUST BAR -->
    <div class="trust-bar">
        <div class="container">
            <div class="trust-items">
                <?php foreach ($retailTrustItems as $item) { ?>
                <div class="trust-item"><i class="bi <?= htmlspecialchars($item['icon'] ?: 'bi-check-circle'); ?>"></i>
                    <div><strong><?= htmlspecialchars($item['title']); ?></strong><small><?= htmlspecialchars($item['subtitle']); ?></small></div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- ══════════════ OCCASION CATEGORIES ══════════════ -->
    <section class="cat-section">
        <div class="container">
            <div class="text-center mb-5">
                <div class="chip">Browse by Occasion</div>
                <h2 class="sec-h2">Shop by <em>Celebration</em></h2>
                <div class="sec-line center"></div>
            </div>
            <div class="occ-grid">

                <?php
                mysqli_data_seek($categories, 0);

                $count = 0;

                while ($cat = mysqli_fetch_assoc($categories)) {

                    $count++;

                    $hidden = ($count > 5) ? 'extra-category d-none' : '';

                    $class = "oc" . $count;

                    if ($count > 6) {
                        $class = "oc6";
                    }
                ?>

                    <a href="javascript:void(0)"
                        class="occ-card <?php echo $class; ?> <?php echo $hidden; ?>"
                        data-filter="<?php echo strtolower($cat['name']); ?>">

                        <div class="occ-emoji">
                            <?php echo $cat['icon']; ?>
                        </div>

                        <h6>
                            <?php echo $cat['name']; ?>
                        </h6>

                    </a>

                <?php } ?>

            </div>
            <div class="text-center mt-4">

                <button id="showMoreCategories"
                    class="btn btn-dark">

                    More Categories

                </button>

                <button id="showLessCategories"
                    class="btn btn-secondary ms-2 d-none">

                    Show Less

                </button>

            </div>
        </div>
    </section>

    <!-- ══════════════ PRODUCTS ══════════════ -->
    <section class="products-section" id="products">
        <div class="container">

            <div class="products-header">
                <div>
                    <h2 class="sec-h2" style="margin-bottom:4px">Our <em>Products</em></h2>
                    <p style="color:#6B4A20;font-size:.84rem;margin:0">Handpicked for quality &amp; authenticity</p>
                </div>
                <div class="filter-bar" style="margin-bottom:0">
                    <div class="price-filter">

                        <select id="priceSort">
                            <option value="">Sort By</option>
                            <option value="low-high">Price: Low → High</option>
                            <option value="high-low">Price: High → Low</option>
                        </select>

                    </div>
                </div>
            </div> <!-- products-header ends -->


            <div id="productGrid">
                <?php

                $product_query = mysqli_query(
                    $conn,
                    "SELECT * FROM products
ORDER BY id DESC"
                );
                while ($product = mysqli_fetch_assoc($product_query)) {





                    $category = strtolower($product['category']);
                ?>

                    <div class="prod-item"
                        data-cat="<?php echo $category; ?>">

                        <?php

                        $sort_price = $product['price'];

                        if ($product['discount'] > 0) {
                            $sort_price =
                                $product['price']
                                - ($product['price'] * $product['discount'] / 100);
                        }

                        ?>

                        <div class="prod-card"
                            data-price="<?php echo $sort_price; ?>">

                            <div class="prod-img pi-<?php echo $category; ?>">

                                <img src="uploads/products/<?php echo $product['image']; ?>"
                                    style="width:100%;
                        height:100%;
                        object-fit:cover;">

                                <?php if ($product['discount'] > 0) { ?>

                                    <span class="prod-badge badge-sale">

                                        <?php echo $product['discount']; ?>% OFF

                                    </span>

                                <?php } ?>

                                <div class="prod-overlay">

                                    <?php

                                    $quick_price = $product['price'];

                                    if ($product['discount'] > 0) {

                                        $quick_price =
                                            $product['price'] -
                                            ($product['price'] * $product['discount'] / 100);
                                    }

                                    ?>

                                    <a
                                        href="product.php?id=<?= $product['id']; ?>"
                                        class="btn-quickview">

                                        <i class="bi bi-eye"></i>

                                        Quick View

                                    </a>

                                </div>

                            </div>

                            <div class="prod-body">

                                <div class="prod-cat">
                                    <?php echo $product['category']; ?>
                                </div>

                                <div class="prod-name">
                                    <?php echo $product['name']; ?>
                                </div>

                                <div class="prod-desc">
                                    <?php echo $product['description']; ?>
                                </div>

                                <div class="prod-footer">

                                    <div>

                                        <?php

                                        if ($product['discount'] > 0) {

                                            $discounted =
                                                $product['price'] -
                                                ($product['price'] * $product['discount'] / 100);

                                        ?>

                                            <span class="prod-price">

                                                ₹<?php echo number_format($discounted); ?>

                                            </span>

                                            <span class="prod-mrp">

                                                ₹<?php echo number_format($product['price']); ?>

                                            </span>

                                        <?php

                                        } else {

                                        ?>

                                            <span class="prod-price">

                                                ₹<?php echo number_format($product['price']); ?>

                                            </span>

                                        <?php

                                        }

                                        ?>

                                    </div>


                                    <?php

                                    $stock = $product['stock'];

                                    if ($stock > 0) {

                                        $class = "stock-in";
                                        $text = "In Stock";
                                    } else {

                                        $class = "stock-out";
                                        $text = "Out Of Stock";
                                    }

                                    ?>

                                    <span class="prod-stock <?php echo $class; ?>">

                                        <?php echo $text; ?>

                                    </span>


                                </div>
                            </div>

                            <div class="prod-footer">



                                <?php if ($product['stock'] > 0) { ?>

                                    <div
                                        class="qty-wrap"
                                        id="qtyWrap<?php echo $product['id']; ?>">

                                        <button
                                            class="btn-add-cart"
                                            id="cartBtn<?php echo $product['id']; ?>"

                                            <?php

                                            $cart_price = $product['price'];

                                            if ($product['discount'] > 0) {

                                                $cart_price =
                                                    $product['price'] -
                                                    ($product['price'] * $product['discount'] / 100);
                                            }

                                            ?>

                                            onclick='addToCart(
<?php echo json_encode([
                                        "id" => $product["id"],
                                        "name" => $product["name"],
                                        "price" => $cart_price,
                                        "image" => $product["image"],
                                        "gst_rate" => $product["gst_rate"]
                                    ]); ?>
)'>

                                            <i class="bi bi-cart-plus"></i>

                                            <span>Add To Cart</span>

                                        </button>

                                    </div>

                                <?php } else { ?>

                                    <button
                                        class="btn-add-cart disabled-btn"
                                        disabled>

                                        Out Of Stock

                                    </button>

                                <?php } ?>



                            </div>

                        </div>

                    </div>

                <?php } ?>

            </div>

            <!-- No results msg -->
            <div id="noResults" style="display:none;text-align:center;padding:60px 0">
                <div style="font-size:3rem;margin-bottom:12px">🔍</div>
                <h5 style="color:#2A1800;margin-bottom:6px">No products in this category</h5>
                <p style="color:#6B4A20;font-size:.85rem">Try a different filter or browse all products.</p>
            </div>

            <div class="text-center mt-5">
                <a href="index.php#contact" style="background:#2A1800;color:#C9A063;padding:13px 32px;border-radius:50px;font-weight:700;font-size:.88rem;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .2s" onmouseover="this.style.background='#C9A063';this.style.color='#0D0800'" onmouseout="this.style.background='#2A1800';this.style.color='#C9A063'">
                    <i class="bi bi-chat-dots-fill"></i>Request Custom Order or Full Catalogue
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════ WHY SHOP WITH US ══════════════ -->
    <section class="featured-banner">
        <div class="container position-relative" style="z-index:1">
            <div class="row align-items-center g-5">
                <div class="col-lg-4">
                    <div class="chip-inv"><?= htmlspecialchars($settings['retail_banner_chip'] ?? 'Why Choose Us'); ?></div>
                    <h2 class="sec-h2-inv"><?= $settings['retail_banner_heading'] ?? 'Shop with Confidence'; ?></h2>
                    <div class="sec-line-inv"></div>
                    <p style="color:rgba(255,255,255,.5);font-size:.88rem;line-height:1.8"><?= htmlspecialchars($settings['retail_banner_text'] ?? '15+ years of expertise in Indian decoration. Every product hand-checked, every order packed with care.'); ?></p>
                    <a href="<?= htmlspecialchars($settings['whatsapp'] ?? 'https://wa.me/919876543210'); ?>" target="_blank" style="background:#25D366;color:#fff;padding:11px 24px;border-radius:50px;font-weight:700;font-size:.84rem;text-decoration:none;display:inline-flex;align-items:center;gap:8px;margin-top:16px;transition:all .2s">
                        <i class="bi bi-whatsapp"></i>Chat for Help
                    </a>
                </div>
                <div class="col-lg-8">
                    <div class="row g-3">
                        <?php foreach ($retailFeatures as $feature) { ?>
                        <div class="col-md-6">
                            <div class="feat-card">
                                <div class="feat-ico"><?= htmlspecialchars($feature['icon']); ?></div>
                                <div>
                                    <h6><?= htmlspecialchars($feature['title']); ?></h6>
                                    <p><?= htmlspecialchars($feature['description']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════ SWITCH TO WHOLESALE ══════════════ -->
    <div class="switch-bar">
        <div class="container text-center">
            <h4><?= htmlspecialchars($settings['retail_switch_heading'] ?? '🏪 Are You a Dealer or Business?'); ?></h4>
            <p><?= htmlspecialchars($settings['retail_switch_text'] ?? 'Get wholesale pricing, bulk discounts up to 40%, and a dedicated account manager for your store or event business.'); ?></p>
            <a href="wholesale.php" class="btn-ws-switch"><i class="bi bi-building-fill"></i>Explore Wholesale Division</a>
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
                        <p class="footer-head">Shop by Occasion</p>
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
                                : ['UPI', 'Cards', 'NetBanking', 'COD', 'EMI'];
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

    <!-- CART TOAST -->
    <div class="cart-toast" id="cartToast"><i class="bi bi-bag-check-fill"></i><span id="cartToastMsg">Added to cart!</span></div>

    <!-- QUICK VIEW MODAL -->
    <div id="qvModal" style="position:fixed;inset:0;z-index:9999;display:none;align-items:center;justify-content:center">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(6px)" onclick="closeQV()"></div>
        <div style="background:#fff;border-radius:20px;padding:32px;max-width:420px;width:90%;position:relative;z-index:1;transform:scale(.9);transition:transform .3s" id="qvBox">
            <button onclick="closeQV()" style="position:absolute;top:14px;right:14px;width:34px;height:34px;border-radius:50%;border:none;background:#F5EDD8;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.9rem;color:#6B4A20"><i class="bi bi-x-lg"></i></button>
            <img id="qvImage"
                style="width:100%;
height:250px;
object-fit:cover;
border-radius:14px;
margin-bottom:15px;">
            <h5 id="qvTitle" style="font-family:'Playfair Display',serif;font-size:1.2rem;color:#2A1800;margin-bottom:8px"></h5>
            <p id="qvDesc" style="color:#6B4A20;font-size:.85rem;line-height:1.7;margin-bottom:14px"></p>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
                <span id="qvPrice" style="font-size:1.4rem;font-weight:800;color:#B8863F"></span>
                <span style="background:#DCFCE7;color:#16A34A;font-size:.72rem;font-weight:700;padding:4px 10px;border-radius:50px">In Stock</span>
            </div>
            <button onclick="closeQV()" style="width:100%;background:#2A1800;color:#C9A063;border:none;padding:12px;border-radius:12px;font-size:.87rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;font-family:'Poppins',sans-serif;transition:all .2s" onmouseover="this.style.background='#C9A063';this.style.color='#0D0800'" onmouseout="this.style.background='#2A1800';this.style.color='#C9A063'">
                <i class="bi bi-cart-plus"></i>Add to Cart
            </button>
        </div>
    </div>

    <a href="https://wa.me/919876543210" class="whatsapp-float" target="_blank"><i class="bi bi-whatsapp"></i></a>
    <button class="btt" id="btt"><i class="bi bi-chevron-up"></i></button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const customerId =
            <?= isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0 ?>;

        const isLoggedIn =
            <?= isset($_SESSION['customer_logged_in']) ? 'true' : 'false' ?>;

        const CART_KEY = "CART_KEY" + customerId;

        let cart = [];

        try {

            cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];

        } catch (e) {

            cart = [];

            localStorage.removeItem(CART_KEY);

        }

        const cartBubble =
            document.getElementById('cartBubble');

        const cartItems =
            document.getElementById('cartItems');

        const cartDropdown =
            document.getElementById('cartDropdown');

        const cartItemCount =
            document.getElementById('cartItemCount');

        const cartIconWrap =
            document.getElementById('cartIconWrap');



        const allProducts = [

            <?php

            $product_query2 =
                mysqli_query(
                    $conn,
                    "SELECT * FROM products"
                );

            while ($p =
                mysqli_fetch_assoc($product_query2)
            ) {

            ?>

                <?php
                $js_price = $p['price'];

                if ($p['discount'] > 0) {
                    $js_price =
                        $p['price'] -
                        ($p['price'] * $p['discount'] / 100);
                }
                ?>

                {
                    id: <?php echo $p['id']; ?>,
                    name: "<?php echo addslashes($p['name']); ?>",
                    price: <?php echo $js_price; ?>,
                    image: "<?php echo $p['image']; ?>",
                    gst_rate: <?php echo $p['gst_rate']; ?>
                },

            <?php } ?>

        ];


        function saveCart() {

            localStorage.setItem(
                CART_KEY,
                JSON.stringify(cart)
            );

        }

        function updateCartCount() {

            let total = 0;

            cart.forEach(item => {

                total += item.qty;

            });

            cartBubble.innerHTML = total;

            cartItemCount.innerHTML =
                total + ' Items';

        }

        function addToCart(product) {

            if (!isLoggedIn) {

                let loginBtn = document.getElementById("openLoginModal");

                if (loginBtn) {
                    loginBtn.click();
                } else {
                    let loginModal = new bootstrap.Modal(
                        document.getElementById("customerLoginModal")
                    );
                    loginModal.show();
                }

                return;

            }

            const existing =
                cart.find(item => item.id == product.id);

            if (existing) {

                existing.qty += 1;

            } else {

                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    image: product.image,
                    gst_rate: product.gst_rate,
                    qty: 1
                });

            }

            saveCart();

            renderCart();

            updateProductQtyUI(product.id);

            cartDropdown.classList.add('show');

        }


        function updateProductQtyUI(productId) {

            const wrap =
                document.getElementById(
                    'qtyWrap' + productId
                );

            const item =
                cart.find(i => i.id == productId);

            if (!item) {

                const product =
                    allProducts.find(
                        p => p.id == productId
                    );

                wrap.innerHTML = `

<button
class="btn-add-cart"

onclick='addToCart(${JSON.stringify(product)})'>

<i class="bi bi-cart-plus"></i>

<span>Add To Cart</span>

</button>

`;

                return;

            }

            wrap.innerHTML = `

<div class="qty-controller">

<button
type="button"
onclick="decreaseQty(${productId})">

−

</button>

<span>${item.qty}</span>

<button
type="button"
onclick="increaseQty(${productId})">

+

</button>

</div>

`;

        }





        function increaseQty(productId) {

            const item = cart.find(i => i.id == productId);

            if (!item) return;

            item.qty++;

            saveCart();

            renderCart();

            updateProductQtyUI(productId);

            cartDropdown.classList.add('show');
        }

        function decreaseQty(productId) {

            const item = cart.find(i => i.id == productId);

            if (!item) return;

            item.qty--;

            // IF QTY BECOMES 0
            // REMOVE PRODUCT & SHOW ADD TO CART BUTTON

            if (item.qty <= 0) {

                cart = cart.filter(i => i.id != productId);

                saveCart();

                renderCart();

                updateProductQtyUI(productId);

                return;
            }

            saveCart();

            renderCart();

            updateProductQtyUI(productId);
        }






        function removeCartItem(index) {

            const productId = cart[index].id;

            cart.splice(index, 1);

            saveCart();

            renderCart();

            updateProductQtyUI(productId);

        }

        function renderCart() {

            if (cart.length === 0) {

                cartItems.innerHTML = `

    <div class="empty-cart">
        <i class="bi bi-cart-x"></i>
        <p>Your cart is empty</p>
    </div>

    `;

                updateCartCount();

                document.querySelectorAll('.qty-wrap')
                    .forEach(wrap => {

                        const id =
                            wrap.id.replace('qtyWrap', '');

                        updateProductQtyUI(id);

                    });

                return;

            }

            let html = '';

            cart.forEach((item, index) => {

                html += `

<div class="cart-item-card">

<div class="cart-item-image">

<img src="uploads/products/${item.image}">

</div>

<div class="cart-item-details">

<div class="cart-item-name">
${item.name}
</div>

<div class="cart-item-price">
₹${item.price}
</div>

<div class="cart-item-qty-control">

<button onclick="decreaseQty(${item.id})">

−

</button>

<span>${item.qty}</span>

<button onclick="increaseQty(${item.id})">

+

</button>

</div>

</div>

<button
class="cart-remove-btn"
onclick="removeCartItem(${index})">

<i class="bi bi-trash"></i>

</button>

</div>

`;

            });

            cartItems.innerHTML = html;

            updateCartCount();

        }

        cartIconWrap.onclick = function(e) {

            e.stopPropagation();

            cartDropdown.classList.toggle('show');

        };


        document.addEventListener(
            'click',
            function(e) {

                if (

                    !cartDropdown.contains(e.target) &&
                    !cartIconWrap.contains(e.target)

                ) {

                    cartDropdown.classList.remove('show');

                }

            }
        );

        cartDropdown.addEventListener(
            'click',
            function(e) {

                e.stopPropagation();

            }
        );

        renderCart();

        updateCartCount();

        cart.forEach(item => {
            updateProductQtyUI(item.id);
        });
    </script>
    <script>
        const showMoreBtn =
            document.getElementById('showMoreCategories');

        const showLessBtn =
            document.getElementById('showLessCategories');

        showMoreBtn.addEventListener('click', function() {

            document
                .querySelectorAll('.extra-category')
                .forEach(function(item) {

                    item.classList.remove('d-none');

                });

            showMoreBtn.classList.add('d-none');

            showLessBtn.classList.remove('d-none');

        });

        showLessBtn.addEventListener('click', function() {

            document
                .querySelectorAll('.extra-category')
                .forEach(function(item) {

                    item.classList.add('d-none');

                });

            showLessBtn.classList.add('d-none');

            showMoreBtn.classList.remove('d-none');

        });
    </script>
    <script>
        document.querySelectorAll('.occ-card').forEach(card => {

            card.addEventListener('click', function() {

                let category =
                    this.dataset.filter;

                document.querySelectorAll('.prod-item')
                    .forEach(product => {

                        if (
                            product.dataset.cat === category
                        ) {

                            product.style.display = 'block';

                        } else {

                            product.style.display = 'none';

                        }

                    });

                document
                    .getElementById('products')
                    .scrollIntoView({
                        behavior: 'smooth'
                    });

            });

        });

        // Support deep-linking into a filtered category view, e.g.
        // retail.php?category=Wedding (used by the homepage footer's
        // "Occasions" links) - applies the same filter logic above,
        // just triggered from the URL instead of a click.
        (function() {
            let params = new URLSearchParams(window.location.search);
            let requestedCategory = params.get('category');

            if (!requestedCategory) {
                return;
            }

            requestedCategory = requestedCategory.toLowerCase();

            document.querySelectorAll('.prod-item').forEach(product => {
                product.style.display =
                    (product.dataset.cat === requestedCategory) ? 'block' : 'none';
            });
        })();
    </script>
    <script>
        const showAllProducts =
            document.getElementById("showAllProducts");

        if (showAllProducts) {

            showAllProducts.addEventListener("click", function() {

                document.querySelectorAll(".prod-item")
                    .forEach(function(product) {

                        product.style.display = "block";

                    });

            });

        }
    </script>



    <div id="recaptcha-container"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            <?php if (!isset($_SESSION['customer_logged_in'])) { ?>

                let loginModal = new bootstrap.Modal(
                    document.getElementById("customerLoginModal")
                );

                loginModal.show();

            <?php } ?>

        });
    </script>

    <script>
        document.getElementById("sendOtpBtn").addEventListener("click", function() {

            let name = document.getElementById("customerName").value.trim();
            let phone = document.getElementById("customerPhone").value.trim();
            let email = document.getElementById("customerEmail").value.trim();

            if (name == "" || phone == "" || email == "") {
                alert("Please fill all fields.");
                return;
            }

            let formData = new FormData();

            formData.append("name", name);
            formData.append("phone", phone);
            formData.append("email", email);

            document.getElementById("sendOtpBtn").disabled = true;

            document.getElementById("sendOtpText").style.display = "none";

            document.getElementById("sendOtpLoader").style.display = "inline";

            fetch("customer/send-otp.php", {

                    method: "POST",

                    body: formData

                })

                .then(response => response.json())

                .then(data => {
                    document.getElementById("sendOtpBtn").disabled = false;

                    document.getElementById("sendOtpText").style.display = "inline";

                    document.getElementById("sendOtpLoader").style.display = "none";

                    document.getElementById("loginStep").style.display = "none";

                    document.getElementById("otpStep").style.display = "block";

                    document.getElementById("otpMessage").innerHTML = `

<div class="alert alert-success">

<i class="bi bi-check-circle-fill me-2"></i>

OTP has been sent successfully to

<b>${email}</b>

</div>

`;

                    if (data.success) {
                        document.getElementById("loginStep").style.display = "none";

                        document.getElementById("otpStep").style.display = "block";
                    }

                })

                .catch(error => {
                    document.getElementById("sendOtpBtn").disabled = false;

                    document.getElementById("sendOtpText").style.display = "inline";

                    document.getElementById("sendOtpLoader").style.display = "none";

                    console.error(error);

                    alert("Unable to connect.");

                });

        });
        document.getElementById("verifyOtpBtn").addEventListener("click", function() {

            let otp = document.getElementById("customerOtp").value.trim();

            let email = document.getElementById("customerEmail").value.trim();

            if (otp == "") {
                alert("Please enter OTP");
                return;
            }
            document.getElementById("verifyOtpBtn").disabled = true;

            document.getElementById("verifyOtpText").style.display = "none";

            document.getElementById("verifyOtpLoader").style.display = "inline";
            let formData = new FormData();

            formData.append("email", email);

            formData.append("otp", otp);

            fetch("customer/verify-otp.php", {

                    method: "POST",

                    body: formData

                })

                .then(response => response.json())

                .then(data => {

                    document.getElementById("verifyOtpBtn").disabled = false;

                    document.getElementById("verifyOtpText").style.display = "inline";

                    document.getElementById("verifyOtpLoader").style.display = "none";

                    if (data.success) {

                        document.getElementById("otpMessage").innerHTML = `
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>
            Login Successful
            <br>
            Redirecting...
        </div>
        `;

                        setTimeout(function() {

                            location.reload();

                        }, 1500);

                    } else {

                        alert(data.message);

                    }

                })

                .catch(error => {

                    document.getElementById("verifyOtpBtn").disabled = false;

                    document.getElementById("verifyOtpText").style.display = "inline";

                    document.getElementById("verifyOtpLoader").style.display = "none";

                    console.log(error);

                    alert("Verification Failed");

                });

        });

        let loginButton = document.getElementById("openLoginModal");

        if (loginButton) {

            loginButton.addEventListener("click", function() {

                let modal = new bootstrap.Modal(document.getElementById("customerLoginModal"));

                modal.show();

            });

        }
    </script>
    <script>
        function loadQuickView(productId){

fetch("ajax/get-product.php?id=" + productId)

.then(response=>response.json())

.then(data=>{

console.log(data);

        if(!data.status){

            alert(data.message);

            return;

        }

        document.getElementById("qvImage").src =
            "uploads/products/" + data.product.image;

        document.getElementById("qvName").innerHTML =
            data.product.name;

        document.getElementById("qvPrice").innerHTML =
            data.product.price;

        document.getElementById("qvCategory").innerHTML =
            data.product.category;

        document.getElementById("qvGST").innerHTML =
            data.product.gst_rate;

        document.getElementById("qvDescription").innerHTML =
            data.product.description;

        document.getElementById("qvAverageRating").innerHTML =
            data.average;

        document.getElementById("qvReviewCount").innerHTML =
            data.reviews.length;

        let reviewHTML = "";

        if(data.reviews.length==0){

            reviewHTML =
            "<div class='alert alert-warning'>No Reviews Yet</div>";

        }
        else{

            data.reviews.forEach(function(review){

                reviewHTML += `

                <div class="border rounded p-3 mb-3">

                    <strong>${review.customer_name}</strong>

                    <div class="text-warning">

                        ${"⭐".repeat(review.rating)}

                    </div>

                    <div>

                        ${review.review}

                    </div>

                    <small class="text-muted">

                        ${review.review_date}

                    </small>

                </div>

                `;

            });

        }

        document.getElementById("qvReviews").innerHTML =
            reviewHTML;

        document.getElementById("qvAddCart").onclick=function(){

            addToCart({

                id:data.product.id,

                name:data.product.name,

                image:data.product.image,

                price:data.product.price,

                gst_rate:data.product.gst_rate

            });

        };

        new bootstrap.Modal(
            document.getElementById("quickViewModal")
        ).show();

    })

    .catch(function(error){

        console.log(error);

        alert("Unable to load product.");

    });

}
    </script>
    
</body>

</html>
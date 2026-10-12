<?php

$newOrders =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT id FROM orders
WHERE admin_seen='0'"
        )
    );

$newWholesale =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT id FROM wholesale_requests
WHERE admin_seen='0'"
        )
    );

$newQuotes =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT id FROM quote_requests
WHERE admin_seen='0'"
        )
    );

$newReturns =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT id
         FROM return_requests
         WHERE status='Pending'"
        )
    );

$newReviews =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT id
         FROM product_reviews
         WHERE admin_seen='0'"
        )
    );

?>


<style>
/* ==================================================
   CONTENT MANAGEMENT DROPDOWN
   (embedded here so it renders consistently on every
   admin page that includes this sidebar file, instead
   of depending on individual pages linking css/sidebar.css)
================================================== */

.cms-menu-item {
    width: 100%;
    margin-bottom: 10px;
}

.cms-menu-toggle {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 18px;
    background: transparent;
    border: none;
    border-radius: 14px;
    color: #ffffff;
    font-size: 16px;
    font-family: inherit;
    text-align: left;
    white-space: nowrap;
    cursor: pointer;
    transition: background 0.3s ease, color 0.3s ease;
}

.cms-menu-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    white-space: nowrap;
}

.cms-menu-left > i {
    flex-shrink: 0;
}

.cms-menu-toggle:hover {
    background: #7A2E0D;
}

.cms-menu-toggle.cms-expanded,
.cms-menu-toggle.active {
    background: #7A2E0D;
    color: #ffffff;
}

.cms-arrow {
    margin-left: auto;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.cms-arrow.cms-arrow-open {
    transform: rotate(180deg);
}

.cms-submenu {
    list-style: none;
    padding: 0;
    margin: 0;
    display: none;
}

.cms-submenu.cms-open {
    display: block;
    margin-top: 10px;
}

.cms-submenu li {
    margin: 4px 0;
}

.cms-submenu li a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 16px 11px 22px;
    border-radius: 12px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 16px;
    text-decoration: none;
    transition: background 0.25s ease, color 0.25s ease, padding-left 0.25s ease;
}

.cms-submenu li a i {
    width: 20px;
    text-align: center;
    flex-shrink: 0;
}

.cms-submenu li a:hover {
    background: rgba(255, 255, 255, 0.10);
    color: #ffffff;
    padding-left: 27px;
}

.cms-submenu li a.active {
    background: #7A2E0D;
    color: #ffffff;
    font-weight: 600;
}
</style>

<div class="sidebar" id="adminSidebar">

    <div>

        <h2 class="logo">
            Shreeji Decoration
        </h2>

        <p class="admin-text">
            Admin Panel
        </p>

    </div>

    <ul class="menu">

        <!-- DASHBOARD -->
        <li>

            <a href="dashboard.php"
                class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">

                <i class="bi bi-grid"></i>

                Dashboard

            </a>

        </li>


        <!-- PRODUCTS -->
        <li>

            <a href="products.php"
                class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">

                <i class="bi bi-box"></i>

                Products

                <?php if (
                    isset($_SESSION['products_seen']) &&
                    $_SESSION['products_seen'] == false &&
                    isset($_SESSION['low_stock_count']) &&
                    $_SESSION['low_stock_count'] > 0
                ) { ?>

                    <span style="
                    background:red;
                    color:#fff;
                    padding:2px 8px;
                    border-radius:30px;
                    font-size:11px;
                    margin-left:auto;
                    ">

                        <?php echo $_SESSION['low_stock_count']; ?>

                    </span>

                <?php } ?>

            </a>

        </li>

        <li>

            <a href="categories.php"
                class="<?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">

                <i class="bi bi-tags"></i>

                Categories

            </a>

        </li>

        <!-- ORDERS -->
        <li>

            <a href="orders.php"
                class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">

                <i class="bi bi-cart"></i>

                Orders

                <?php if ($newOrders > 0) { ?>

                    <span
                        style="
background:red;
color:white;
padding:2px 8px;
border-radius:30px;
font-size:11px;
margin-left:auto;
">

                        <?= $newOrders ?>

                    </span>

                <?php } ?>



            </a>

        </li>


        <!-- USERS -->
        <li>

            <a href="users.php"
                class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">

                <i class="bi bi-people"></i>

                Users

            </a>

        </li>


        <!-- WHOLESALE -->
        <li>

            <a href="wholesale.php"
                class="<?= basename($_SERVER['PHP_SELF']) == 'wholesale.php' ? 'active' : '' ?>">

                <i class="bi bi-building"></i>

                Wholesale

                <?php if ($newWholesale > 0) { ?>

                    <span
                        style="
background:orange;
color:white;
padding:2px 8px;
border-radius:30px;
font-size:11px;
margin-left:auto;
">

                        <?= $newWholesale ?>

                    </span>

                <?php } ?>

            </a>

        </li>


        <!-- GALLERY -->
        <li>

            <a href="gallery.php"
                class="<?= basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : '' ?>">

                <i class="bi bi-images"></i>

                Gallery

            </a>

        </li>

        <li>
            <a href="quotes.php">
                <i class="bi bi-chat-dots"></i>
                Quote Requests

                <?php if ($newQuotes > 0) { ?>

                    <span
                        style="
background:#0d6efd;
color:white;
padding:2px 8px;
border-radius:30px;
font-size:11px;
margin-left:auto;
">

                        <?= $newQuotes ?>

                    </span>

                <?php } ?>
            </a>
        </li>

        <li>

            <a href="return-requests.php"

                class="<?= basename($_SERVER['PHP_SELF']) == 'return-requests.php' ? 'active' : '' ?>">

                <i class="bi bi-arrow-counterclockwise"></i>

                Return Requests

                <?php if ($newReturns > 0) { ?>

                    <span
                        style="
background:#dc3545;
color:white;
padding:2px 8px;
border-radius:30px;
font-size:11px;
margin-left:auto;
">

                        <?= $newReturns ?>

                    </span>

                <?php } ?>

            </a>

        </li>

        <?php

        $newReviews =
            mysqli_num_rows(
                mysqli_query(
                    $conn,
                    "SELECT id
FROM product_reviews
WHERE status='Pending'"
                )
            );

        ?>

        <li>

            <a href="reviews.php"
                class="<?= basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : '' ?>">

                <i class="bi bi-star-fill"></i>

                Reviews

                <?php if ($newReviews > 0) { ?>

                    <span
                        style="
background:red;
color:white;
padding:2px 8px;
border-radius:30px;
font-size:11px;
margin-left:auto;
">

                        <?= $newReviews ?>

                    </span>

                <?php } ?>

            </a>

        </li>

       

        <?php

$currentPage = basename($_SERVER['PHP_SELF']);

$cmsPages = [
    'about-content.php',
    'faq.php',
    'privacy-policy.php',
    'terms.php',
    'refund-policy.php',
    'wholesale-content.php',
    'retail-content.php'
];

$isCmsPage = in_array($currentPage, $cmsPages);

?>

<li class="cms-menu-item">

    <!-- CMS MAIN BUTTON -->
    <button
        type="button"
        class="cms-menu-toggle <?= $isCmsPage ? 'active' : '' ?>"
        id="cmsMenuToggle"
        aria-expanded="<?= $isCmsPage ? 'true' : 'false' ?>">

        <span class="cms-menu-left">

            <i class="bi bi-file-earmark-text"></i>

            <span>
                Site Content
            </span>

        </span>

        <i
            class="bi bi-chevron-down cms-arrow <?= $isCmsPage ? 'cms-arrow-open' : '' ?>"
            id="cmsArrow">
        </i>

    </button>


    <!-- CMS DROPDOWN -->
    <ul
        class="cms-submenu <?= $isCmsPage ? 'cms-open' : '' ?>"
        id="cmsMenu">

        <li>

            <a
                href="about-content.php"
                class="<?= $currentPage === 'about-content.php' ? 'active' : '' ?>">

                <i class="bi bi-info-circle"></i>

                <span>About Us</span>

            </a>

        </li>

        <li>

            <a
                href="wholesale-content.php"
                class="<?= $currentPage === 'wholesale-content.php' ? 'active' : '' ?>">

                <i class="bi bi-building"></i>

                <span>Wholesale Page</span>

            </a>

        </li>

        <li>

            <a
                href="retail-content.php"
                class="<?= $currentPage === 'retail-content.php' ? 'active' : '' ?>">

                <i class="bi bi-bag-heart"></i>

                <span>Retail Page</span>

            </a>

        </li>


        <li>

            <a
                href="faq.php"
                class="<?= $currentPage === 'faq.php' ? 'active' : '' ?>">

                <i class="bi bi-question-circle"></i>

                <span>FAQ</span>

            </a>

        </li>


        <li>

            <a
                href="privacy-policy.php"
                class="<?= $currentPage === 'privacy-policy.php' ? 'active' : '' ?>">

                <i class="bi bi-shield-check"></i>

                <span>Privacy Policy</span>

            </a>

        </li>

        <li>

            <a
                href="terms.php"
                class="<?= $currentPage === 'terms.php' ? 'active' : '' ?>">

                <i class="bi bi-file-earmark-text"></i>

                <span>Terms & Conditions</span>

            </a>

        </li>

        <li>

            <a
                href="refund-policy.php"
                class="<?= $currentPage === 'refund-policy.php' ? 'active' : '' ?>">

                <i class="bi bi-arrow-return-left"></i>

                <span>Refund Policy</span>

            </a>

        </li>

    </ul>

</li>


        <!-- SETTINGS -->
        <li>

            <a href="settings.php"
                class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">

                <i class="bi bi-gear"></i>

                Settings

            </a>

        </li>


        <!-- LOGOUT -->
        <li>

            <a href="logout.php">

                <i class="bi bi-box-arrow-right"></i>

                Logout

            </a>

        </li>
    </ul>

</div>
<script>

document.addEventListener("DOMContentLoaded",function(){

const btn=document.getElementById("cmsMenuToggle");

const menu=document.getElementById("cmsMenu");

const arrow=document.getElementById("cmsArrow");

if(!btn || !menu){

return;

}

btn.onclick=function(){

menu.classList.toggle("cms-open");

btn.classList.toggle("cms-expanded");

arrow.classList.toggle("cms-arrow-open");

};

});

</script>

<script>

/* ==========================================================
   SIDEBAR SCROLL POSITION PERSISTENCE
   Every admin page is a full page load, which normally resets
   the sidebar's scroll to the top. This remembers where the
   user was scrolled to (per browser tab) and restores it on
   the next page, so clicking a link near the bottom (e.g.
   "Quote Requests") doesn't jump back to the top.
========================================================== */

(function() {

    var sidebar = document.getElementById("adminSidebar");

    if (!sidebar) {
        return;
    }

    var STORAGE_KEY = "adminSidebarScrollTop";

    var savedScroll = sessionStorage.getItem(STORAGE_KEY);

    if (savedScroll !== null) {
        sidebar.scrollTop = parseInt(savedScroll, 10) || 0;
    }

    sidebar.addEventListener("scroll", function() {
        sessionStorage.setItem(STORAGE_KEY, sidebar.scrollTop);
    });

    // Also capture the scroll position right before navigating away,
    // in case the "scroll" event's last value hasn't fired yet.
    sidebar.querySelectorAll("a").forEach(function(link) {
        link.addEventListener("click", function() {
            sessionStorage.setItem(STORAGE_KEY, sidebar.scrollTop);
        });
    });

})();

</script>
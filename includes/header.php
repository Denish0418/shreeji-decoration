<?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>
  <?php
    require_once 'config/db.php';
    ?>
  <!-- TOPBAR -->
  <div class="topbar">
      <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div class="d-flex gap-4">
              <span><i class="bi bi-telephone-fill"></i>+91 98765 43210</span>
              <span><i class="bi bi-envelope-fill"></i>info@shreejidecoration.com</span>
          </div>
          <div class="d-flex gap-3 align-items-center">
              <span><i class="bi bi-clock-fill"></i>Mon–Sun 9AM–8PM</span>
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
  </div>

  <!-- NAVBAR -->
  <nav class="main-nav" id="mainNav">

      <div class="container d-flex align-items-center justify-content-between">

          <!-- LOGO -->

          <a href="index.php"
              class="d-flex align-items-center gap-2"
              style="text-decoration:none">

              <div class="brand-icon" style="width:44px;height:44px;border-radius:50%;overflow:hidden;flex-shrink:0;">
                  <img src="uploads/site-content/<?= !empty($settings['logo']) ? htmlspecialchars($settings['logo']) : 'logo.jpg' ?>" alt="Shreeji Decoration" style="width:100%;height:100%;object-fit:cover;display:block;">
              </div>

              <div>
                  <div class="brand-name">
                      Shreeji Decoration
                  </div>

                  <div class="brand-tagline">
                      सजाएँ हर पल, हर जश्न
                  </div>
              </div>

          </a>

          <!-- NAV LINKS -->

          <div id="navMenu"
              class="align-items-center gap-1 ms-auto">

              <a class="nav-link <?= ($currentPage == 'index.php') ? 'active' : ''; ?>" href="index.php">
                  Home
              </a>

              <a class="nav-link"
                  href="index.php#about">
                  About
              </a>

              <a class="nav-link <?= ($currentPage == 'wholesale.php') ? 'active' : ''; ?>" href="wholesale.php">
                  Wholesale
              </a>

              <a class="nav-link <?= ($currentPage == 'retail.php') ? 'active' : ''; ?>" href="retail.php">
                  Retail
              </a>

              <a class="nav-link"
                  href="index.php#gallery">
                  Gallery
              </a>

              <a class="nav-link"
                  href="index.php#testimonials">
                  Reviews
              </a>

          </div>

          <!-- ALWAYS-VISIBLE ACTIONS (Get Quote, Login, Cart) -->
          <!-- These never collapse into the hamburger menu, regardless
               of screen width, so the cart is always reachable. -->
          <div class="d-flex align-items-center gap-1 ms-auto">

              <!-- GET QUOTE -->

              <a class="btn-quote ms-2"
                  href="index.php#contact">

                  Get Quote

                  <i class="bi bi-arrow-right-short"></i>

              </a>

              <!-- CART -->
              <?php

                        if (isset($_SESSION['customer_logged_in'])) {

                            $profileLetter = strtoupper(substr($_SESSION['customer_name'], 0, 1));

                        ?>

                          <div class="dropdown ms-3">

                              <button
                                  class="btn btn-profile dropdown-toggle"
                                  type="button"
                                  data-bs-toggle="dropdown">

                                  <?= $profileLetter; ?>

                              </button>

                              <ul class="dropdown-menu dropdown-menu-end profile-dropdown">

                                  <li class="px-3 py-2">

                                      <strong>

                                          <?= htmlspecialchars($_SESSION['customer_name']); ?>

                                      </strong>

                                      <br>

                                      <small>

                                          <?= htmlspecialchars($_SESSION['customer_email']); ?>

                                      </small>

                                  </li>

                                  <li>
                                      <hr class="dropdown-divider">
                                  </li>

                                  <li>

                                      <a
                                          class="dropdown-item"
                                          href="customer-orders.php">

                                          <i class="bi bi-box-seam me-2"></i>

                                          My Orders

                                      </a>

                                  </li>

                                  <li>

                                      <a
                                          class="dropdown-item"
                                          href="customer/profile.php">

                                          <i class="bi bi-person me-2"></i>

                                          My Profile

                                      </a>

                                  </li>

                                  <li>

                                      <a
                                          class="dropdown-item text-danger"
                                          href="customer/logout.php">

                                          <i class="bi bi-box-arrow-right me-2"></i>

                                          Logout

                                      </a>

                                  </li>

                              </ul>

                          </div>

                      <?php

                        } else {

                        ?>

                          <button
                              class="btn btn-profile ms-3"
                              id="openLoginModal">

                              <i class="bi bi-person"></i>

                          </button>

                      <?php

                        }

                        ?>
                      <div class="cart-wrapper">

                          <!-- ICON -->

                          <div class="cart-bubble"
                              id="cartIconWrap">

                              <i class="bi bi-bag-fill cart-icon"></i>

                              <span class="bubble" id="cartBubble">0</span>

                          </div>

                          <!-- DROPDOWN -->

                          <div class="cart-dropdown"
                              id="cartDropdown">

                              <div class="cart-head">

                                  <span>
                                      Shopping Bag
                                  </span>

                                  <span id="cartItemCount">
                                      0 Items
                                  </span>

                              </div>

                              <div class="cart-items"
                                  id="cartItems">

                                  <div class="empty-cart">

                                      <i class="bi bi-bag-x"></i>

                                      <p>Your bag is empty</p>

                                  </div>

                              </div>

                              <div class="cart-footer">

                                  <a href="cart.php" class="view-cart-btn d-flex align-items-center justify-content-center text-decoration-none">
                                      View Cart
                                  </a>

                              </div>

                          </div>

                      </div>

          </div>

          <!-- MOBILE BUTTON -->

          <button class="navbar-toggler custom-nav-toggler"
              id="navToggle">

              <i class="bi bi-list"></i>

          </button>

      </div>

  </nav>
  <?php

    if (isset($_SESSION['clear_cart'])) {

    ?>

      <script>
          localStorage.removeItem(
              "CART_KEY<?= $_SESSION['clear_cart'] ?>"
          );
      </script>

  <?php

        unset($_SESSION['clear_cart']);
    }

    ?>

    <script>
        /* ==========================================================
           SHARED CART WIDGET
           Powers the cart icon/dropdown in the navbar above, on
           every page that includes this header. Pages that also let
           customers add items (retail.php, product.php) may define
           their own add-to-cart logic separately, but all of them
           share this same rendering/toggle behavior so the icon
           count and dropdown always work correctly everywhere.
        ========================================================== */
        (function() {

            const customerId =
                <?= isset($_SESSION['customer_id']) ? (int) $_SESSION['customer_id'] : 0 ?>;

            window.CART_KEY = "CART_KEY" + customerId;

            let cart = [];

            try {
                cart = JSON.parse(localStorage.getItem(window.CART_KEY)) || [];
            } catch (e) {
                cart = [];
                localStorage.removeItem(window.CART_KEY);
            }

            window.cart = cart;

            const cartBubble = document.getElementById('cartBubble');
            const cartItemsEl = document.getElementById('cartItems');
            const cartItemCount = document.getElementById('cartItemCount');
            const cartDropdown = document.getElementById('cartDropdown');
            const cartIconWrap = document.getElementById('cartIconWrap');

            window.saveCart = function() {
                localStorage.setItem(window.CART_KEY, JSON.stringify(window.cart));
            };

            window.updateCartCount = function() {
                let total = 0;
                window.cart.forEach(item => total += item.qty);
                if (cartBubble) cartBubble.innerHTML = total;
                if (cartItemCount) cartItemCount.innerHTML = total + ' Items';
            };

            window.renderCart = function() {

                if (!cartItemsEl) return;

                if (window.cart.length === 0) {
                    cartItemsEl.innerHTML = `
                        <div class="empty-cart">
                            <i class="bi bi-cart-x"></i>
                            <p>Your cart is empty</p>
                        </div>
                    `;
                    window.updateCartCount();
                    return;
                }

                let html = '';

                window.cart.forEach((item, index) => {
                    html += `
                        <div class="cart-item-card">
                            <div class="cart-item-image">
                                <img src="uploads/products/${item.image}">
                            </div>
                            <div class="cart-item-details">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">₹${item.price}</div>
                                <div class="cart-item-qty-control">
                                    <button onclick="window.cartDecreaseQty(${item.id})">−</button>
                                    <span>${item.qty}</span>
                                    <button onclick="window.cartIncreaseQty(${item.id})">+</button>
                                </div>
                            </div>
                            <button class="cart-remove-btn" onclick="window.cartRemoveItem(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                });

                cartItemsEl.innerHTML = html;
                window.updateCartCount();
            };

            window.cartIncreaseQty = function(id) {
                let item = window.cart.find(i => i.id == id);
                if (item) {
                    item.qty += 1;
                    window.saveCart();
                    window.renderCart();
                }
            };

            window.cartDecreaseQty = function(id) {
                let item = window.cart.find(i => i.id == id);
                if (item) {
                    item.qty -= 1;
                    if (item.qty <= 0) {
                        window.cart = window.cart.filter(i => i.id != id);
                    }
                    window.saveCart();
                    window.renderCart();
                }
            };

            window.cartRemoveItem = function(index) {
                window.cart.splice(index, 1);
                window.saveCart();
                window.renderCart();
            };

            if (cartIconWrap && cartDropdown) {

                cartIconWrap.onclick = function(e) {
                    e.stopPropagation();
                    cartDropdown.classList.toggle('show');
                };

                document.addEventListener('click', function(e) {
                    if (!cartDropdown.contains(e.target) && !cartIconWrap.contains(e.target)) {
                        cartDropdown.classList.remove('show');
                    }
                });
            }

            window.renderCart();

        })();

        /* ==========================================================
           MOBILE NAV TOGGLE
           Makes the hamburger button open/close the nav menu (which
           includes the cart icon) on narrower screens, on every page
           that uses this shared header.
        ========================================================== */
        (function() {
            let navToggle = document.getElementById('navToggle');
            let navMenu = document.getElementById('navMenu');

            if (navToggle && navMenu) {
                navToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('open');
                });
            }
        })();
    </script>
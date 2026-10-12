/* ═══════════════════════════════
   SHREEJI DECORATION – SCRIPTS
═══════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

  /* ─── 1. NAVBAR SCROLL EFFECT ─── */
  const nav = document.getElementById('mainNav');
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);
  });

  /* ─── 2. ACTIVE NAV LINK on scroll ─── */
  const sections = document.querySelectorAll('section[id], div[id]');
  const navLinks = document.querySelectorAll('.nav-link');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        navLinks.forEach(l => {
          l.classList.toggle('active', l.getAttribute('href') === '#' + e.target.id);
        });
      }
    });
  }, { threshold: 0.4 });
  sections.forEach(s => observer.observe(s));

  /* ─── 3. SMOOTH SCROLL ─── */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        const offset = 75;
        const top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
        // Close mobile menu
        const menu = document.getElementById('navMenu');
        if (menu.classList.contains('show')) {
          new bootstrap.Collapse(menu).hide();
        }
      }
    });
  });

  /* ─── 4. BACK TO TOP ─── */
  const backTop = document.getElementById('backTop');
  window.addEventListener('scroll', () => {
    backTop.classList.toggle('visible', window.scrollY > 400);
  });
  backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* ─── 5. FADE-IN ANIMATION ─── */
  const fadeEls = document.querySelectorAll(
    '.ws-benefit-card, .ws-product-card, .retail-card, .testi-card, .feat-card, .ci-item'
  );
  fadeEls.forEach(el => el.classList.add('fade-in'));
  const fadeObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        fadeObserver.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });
  fadeEls.forEach(el => fadeObserver.observe(el));

  /* ─── 6. RETAIL FILTER ─── */
  const filterBtns = document.querySelectorAll('#retailFilter .btn-filter');
  const retailItems = document.querySelectorAll('.retail-item');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filter;
      retailItems.forEach(item => {
        const show = filter === 'all' || item.dataset.cat === filter;
        item.style.transition = 'opacity .3s, transform .3s';
        if (show) {
          item.style.display = '';
          setTimeout(() => { item.style.opacity = '1'; item.style.transform = 'scale(1)'; }, 10);
        } else {
          item.style.opacity = '0';
          item.style.transform = 'scale(.95)';
          setTimeout(() => { item.style.display = 'none'; }, 300);
        }
      });
    });
  });

  /* ─── 7. WISHLIST TOGGLE ─── */
  document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const icon = btn.querySelector('i');
      if (icon.classList.contains('bi-heart')) {
        icon.classList.replace('bi-heart', 'bi-heart-fill');
        btn.style.color = '#e74c3c';
        showToast('Added to wishlist ❤️');
      } else {
        icon.classList.replace('bi-heart-fill', 'bi-heart');
        btn.style.color = '';
        showToast('Removed from wishlist');
      }
    });
  });

  /* ─── 8. ADD TO CART ─── */
  document.querySelectorAll('.btn-retail-add').forEach(btn => {
    btn.addEventListener('click', () => {
      const name = btn.closest('.retail-card').querySelector('h6').textContent;
      btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Added!';
      btn.style.background = '#27AE60';
      setTimeout(() => {
        btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Add to Cart';
        btn.style.background = '';
      }, 2000);
      showToast(`"${name}" added to cart 🛒`);
    });
  });

  /* ─── 9. CONTACT FORM ─── */
  const form = document.getElementById('contactForm');

  if (form) {

    form.addEventListener('submit', function () {

      const btn =
        form.querySelector('button[type=submit]');

      btn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

      btn.disabled = true;

    });

  }


  /* ─── 10. TOAST NOTIFICATION ─── */
  function showToast(msg) {
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.style.cssText = 'position:fixed;bottom:80px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;align-items:center;gap:8px;';
      document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.style.cssText = `
      background: #1A1208; color: #fff; padding: 10px 22px;
      border-radius: 25px; font-size: 13.5px; font-weight: 500;
      box-shadow: 0 4px 20px rgba(0,0,0,.3);
      opacity: 0; transform: translateY(10px);
      transition: all .3s; border-left: 3px solid #E8772E;
    `;
    toast.textContent = msg;
    container.appendChild(toast);
    requestAnimationFrame(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    });
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(() => toast.remove(), 350);
    }, 3000);
  }

  /* ─── 11. COUNTER ANIMATION ─── */
  const counters = document.querySelectorAll('.stat-num');
  const counterObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const el = e.target;
        const target = parseInt(el.textContent) || 0;
        const suffix = el.textContent.replace(/[0-9]/g, '');
        let count = 0;
        const step = Math.ceil(target / 60);
        const timer = setInterval(() => {
          count = Math.min(count + step, target);
          el.textContent = count + suffix;
          if (count >= target) clearInterval(timer);
        }, 25);
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.6 });
  counters.forEach(c => counterObserver.observe(c));

  /* ─── 12. NEWSLETTER SUBSCRIBE ─── */
  const subBtn = document.querySelector('.btn-saffron');
  if (subBtn) {
    subBtn.addEventListener('click', () => {
      const input = subBtn.previousElementSibling;
      if (input.value && input.value.includes('@')) {
        subBtn.textContent = '✓ Done!';
        subBtn.style.background = '#27AE60';
        input.value = '';
        setTimeout(() => {
          subBtn.textContent = 'Subscribe';
          subBtn.style.background = '';
        }, 3000);
      } else {
        input.style.borderColor = '#e74c3c';
        setTimeout(() => input.style.borderColor = '', 2000);
      }
    });
  }

});
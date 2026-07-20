/* =========================================================
   SAMSAR · 01-home.js
   Self-contained vanilla JS for the home page.
   No imports, no globals — wrapped in an IIFE.
   ========================================================= */
(function () {
  "use strict";

  const prefersReduced =
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const isFinePointer = window.matchMedia("(pointer: fine)").matches;

  // ---------------------------------------------------------
  // 0. PAGE TRANSITION
  //    Handled by samsar-transitions.js — no need to duplicate.
  //    The overlay is already in the HTML and the JS auto-initializes.
  // ---------------------------------------------------------

  // ---------------------------------------------------------
  // 0b. HERO HEADLINE — split into word spans for reveal
  // ---------------------------------------------------------
  const headline = document.querySelector("[data-split]");
  if (headline && !prefersReduced) {
    const html = headline.innerHTML;
    // wrap each word in <span class="word"><span>word</span></span>
    headline.innerHTML = html.replace(/(<[^>]+>|[^\s<]+)/g, (m) => {
      if (m.startsWith("<")) return m;
      return `<span class="word"><span>${m}</span></span> `;
    });
    setTimeout(() => headline.classList.add("is-revealed"), 350);
  } else if (headline) {
    headline.classList.add("is-revealed");
  }

  // ---------------------------------------------------------
  // 0c. HERO SCROLL HIDE (Villa slides up, text hides)
  // ---------------------------------------------------------
  const heroBg = document.querySelector(".hero-cinematic .hero-bg");
  const scrollHide = document.querySelectorAll("[data-scroll-hide]");
  window.addEventListener("scroll", () => {
    const s = window.scrollY;
    if (s < window.innerHeight) {
      heroBg.style.transform = `translateY(${s * 0.5}px)`;
      scrollHide.forEach(el => el.style.opacity = 1 - (s / 400));
    }
  }, { passive: true });

  // ---------------------------------------------------------
  // 0d. STAT COUNT-UP
  // ---------------------------------------------------------
  const stats = document.querySelectorAll(".stat .num");
  if (stats.length && !prefersReduced) {
    const countIO = new IntersectionObserver((es, o) => {
      es.forEach(e => {
        if (!e.isIntersecting) return;
        const el = e.target;
        const text = el.textContent;
        const match = text.match(/^(\d+)(.*)$/);
        if (!match) { o.unobserve(el); return; }
        const target = parseInt(match[1], 10);
        const suffix = match[2] || "";
        let cur = 0;
        const dur = 1400;
        const start = performance.now();
        function step(now) {
          const t = Math.min((now - start) / dur, 1);
          const eased = 1 - Math.pow(1 - t, 3);
          cur = Math.round(target * eased);
          el.innerHTML = cur + suffix;
          if (t < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
        o.unobserve(el);
      });
    }, { threshold: 0.5 });
    stats.forEach(s => countIO.observe(s));
  }

  // ---------------------------------------------------------
  // 0f. ACTIONS SECTION line-draw on enter
  // ---------------------------------------------------------
  const actionsSec = document.querySelector(".actions");
  if (actionsSec) {
    const ioA = new IntersectionObserver((es, o) => es.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add("is-in-view"); o.unobserve(e.target); }
    }), { threshold: 0.15 });
    ioA.observe(actionsSec);
  }

  // ---------------------------------------------------------
  // 1. Single rAF loop — everything that animates per-frame
  //    subscribes here. One loop = predictable perf budget.
  // ---------------------------------------------------------
  const subscribers = new Set();
  function tick() {
    subscribers.forEach((fn) => fn());
    requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);

  // ---------------------------------------------------------
  // 2. Custom cursor (lerp 0.18 for outer ring, 0.32 for dot)
  // ---------------------------------------------------------
  if (isFinePointer && !prefersReduced) {
    const ring = document.querySelector(".cursor");
    const dot = document.querySelector(".cursor-dot");
    if (ring && dot) {
      const target = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
      const ringPos = { x: target.x, y: target.y };
      const dotPos = { x: target.x, y: target.y };

      window.addEventListener("mousemove", (e) => {
        target.x = e.clientX;
        target.y = e.clientY;
      }, { passive: true });

      subscribers.add(() => {
        ringPos.x += (target.x - ringPos.x) * 0.18;
        ringPos.y += (target.y - ringPos.y) * 0.18;
        dotPos.x  += (target.x - dotPos.x)  * 0.32;
        dotPos.y  += (target.y - dotPos.y)  * 0.32;
        ring.style.transform =
          `translate3d(${ringPos.x - 18}px, ${ringPos.y - 18}px, 0)`;
        dot.style.transform =
          `translate3d(${dotPos.x - 2.5}px, ${dotPos.y - 2.5}px, 0)`;
      });

      const hoverables = document.querySelectorAll(
        'a, button, input, select, textarea, [data-cursor="hover"]'
      );
      hoverables.forEach((el) => {
        el.addEventListener("mouseenter", () => {
          ring.classList.add("is-hover");
          dot.classList.add("is-hover");
        });
        el.addEventListener("mouseleave", () => {
          ring.classList.remove("is-hover");
          dot.classList.remove("is-hover");
        });
      });

      document.addEventListener("mouseleave", () => {
        ring.style.opacity = "0";
        dot.style.opacity = "0";
      });
      document.addEventListener("mouseenter", () => {
        ring.style.opacity = "1";
        dot.style.opacity = "1";
      });
    }
  }

  // ---------------------------------------------------------
  // 3. Parallax hero image (depth factor 0.18 = slower than doc)
  // ---------------------------------------------------------
  const heroImg = document.querySelector(".hero-bg img[data-parallax]") || document.querySelector("[data-parallax]");
  if (heroImg && !prefersReduced) {
    let active = false;
    let scrollY = 0;
    let current = 0;
    const factor = parseFloat(heroImg.dataset.parallax) || 0.12;

    const io = new IntersectionObserver(
      (entries) => entries.forEach((e) => (active = e.isIntersecting)),
      { rootMargin: "100px" }
    );
    io.observe(heroImg);

    window.addEventListener("scroll", () => {
      scrollY = window.scrollY;
    }, { passive: true });

    subscribers.add(() => {
      if (!active) return;
      current += (scrollY * factor - current) * 0.12;
      heroImg.style.transform = `translate3d(0, ${current}px, 0)`;
    });
  }

  // ---------------------------------------------------------
  // 4. Scroll reveals — IntersectionObserver, staggered
  // ---------------------------------------------------------
  const revealEls = document.querySelectorAll(".reveal");
  if (revealEls.length) {
    const ro = new IntersectionObserver(
      (entries, observer) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const delay = entry.target.dataset.delay || 0;
            entry.target.style.transitionDelay = `${delay}ms`;
            entry.target.classList.add("is-in");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15, rootMargin: "0px 0px -60px 0px" }
    );
    revealEls.forEach((el) => ro.observe(el));
  }

  // ---------------------------------------------------------
  // 5. Sticky nav — add border when scrolled
  // ---------------------------------------------------------
  const nav = document.querySelector(".nav");
  if (nav) {
    let lastState = false;
    window.addEventListener("scroll", () => {
      const scrolled = window.scrollY > (window.innerHeight * 0.6);
      if (scrolled !== lastState) {
        nav.classList.toggle("is-scrolled", scrolled);
        lastState = scrolled;
      }
    }, { passive: true });
  }

  // ---------------------------------------------------------
  // 6. Modal — open / close / focus-trap / Esc
  // ---------------------------------------------------------
  const modal = document.getElementById("modal");
  const openers = document.querySelectorAll("[data-open-modal]");
  if (modal && openers.length) {
    const closers = modal.querySelectorAll("[data-close-modal]");
    let lastFocused = null;

    const openModal = () => {
      lastFocused = document.activeElement;
      modal.classList.add("is-open");
      modal.setAttribute("aria-hidden", "false");
      document.body.style.overflow = "hidden";
      const firstField = modal.querySelector("input, select, textarea, button");
      if (firstField) setTimeout(() => firstField.focus(), 420);
    };
    const closeModal = () => {
      modal.classList.remove("is-open");
      modal.setAttribute("aria-hidden", "true");
      document.body.style.overflow = "";
      if (lastFocused) lastFocused.focus();
    };

    openers.forEach((o) => o.addEventListener("click", (e) => {
      e.preventDefault(); openModal();
    }));
    closers.forEach((c) => c.addEventListener("click", closeModal));

    document.addEventListener("keydown", (e) => {
      if (!modal.classList.contains("is-open")) return;
      if (e.key === "Escape") closeModal();
      if (e.key === "Tab") {
        const focusables = modal.querySelectorAll(
          'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (!focusables.length) return;
        const first = focusables[0];
        const last  = focusables[focusables.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault(); last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault(); first.focus();
        }
      }
    });

    // Form fake-submit
    const form = modal.querySelector("form");
    if (form) {
      form.addEventListener("submit", (e) => {
        e.preventDefault();
        const btn = form.querySelector("button[type='submit']");
        if (btn) {
          const orig = btn.textContent;
          btn.textContent = window.t ? window.t('home.modal.sent') : "Sent — we'll be in touch ✓";
          btn.disabled = true;
          setTimeout(() => {
            closeModal();
            btn.textContent = orig;
            btn.disabled = false;
            form.reset();
          }, 1400);
        }
      });
    }
  }

  // ---------------------------------------------------------
  // 7. Cities horizontal scroll controls
  // ---------------------------------------------------------
  const track = document.querySelector(".cities-track");
  const prev = document.querySelector("[data-cities-prev]");
  const next = document.querySelector("[data-cities-next]");
  if (track && prev && next) {
    const step = () => track.clientWidth * 0.6;
    prev.addEventListener("click", () => track.scrollBy({ left: -step(), behavior: "smooth" }));
    next.addEventListener("click", () => track.scrollBy({ left:  step(), behavior: "smooth" }));
  }

  // ---------------------------------------------------------
  // 8. Mobile nav toggle now handled by scripts/responsive-nav.js
  // ---------------------------------------------------------

  // ---------------------------------------------------------
  // 9. Year stamp
  // ---------------------------------------------------------
  const yr = document.querySelector("[data-year]");
  if (yr) yr.textContent = new Date().getFullYear();
})();

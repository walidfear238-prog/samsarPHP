/* =========================================================
   SAMSAR · Page Transition Controller
   Drop this into any page's JS — handles navigation + animation routing.
   ========================================================= */
(function () {
  "use strict";

  const prefersReduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (prefersReduced) return; // Bail — respect system setting

  // ---------- CONFIG ----------
  const ENTER_DELAY = 560;
  const LEAVE_DELAY = 400;

  // ---------- MINIMAL PLACEHOLDER OVERLAY (unused visually) ----------
  const overlay = document.createElement("div");
  overlay.className = "page-transition";
  overlay.setAttribute("aria-hidden", "true");
  overlay.innerHTML = '<span class="layer"></span><span class="layer"></span><span class="layer"></span>';
  document.body.appendChild(overlay);

  // ---------- PAGE ENTRY ----------
  requestAnimationFrame(() => {
    document.body.classList.add("is-entering");
    setTimeout(() => {
      document.body.classList.remove("is-entering", "is-leaving", "is-transiting");
    }, ENTER_DELAY);
  });

  // ---------- NAVIGATION HANDLER ----------
  document.addEventListener("click", (e) => {
    const link = e.target.closest("a");
    if (!link) return;

    const href = link.getAttribute("href");
    if (
      !href ||
      href.startsWith("#") ||
      href.startsWith("mailto:") ||
      href.startsWith("tel:") ||
      link.target === "_blank" ||
      link.hasAttribute("data-open-modal")
    ) return;

    const url = new URL(href, location.href);
    if (url.origin !== location.origin) return;

    e.preventDefault();
    document.body.classList.add("is-transiting", "is-leaving");
    setTimeout(() => {
      location.href = link.href;
    }, LEAVE_DELAY);
  });

  // ---------- KEYBOARD: Enter on focused link ----------
  document.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && document.activeElement.tagName === "A") {
      document.activeElement.click();
    }
  });

  // ---------- EXPOSE MINIMAL API ----------
  window.SamsarTransition = {
    play(callback) {
      document.body.classList.add("is-entering");
      setTimeout(() => {
        document.body.classList.remove("is-entering", "is-transiting", "is-leaving");
        if (callback) callback();
      }, ENTER_DELAY);
    },
    leave(callback) {
      document.body.classList.add("is-transiting", "is-leaving");
      setTimeout(() => {
        if (callback) callback();
      }, LEAVE_DELAY);
    },
  };
})();

/* =========================================================
   SAMSAR · responsive-nav.js
   Powers the mobile hamburger drawer for the standard `.nav`
   header used across the marketing pages. Pairs with
   styles/responsive-nav.css. Safe to include on every page —
   it simply does nothing if no `.nav-toggle` is present.
   ========================================================= */
(function () {
  "use strict";

  function init() {
    var headers = document.querySelectorAll("header.nav");
    headers.forEach(function (header) {
      var toggle = header.querySelector(".nav-toggle");
      if (!toggle) return;

      function closeMenu() {
        header.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
        document.body.classList.remove("nav-scroll-lock");
      }

      function openMenu() {
        header.classList.add("is-open");
        toggle.setAttribute("aria-expanded", "true");
        document.body.classList.add("nav-scroll-lock");
      }

      toggle.addEventListener("click", function (e) {
        e.stopPropagation();
        if (header.classList.contains("is-open")) closeMenu();
        else openMenu();
      });

      // Close after choosing a destination
      header.querySelectorAll(".nav-links a, .nav-right a").forEach(function (link) {
        link.addEventListener("click", closeMenu);
      });

      // Close on outside click
      document.addEventListener("click", function (e) {
        if (header.classList.contains("is-open") && !header.contains(e.target)) {
          closeMenu();
        }
      });

      // Close on escape
      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && header.classList.contains("is-open")) {
          closeMenu();
          toggle.focus();
        }
      });

      // Close the drawer if the viewport is resized back up to desktop
      var mq = window.matchMedia("(min-width: 901px)");
      var onChange = function (m) {
        if (m.matches) closeMenu();
      };
      if (mq.addEventListener) mq.addEventListener("change", onChange);
      else if (mq.addListener) mq.addListener(onChange);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

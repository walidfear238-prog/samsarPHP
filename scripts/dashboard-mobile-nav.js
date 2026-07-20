/* =========================================================
   SAMSAR · dashboard-mobile-nav.js
   Turns the dashboard sidebar into an off-canvas drawer on
   narrow screens. Injects a small fixed top bar (brand +
   hamburger) so every dashboard-area page gets working mobile
   navigation without editing each page's markup individually.
   Pairs with the mobile rules in styles/dashboard-shell.css.
   ========================================================= */
(function () {
  "use strict";

  function init() {
    var shell = document.querySelector(".dashboard-shell");
    var sidebar = document.querySelector(".dashboard-sidebar");
    if (!shell || !sidebar || !shell.parentNode) return;

    var topbar = document.createElement("div");
    topbar.className = "dash-mobile-topbar";
    topbar.innerHTML =
      '<a class="dash-mobile-brand" href="index.php">' +
      '<svg viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">' +
      '<path d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z"></path>' +
      "</svg>" +
      "<span>SAMSAR</span></a>" +
      '<button type="button" class="dash-mobile-toggle" aria-label="Open menu" aria-expanded="false"><span></span></button>';

    var backdrop = document.createElement("div");
    backdrop.className = "dash-mobile-backdrop";

    shell.parentNode.insertBefore(topbar, shell);
    shell.parentNode.insertBefore(backdrop, shell);

    var toggle = topbar.querySelector(".dash-mobile-toggle");

    function close() {
      sidebar.classList.remove("is-open");
      toggle.classList.remove("is-open");
      backdrop.classList.remove("is-open");
      toggle.setAttribute("aria-expanded", "false");
      document.body.classList.remove("dash-scroll-lock");
    }

    function open() {
      sidebar.classList.add("is-open");
      toggle.classList.add("is-open");
      backdrop.classList.add("is-open");
      toggle.setAttribute("aria-expanded", "true");
      document.body.classList.add("dash-scroll-lock");
    }

    toggle.addEventListener("click", function () {
      if (sidebar.classList.contains("is-open")) close();
      else open();
    });

    backdrop.addEventListener("click", close);

    sidebar.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", close);
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && sidebar.classList.contains("is-open")) {
        close();
        toggle.focus();
      }
    });

    var mq = window.matchMedia("(min-width: 1101px)");
    var onChange = function (m) {
      if (m.matches) close();
    };
    if (mq.addEventListener) mq.addEventListener("change", onChange);
    else if (mq.addListener) mq.addListener(onChange);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

/* ============================================================
   SAMSAR — language-switcher.js
   - Reads translations from window.translations (translations.js)
   - Applies the saved language on page load
   - Exposes window.setLanguage(lang) for the nav buttons
   - Exposes window.t(key) / window.getCurrentLang() so other
     scripts (chat.js, dashboard.js, property cards, etc.) can
     translate text they render dynamically.
   - Toggles dir="rtl" + class "lang-ar" for Arabic
   - Persists choice in localStorage AND syncs it to the PHP
     session via php/lang.php (fire-and-forget, non-blocking)
   - Fires a "samsar:langchange" event on document so any script
     that builds DOM at runtime can re-render its own text.
   None of this touches existing markup, CSS classes or IDs —
   it only reads/writes a small "lang-switcher" widget that this
   same file injects into the page.
   ============================================================ */

(function () {
  const SUPPORTED = ["en", "fr", "ar"];
  const STORAGE_KEY = "samsar_lang";
  const RTL_LANGS = ["ar"];

  function getSavedLanguage() {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved && SUPPORTED.includes(saved)) return saved;
    return "en"; // default, matches existing lang="en" on <html>
  }

  let currentLang = getSavedLanguage();

  /**
   * Returns the translated string for `key` in the current language,
   * falling back to English, then to the key itself (and optional
   * `fallback` text supplied by the caller).
   */
  function t(key, fallback) {
    const dict = (window.translations && window.translations[currentLang]) || {};
    if (dict[key] !== undefined) return dict[key];
    const enDict = (window.translations && window.translations.en) || {};
    if (enDict[key] !== undefined) return enDict[key];
    return fallback !== undefined ? fallback : key;
  }

  function getCurrentLang() {
    return currentLang;
  }

  function applyTranslations(lang) {
    const dict = (window.translations && window.translations[lang]) || {};

    // Plain text content
    document.querySelectorAll("[data-i18n]").forEach((el) => {
      const key = el.getAttribute("data-i18n");
      if (dict[key] !== undefined) {
        el.textContent = dict[key];
      }
    });

    // placeholder="..."
    document.querySelectorAll("[data-i18n-placeholder]").forEach((el) => {
      const key = el.getAttribute("data-i18n-placeholder");
      if (dict[key] !== undefined) el.setAttribute("placeholder", dict[key]);
    });

    // title="..."
    document.querySelectorAll("[data-i18n-title]").forEach((el) => {
      const key = el.getAttribute("data-i18n-title");
      if (dict[key] !== undefined) el.setAttribute("title", dict[key]);
    });

    // aria-label="..."
    document.querySelectorAll("[data-i18n-aria-label]").forEach((el) => {
      const key = el.getAttribute("data-i18n-aria-label");
      if (dict[key] !== undefined) el.setAttribute("aria-label", dict[key]);
    });

    // value="..." (submit buttons / inputs)
    document.querySelectorAll("[data-i18n-value]").forEach((el) => {
      const key = el.getAttribute("data-i18n-value");
      if (dict[key] !== undefined) el.setAttribute("value", dict[key]);
    });

    // <title> document title
    document.querySelectorAll("[data-i18n-doctitle]").forEach((el) => {
      const key = el.getAttribute("data-i18n-doctitle");
      if (dict[key] !== undefined) document.title = dict[key];
    });

    // meta name="description" content="..."
    document.querySelectorAll("[data-i18n-content]").forEach((el) => {
      const key = el.getAttribute("data-i18n-content");
      if (dict[key] !== undefined) el.setAttribute("content", dict[key]);
    });
  }

  function applyDirection(lang) {
    const isRtl = RTL_LANGS.includes(lang);
    document.documentElement.setAttribute("dir", isRtl ? "rtl" : "ltr");
    document.documentElement.setAttribute("lang", lang);
    document.body.classList.toggle("lang-ar", isRtl);
  }

  function syncToServer(lang) {
    // Best-effort sync so PHP ($_SESSION['lang']) agrees with the client.
    // Existing PHP logic is untouched; this just calls the new lang.php.
    fetch("php/lang.php?lang=" + encodeURIComponent(lang), {
      method: "GET",
      credentials: "same-origin",
    }).catch(() => {
      /* non-critical: UI already switched client-side */
    });
  }

  function setActiveButtonState(lang) {
    document.querySelectorAll(".lang-switcher [data-lang]").forEach((btn) => {
      btn.classList.toggle("active", btn.getAttribute("data-lang") === lang);
    });
  }

  window.setLanguage = function (lang) {
    if (!SUPPORTED.includes(lang)) return;
    currentLang = lang;
    localStorage.setItem(STORAGE_KEY, lang);
    applyDirection(lang);
    applyTranslations(lang);
    setActiveButtonState(lang);
    syncToServer(lang);
    document.dispatchEvent(new CustomEvent("samsar:langchange", { detail: { lang: lang } }));
  };

  // Public helpers for other scripts that render text dynamically
  // (chat.js, dashboard.js, property cards, notifications, etc.)
  window.t = t;
  window.getCurrentLang = getCurrentLang;
  window.applyTranslations = function () {
    applyTranslations(currentLang);
  };

  function buildSwitcherMarkup() {
    return (
      '<div class="lang-switcher" data-i18n-widget aria-label="Language selector">' +
      '<button type="button" data-lang="en" onclick="setLanguage(\'en\')">EN</button>' +
      '<button type="button" data-lang="fr" onclick="setLanguage(\'fr\')">FR</button>' +
      '<button type="button" data-lang="ar" onclick="setLanguage(\'ar\')">AR</button>' +
      "</div>"
    );
  }

  function injectSwitcher() {
    // Drop the widget into whichever nav container exists on this page,
    // without touching the existing links inside it.
    const target =
      document.querySelector("header.nav .nav-inner") ||
      document.querySelector(".dashboard-sidebar .dashboard-nav") ||
      document.querySelector(".form-head") ||
      document.querySelector("nav.nav-links");
    if (!target) return;
    if (target.querySelector(".lang-switcher")) return; // already injected
    const wrapper = document.createElement("div");
    wrapper.innerHTML = buildSwitcherMarkup();
    target.appendChild(wrapper.firstChild);
  }

  document.addEventListener("DOMContentLoaded", function () {
    injectSwitcher();
    const lang = getSavedLanguage();
    currentLang = lang;
    applyDirection(lang);
    applyTranslations(lang);
    setActiveButtonState(lang);
  });
})();

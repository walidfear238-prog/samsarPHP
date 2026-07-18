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

  const LANG_LABELS = {
    en: { code: "EN", name: "English" },
    fr: { code: "FR", name: "Français" },
    ar: { code: "AR", name: "العربية" },
  };

  function setActiveButtonState(lang) {
    document.querySelectorAll(".lang-switcher [data-lang]").forEach((btn) => {
      const isActive = btn.getAttribute("data-lang") === lang;
      btn.classList.toggle("active", isActive);
      const option = btn.closest('[role="option"]');
      if (option) option.setAttribute("aria-selected", isActive ? "true" : "false");
    });
    // Reflect the current language on the compact trigger button, if present.
    document.querySelectorAll(".lang-switcher-trigger .lang-switcher-code").forEach((el) => {
      el.textContent = (LANG_LABELS[lang] && LANG_LABELS[lang].code) || lang.toUpperCase();
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
    const options = SUPPORTED.map(function (lang) {
      const label = LANG_LABELS[lang] || { code: lang.toUpperCase(), name: lang };
      return (
        '<li role="option" aria-selected="false">' +
        '<button type="button" class="lang-switcher-option" data-lang="' + lang + '">' +
        '<span class="lang-switcher-option-code">' + label.code + "</span>" +
        '<span class="lang-switcher-option-name">' + label.name + "</span>" +
        '<svg class="lang-switcher-check" viewBox="0 0 14 10" aria-hidden="true"><path d="M1 5l4 4 8-8" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
        "</button>" +
        "</li>"
      );
    }).join("");

    return (
      '<div class="lang-switcher" data-i18n-widget>' +
      '<button type="button" class="lang-switcher-trigger" aria-haspopup="listbox" aria-expanded="false" aria-label="Change language">' +
      '<svg class="lang-switcher-globe" viewBox="0 0 16 16" aria-hidden="true"><circle cx="8" cy="8" r="6.4" fill="none" stroke="currentColor" stroke-width="1.2"/><path d="M1.6 8h12.8M8 1.6c1.8 1.8 2.8 4 2.8 6.4s-1 4.6-2.8 6.4c-1.8-1.8-2.8-4-2.8-6.4S6.2 3.4 8 1.6Z" fill="none" stroke="currentColor" stroke-width="1.2"/></svg>' +
      '<span class="lang-switcher-code">EN</span>' +
      '<svg class="lang-switcher-chevron" viewBox="0 0 10 6" aria-hidden="true"><path d="M1 1l4 4 4-4" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
      "</button>" +
      '<ul class="lang-switcher-menu" role="listbox" aria-label="Available languages">' +
      options +
      "</ul>" +
      "</div>"
    );
  }

  function closeSwitcher(widget) {
    widget.classList.remove("is-open");
    const trigger = widget.querySelector(".lang-switcher-trigger");
    if (trigger) trigger.setAttribute("aria-expanded", "false");
  }

  function openSwitcher(widget) {
    document.querySelectorAll(".lang-switcher.is-open").forEach(function (open) {
      if (open !== widget) closeSwitcher(open);
    });
    widget.classList.add("is-open");
    const trigger = widget.querySelector(".lang-switcher-trigger");
    if (trigger) trigger.setAttribute("aria-expanded", "true");
  }

  function wireSwitcher(widget) {
    const trigger = widget.querySelector(".lang-switcher-trigger");
    if (trigger) {
      trigger.addEventListener("click", function (e) {
        e.stopPropagation();
        if (widget.classList.contains("is-open")) closeSwitcher(widget);
        else openSwitcher(widget);
      });
    }
    widget.querySelectorAll(".lang-switcher-option").forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.stopPropagation();
        closeSwitcher(widget);
        window.setLanguage(btn.getAttribute("data-lang"));
        if (trigger) trigger.focus();
      });
    });
    widget.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        closeSwitcher(widget);
        if (trigger) trigger.focus();
      }
    });
  }

  // Close any open switcher when clicking outside it, or when it loses focus entirely.
  document.addEventListener("click", function (e) {
    document.querySelectorAll(".lang-switcher.is-open").forEach(function (widget) {
      if (!widget.contains(e.target)) closeSwitcher(widget);
    });
  });
  document.addEventListener("focusin", function (e) {
    document.querySelectorAll(".lang-switcher.is-open").forEach(function (widget) {
      if (!widget.contains(e.target)) closeSwitcher(widget);
    });
  });

  function injectSwitcher() {
    // Drop the widget into whichever nav container exists on this page,
    // without touching the existing links inside it. On the main site
    // header, nest it inside .nav-right so it sits inline with the other
    // nav items (same row, same spacing/gap) instead of the header's own
    // grid wrapping it onto a new line.
    const headerInner = document.querySelector("header.nav .nav-inner");
    const target =
      (headerInner && (headerInner.querySelector(".nav-right") || headerInner)) ||
      document.querySelector(".dashboard-sidebar .dashboard-nav") ||
      document.querySelector(".form-head") ||
      document.querySelector("nav.nav-links");
    if (!target) return;
    if (target.querySelector(".lang-switcher")) return; // already injected
    const wrapper = document.createElement("div");
    wrapper.innerHTML = buildSwitcherMarkup();
    const widget = wrapper.firstChild;
    const beforeNode = target.querySelector(".nav-toggle");
    if (beforeNode) target.insertBefore(widget, beforeNode);
    else target.appendChild(widget);
    wireSwitcher(widget);
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

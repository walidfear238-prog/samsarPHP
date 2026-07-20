/* =========================================================
   SAMSAR · chat-mobile.js
   On narrow screens the chat layout shows either the
   conversation list OR the open thread, never both at once
   (matches common messaging-app UX). Watches #chat-window for
   changes made by scripts/chat.js and toggles a `.show-chat`
   class on `.chat-layout` accordingly, plus wires the back
   button. Pure enhancement — chat.js is untouched.
   ========================================================= */
(function () {
  "use strict";

  function init() {
    var layout = document.querySelector(".chat-layout");
    var winEl = document.getElementById("chat-window");
    var backBtn = document.querySelector(".chat-back-btn");
    if (!layout || !winEl || !backBtn) return;

    backBtn.addEventListener("click", function () {
      layout.classList.remove("show-chat");
    });

    var observer = new MutationObserver(function () {
      var hasOpenThread = !!winEl.querySelector(".chat-head");
      layout.classList.toggle("show-chat", hasOpenThread);
    });
    observer.observe(winEl, { childList: true });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();

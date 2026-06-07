(function () {
  'use strict';
  // Transition logic
  requestAnimationFrame(() => document.body.classList.add('is-entering'));

  // Custom Cursor
  const r = document.querySelector('.cursor'), d = document.querySelector('.cursor-dot');
  const t = { x: innerWidth / 2, y: innerHeight / 2 }, rp = { ...t }, dp = { ...t };
  if (r && d) {
    addEventListener('mousemove', e => { t.x = e.clientX; t.y = e.clientY; }, { passive: true });
    (function loop() {
      rp.x += (t.x - rp.x) * .15; rp.y += (t.y - rp.y) * .15;
      dp.x += (t.x - dp.x) * .25; dp.y += (t.y - dp.y) * .25;
      r.style.transform = `translate3d(${rp.x - 16}px, ${rp.y - 16}px, 0)`;
      d.style.transform = `translate3d(${dp.x - 2.5}px, ${dp.y - 2.5}px, 0)`;
      requestAnimationFrame(loop);
    })();
    document.querySelectorAll('a, button, input').forEach(el => {
      el.addEventListener('mouseenter', () => r.classList.add('is-hover'));
      el.addEventListener('mouseleave', () => r.classList.remove('is-hover'));
    });
  }

  // Logout Simulation
  document.getElementById('logout-trigger')?.addEventListener('click', () => {
    if (window.SamsarTransition) SamsarTransition.leave(() => location.href = '08-login.php');
    else location.href = '08-login.php';
  });
})();

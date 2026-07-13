(function () {
    'use strict';
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const fine = matchMedia('(pointer: fine)').matches;

    // PAGE TRANSITION — handled by samsar-transitions.js

    if (fine && !reduced) {
        const r = document.querySelector('.cursor'), d = document.querySelector('.cursor-dot');
        const t = { x: innerWidth / 2, y: innerHeight / 2 }, rp = { ...t }, dp = { ...t };
        addEventListener('mousemove', e => { t.x = e.clientX; t.y = e.clientY }, { passive: true });
        (function loop() {
            rp.x += (t.x - rp.x) * .18; rp.y += (t.y - rp.y) * .18; dp.x += (t.x - dp.x) * .32; dp.y += (t.y - dp.y) * .32;
            r.style.transform = `translate3d(${rp.x - 18}px,${rp.y - 18}px,0)`; d.style.transform = `translate3d(${dp.x - 2.5}px,${dp.y - 2.5}px,0)`;
            requestAnimationFrame(loop)
        })();
        document.querySelectorAll('a,button,input,label').forEach(el => {
            el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover') });
            el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover') });
        });
    }

    // password toggle
    const tog = document.getElementById('pw-toggle'), pw = document.getElementById('pw');
    if (tog && pw) tog.addEventListener('click', () => { pw.type = pw.type === 'password' ? 'text' : 'password' });

    // submit
    document.getElementById('login-form').addEventListener('submit', e => {
        e.preventDefault();
        const b = e.target.querySelector('.pill-btn span:first-child');
        b.textContent = window.t ? window.t('login.js.signingin') : 'Signing in…';
        document.body.classList.add('is-leaving');
        setTimeout(() => location.href = 'dashboard.php', 500);
    });
})();

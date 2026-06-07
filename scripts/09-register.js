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
        document.querySelectorAll('a,button,input,select,label').forEach(el => {
            el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover') });
            el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover') });
        });
    }

    // user/agency toggle
    const agencyFields = document.getElementById('agency-fields');
    document.querySelectorAll('.tt-opt').forEach(opt => {
        opt.addEventListener('click', e => {
            if (e.target.tagName === 'INPUT') return;
            document.querySelectorAll('.tt-opt').forEach(o => o.classList.remove('active'));
            opt.classList.add('active'); opt.querySelector('input').checked = true;
            const isAgency = opt.dataset.type === 'agency';
            if (isAgency) { agencyFields.hidden = false }
            else { agencyFields.style.animation = 'none'; setTimeout(() => { agencyFields.hidden = true; agencyFields.style.animation = '' }, 10) }
        });
    });

    // preselect from query string
    const params = new URLSearchParams(location.search);
    if (params.get('type') === 'agency') { const a = document.querySelector('[data-type="agency"]'); if (a) a.click() }

    // password meter
    const pw = document.getElementById('pw'), meter = document.querySelector('.meter span');
    if (pw && meter) {
        pw.addEventListener('input', () => {
            let s = 0; if (pw.value.length >= 8) s += 33; if (/[A-Z]/.test(pw.value)) s += 22; if (/\d/.test(pw.value)) s += 22; if (/[^A-Za-z0-9]/.test(pw.value)) s += 23;
            meter.style.width = Math.min(s, 100) + '%';
        })
    }

    // password toggle
    const tog = document.getElementById('pw-toggle');
    if (tog && pw) tog.addEventListener('click', () => { pw.type = pw.type === 'password' ? 'text' : 'password' });

    // submit
    document.getElementById('reg-form').addEventListener('submit', e => {
        e.preventDefault();
        const type = document.querySelector('input[name="acct"]:checked').value;
        const emailVal = e.target.querySelector('input[type="email"]').value || '';
        const b = e.target.querySelector('.pill-btn span:first-child');
        b.textContent = 'Creating account…';
        const dest = '20-verify-email.php?email=' + encodeURIComponent(emailVal) + '&type=' + type;
        if (window.SamsarTransition) { SamsarTransition.leave('clip-circle-center', 'normal', () => location.href = dest) }
        else { setTimeout(() => location.href = dest, 640) }
    });
})();

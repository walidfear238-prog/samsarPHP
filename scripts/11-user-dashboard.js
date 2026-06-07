(function () {
    'use strict';
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const fine = matchMedia('(pointer: fine)').matches;
    requestAnimationFrame(() => document.body.classList.add('is-entering'));
    setTimeout(() => document.body.classList.remove('is-entering'), 900);
    if (!reduced) { document.addEventListener('click', e => { const a = e.target.closest('a'); if (!a) return; const href = a.getAttribute('href'); if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || a.target === '_blank') return; const url = new URL(a.href, location.href); if (url.origin !== location.origin) return; e.preventDefault(); document.body.classList.add('is-leaving'); setTimeout(() => location.href = a.href, 700) }) }
    if (fine && !reduced) {
        const r = document.querySelector('.cursor'), d = document.querySelector('.cursor-dot');
        const t = { x: innerWidth / 2, y: innerHeight / 2 }, rp = { ...t }, dp = { ...t };
        addEventListener('mousemove', e => { t.x = e.clientX; t.y = e.clientY }, { passive: true });
        (function loop() {
            rp.x += (t.x - rp.x) * .18; rp.y += (t.y - rp.y) * .18; dp.x += (t.x - dp.x) * .32; dp.y += (t.y - dp.y) * .32;
            r.style.transform = `translate3d(${rp.x - 16}px,${rp.y - 16}px,0)`; d.style.transform = `translate3d(${dp.x - 2.5}px,${dp.y - 2.5}px,0)`;
            requestAnimationFrame(loop)
        })();
        document.querySelectorAll('a,button,input').forEach(el => {
            el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover') });
            el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover') });
        });
    }
    const rec = [
        { t: 'Villa Atlas', l: 'Souissi · Rabat', p: '18,500,000 MAD', img: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=400&q=80' },
        { t: 'Villa Ocean', l: 'Essaouira', p: '22,000,000 MAD', img: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=400&q=80' },
        { t: 'Riad Bahia', l: 'Fès Medina', p: '4,100,000 MAD', img: 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=400&q=80' },
        { t: 'Riad Yasmine', l: 'Marrakech', p: '5,200,000 MAD', img: 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=400&q=80' },
    ];
    document.getElementById('rec-grid').innerHTML = rec.map(p => `
  <a class="rec-card" href="03-property-details.php"><div class="rec-img"><img src="${p.img}" alt="${p.t}" loading="lazy"/></div><div class="rec-body"><span>${p.l}</span><strong>${p.t}</strong><b>${p.p}</b></div></a>`).join('');
    const io = new IntersectionObserver((es, o) => es.forEach(e => { if (e.isIntersecting) { const d = e.target.dataset.delay || 0; e.target.style.transitionDelay = d + 'ms'; e.target.classList.add('is-in'); o.unobserve(e.target) } }), { threshold: .1 });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
})();

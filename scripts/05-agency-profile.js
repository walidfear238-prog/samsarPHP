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
            r.style.transform = `translate3d(${rp.x - 18}px,${rp.y - 18}px,0)`; d.style.transform = `translate3d(${dp.x - 2.5}px,${dp.y - 2.5}px,0)`;
            requestAnimationFrame(loop)
        })();
        document.querySelectorAll('a,button,input,textarea').forEach(el => {
            el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover') });
            el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover') });
        });
    }
    const nav = document.querySelector('.nav');
    addEventListener('scroll', () => nav.classList.toggle('is-scrolled', scrollY > 40), { passive: true });

    // listings
    const list = [
        { t: 'Villa Tazri', l: 'Palmeraie', p: '12,400,000', img: 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=600&q=80' },
        { t: 'Riad Yasmine', l: 'Medina', p: '5,200,000', img: 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=600&q=80' },
        { t: 'Villa Ourika', l: 'Ourika Valley', p: '8,900,000', img: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=600&q=80' },
        { t: 'Riad Bahia', l: 'Medina', p: '4,100,000', img: 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=600&q=80' },
    ];
    document.getElementById('prop-grid').innerHTML = list.map((p, i) => `
  <a class="p-card" href="03-property-details.php" style="transition-delay:${i * 70}ms">
   <div class="p-img"><img src="${p.img}" alt="${p.t}" loading="lazy"/></div>
   <div class="p-body">
    <span class="p-loc">${p.l} · Marrakech</span>
    <h3 class="p-title">${p.t}</h3>
    <span class="p-price">${p.p} <small>MAD</small></span>
   </div>
  </a>`).join('');

    const io = new IntersectionObserver((es, o) => es.forEach(e => { if (e.isIntersecting) { const d = e.target.dataset.delay || 0; e.target.style.transitionDelay = d + 'ms'; e.target.classList.add('is-in'); o.unobserve(e.target) } }), { threshold: .1 });
    document.querySelectorAll('.reveal,.p-card').forEach(el => io.observe(el));

    // tabs
    document.querySelectorAll('.tab').forEach(t => t.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        document.querySelector(`[data-panel="${t.dataset.tab}"]`).classList.add('active');
    }));
})();

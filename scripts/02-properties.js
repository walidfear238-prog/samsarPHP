(function () {
    'use strict';
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const fine = matchMedia('(pointer: fine)').matches;

    const props = [
        { t: 'Villa Tazri', l: 'Palmeraie · Marrakech', city: 'marrakech', img: 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80', p: '12,400,000', price: 12400000, u: 'MAD', b: 5, ba: 6, a: 620, type: 'villa', status: 'sale', features: ['pool', 'garden', 'mountain', 'parking'], badge: 'For Sale', crimson: true },
        { t: 'Riad Souira', l: 'Medina · Essaouira', city: 'essaouira', img: 'https://images.unsplash.com/photo-1542718610-a1d656d1884c?auto=format&fit=crop&w=800&q=80', p: '38,000', price: 38000, u: 'MAD / mo', b: 4, ba: 4, a: 310, type: 'riad', status: 'rent', features: ['garden', 'sea'], badge: 'For Rent' },
        { t: 'Penthouse Lumiere', l: 'Anfa · Casablanca', city: 'casablanca', img: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80', p: '7,950,000', price: 7950000, u: 'MAD', b: 3, ba: 3, a: 240, type: 'apt', status: 'sale', features: ['sea', 'parking'], badge: 'New', crimson: true },
        { t: 'Riad Yasmine', l: 'Medina · Marrakech', city: 'marrakech', img: 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=800&q=80', p: '5,200,000', price: 5200000, u: 'MAD', b: 6, ba: 5, a: 380, type: 'riad', status: 'sale', features: ['garden', 'hammam'], badge: 'For Sale' },
        { t: 'Villa Atlas', l: 'Souissi · Rabat', city: 'rabat', img: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80', p: '18,500,000', price: 18500000, u: 'MAD', b: 6, ba: 7, a: 740, type: 'villa', status: 'sale', features: ['pool', 'garden', 'parking'], badge: 'For Sale', crimson: true },
        { t: 'Apartment Marina', l: 'Tangier Hills', city: 'tangier', img: 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800&q=80', p: '2,800,000', price: 2800000, u: 'MAD', b: 2, ba: 2, a: 120, type: 'apt', status: 'sale', features: ['sea', 'parking'], badge: 'For Sale' },
        { t: 'Riad Bahia', l: 'Medina · Fes', city: 'fes', img: 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=800&q=80', p: '4,100,000', price: 4100000, u: 'MAD', b: 5, ba: 4, a: 290, type: 'riad', status: 'sale', features: ['hammam', 'garden'], badge: 'For Sale' },
        { t: 'Villa Ocean', l: 'Essaouira Coast', city: 'essaouira', img: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=800&q=80', p: '22,000,000', price: 22000000, u: 'MAD', b: 7, ba: 8, a: 920, type: 'villa', status: 'sale', features: ['pool', 'garden', 'sea', 'parking'], badge: 'New', crimson: true },
        { t: 'Loft Hassan', l: 'Centre · Casablanca', city: 'casablanca', img: 'https://images.unsplash.com/photo-1567496898669-ee935f5f647a?auto=format&fit=crop&w=800&q=80', p: '18,000', price: 18000, u: 'MAD / mo', b: 2, ba: 2, a: 140, type: 'apt', status: 'rent', features: ['parking'], badge: 'For Rent' }
    ];

    const state = { type: 'all', status: 'all', bd: 0, ba: 0, q: '', min: 0, max: Infinity, cities: [], features: [] };
    const grid = document.getElementById('grid');
    const countEl = document.getElementById('result-count');
    const panel = document.getElementById('filter-panel');

    function moneyToNumber(value) {
        const n = parseInt(String(value || '').replace(/[^0-9]/g, ''), 10);
        return Number.isFinite(n) ? n : 0;
    }

    function syncAdvancedFilters() {
        const minInput = document.getElementById('f-min-price');
        const maxInput = document.getElementById('f-max-price');
        state.min = moneyToNumber(minInput && minInput.value);
        const max = moneyToNumber(maxInput && maxInput.value);
        state.max = max > 0 ? max : Infinity;
        state.cities = Array.from(document.querySelectorAll('[data-city]:checked')).map(x => x.dataset.city);
        state.features = Array.from(document.querySelectorAll('[data-feature]:checked')).map(x => x.dataset.feature);
    }

    function filteredList() {
        syncAdvancedFilters();
        return props.filter(p => {
            if (state.type !== 'all' && p.type !== state.type) return false;
            if (state.status !== 'all' && p.status !== state.status) return false;
            if (state.bd > 0 && p.b < state.bd) return false;
            if (state.ba > 0 && p.ba < state.ba) return false;
            if (state.min > 0 && p.price < state.min) return false;
            if (state.max < Infinity && p.price > state.max) return false;
            if (state.cities.length && !state.cities.includes(p.city)) return false;
            if (state.features.length && !state.features.every(f => p.features.includes(f))) return false;
            if (state.q.trim() && !(p.t + ' ' + p.l).toLowerCase().includes(state.q.toLowerCase())) return false;
            return true;
        });
    }

    function render() {
        if (!grid) return;
        const list = filteredList();
        grid.innerHTML = list.map((p, i) => `
   <article class="card is-in" style="transition-delay:${i * 45}ms">
    <div class="card-media">
     <img src="${p.img}" alt="${p.t}" loading="lazy"/>
     <span class="card-badge ${p.crimson ? 'crimson' : ''}">${p.badge}</span>
     <button class="card-fav" data-fav aria-label="Save"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></button>
    </div>
    <div class="card-body">
     <span class="card-loc">${p.l}</span>
     <h3 class="card-title"><a href="03-property-details.php">${p.t}</a></h3>
     <div class="card-specs"><span>${p.b} bd</span><span>${p.ba} ba</span><span>${p.a} m²</span></div>
     <div class="card-foot"><span class="card-price">${p.p} <small>${p.u}</small></span><a class="view-details" href="03-property-details.php">View details <span class="arrow">→</span></a></div>
    </div>
   </article>`).join('') || '<div class="empty-results"><h3>No properties found</h3><p>Try removing one or more filters.</p></div>';
        if (countEl) countEl.textContent = list.length;
        grid.querySelectorAll('[data-fav]').forEach(b => b.addEventListener('click', e => { e.preventDefault(); e.stopPropagation(); b.classList.toggle('active'); }));
    }

    function setActive(groupSelector, activeEl) {
        document.querySelectorAll(groupSelector).forEach(x => x.classList.remove('active'));
        activeEl.classList.add('active');
    }

    if (panel) {
        panel.addEventListener('click', e => {
            const chip = e.target.closest('.filter-chip');
            if (chip) { e.preventDefault(); setActive('.filter-chip', chip); state.type = chip.dataset.v || 'all'; render(); return; }
            const seg = e.target.closest('.seg-btn');
            if (seg) { e.preventDefault(); setActive('.seg-btn', seg); state.status = seg.dataset.v || 'all'; render(); return; }
            const pill = e.target.closest('.pill');
            if (pill) {
                e.preventDefault();
                const row = pill.closest('.pill-row');
                row.querySelectorAll('.pill').forEach(x => x.classList.remove('active'));
                pill.classList.add('active');
                const group = row.dataset.group;
                if (group === 'bd' || group === 'ba') state[group] = +pill.dataset.v || 0;
                render();
            }
        });
    }

    const searchInput = document.getElementById('f-search');
    if (searchInput) searchInput.addEventListener('input', e => { state.q = e.target.value; render(); });
    ['f-min-price', 'f-max-price'].forEach(id => { const input = document.getElementById(id); if (input) input.addEventListener('input', render); });
    document.querySelectorAll('[data-city], [data-feature]').forEach(input => input.addEventListener('change', render));
    const applyBtn = document.getElementById('apply-filters');
    if (applyBtn) applyBtn.addEventListener('click', render);

    const resetBtn = document.getElementById('reset-filters');
    if (resetBtn) resetBtn.addEventListener('click', () => {
        state.type = 'all'; state.status = 'all'; state.bd = 0; state.ba = 0; state.q = ''; state.min = 0; state.max = Infinity; state.cities = []; state.features = [];
        if (searchInput) searchInput.value = '';
        const minInput = document.getElementById('f-min-price');
        const maxInput = document.getElementById('f-max-price');
        if (minInput) minInput.value = ''; if (maxInput) maxInput.value = '';
        document.querySelectorAll('[data-city], [data-feature]').forEach(x => x.checked = false);
        document.querySelectorAll('.filter-chip,.seg-btn,.pill').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('[data-v="all"],[data-v="0"]').forEach(el => el.classList.add('active'));
        render();
    });

    const ftToggle = document.getElementById('ft-toggle');
    if (ftToggle && panel) ftToggle.addEventListener('click', () => panel.classList.toggle('is-open'));

    document.querySelectorAll('.vt-btn').forEach(b => b.addEventListener('click', () => {
        document.querySelectorAll('.vt-btn').forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        if (grid) grid.classList.toggle('list-view', b.dataset.view === 'list');
    }));

    const nav = document.querySelector('.nav');
    if (nav) addEventListener('scroll', () => nav.classList.toggle('is-scrolled', scrollY > 10), { passive: true });

    if (fine && !reduced) {
        const r = document.querySelector('.cursor'), d = document.querySelector('.cursor-dot');
        if (r && d) {
            const t = { x: innerWidth / 2, y: innerHeight / 2 }, rp = { ...t }, dp = { ...t };
            addEventListener('mousemove', e => { t.x = e.clientX; t.y = e.clientY; }, { passive: true });
            (function loop() {
                rp.x += (t.x - rp.x) * .18; rp.y += (t.y - rp.y) * .18; dp.x += (t.x - dp.x) * .32; dp.y += (t.y - dp.y) * .32;
                r.style.transform = `translate3d(${rp.x - 18}px,${rp.y - 18}px,0)`;
                d.style.transform = `translate3d(${dp.x - 2.5}px,${dp.y - 2.5}px,0)`;
                requestAnimationFrame(loop);
            })();
            document.querySelectorAll('a,button,input,select,label').forEach(el => {
                el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover') });
                el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover') });
            });
        }
    }

    render();
})();
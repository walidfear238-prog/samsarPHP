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
    document.querySelectorAll('a,button,input').forEach(el => {
      el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover') });
      el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover') });
    });
  }
  const nav = document.querySelector('.nav');
  addEventListener('scroll', () => nav.classList.toggle('is-scrolled', scrollY > 10), { passive: true });

  const ags = [
    { n: 'Atlas Real Estate', c: 'Marrakech', cover: 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=600&q=80', logo: 'https://images.unsplash.com/photo-1572021335469-31706a17aaef?auto=format&fit=crop&w=200&q=80', bio: 'Luxury riads & villas in the Palmeraie and Ourika valley since 2008.', p: 124, y: 18 },
    { n: 'Anfa Properties', c: 'Casablanca', cover: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=600&q=80', logo: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80', bio: 'Modern urban living — penthouses, beachside condos and corporate residences.', p: 212, y: 12 },
    { n: 'Médina Heritage', c: 'Fès', cover: 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=600&q=80', logo: 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?auto=format&fit=crop&w=200&q=80', bio: 'Restored 17th-19th century riads in the medina of Fès el-Bali.', p: 48, y: 22 },
    { n: 'Tangier Hills Realty', c: 'Tangier', cover: 'https://images.unsplash.com/photo-1577147443647-81d0e4bfe4cc?auto=format&fit=crop&w=600&q=80', logo: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80', bio: 'Hillside villas with views over the Strait of Gibraltar.', p: 96, y: 9 },
    { n: 'Souissi Maison', c: 'Rabat', cover: 'https://images.unsplash.com/photo-1570214476695-19bd467e6f7a?auto=format&fit=crop&w=600&q=80', logo: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=200&q=80', bio: 'Family villas and ambassadorial residences in the Souissi district.', p: 67, y: 15 },
    { n: 'Essaouira Coast Group', c: 'Essaouira', cover: 'https://images.unsplash.com/photo-1528657249085-893be9ffd04f?auto=format&fit=crop&w=600&q=80', logo: 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?auto=format&fit=crop&w=200&q=80', bio: 'Beachfront villas and medina riads on Morocco\'s Atlantic coast.', p: 54, y: 11 },
  ];
  const grid = document.getElementById('ag-grid');
  grid.innerHTML = ags.map((a, i) => `
  <a class="ag-card" href="05-agency-profile.php" style="transition-delay:${i * 70}ms">
   <div class="ag-cover"><img src="${a.cover}" alt="${a.n}" loading="lazy"/></div>
   <div class="ag-body">
    <div class="ag-top">
     <img class="ag-logo" src="${a.logo}" alt=""/>
     <div><div class="ag-name">${a.n}</div><div class="ag-city">${a.c}</div></div>
    </div>
    <p class="ag-bio">${a.bio}</p>
    <div class="ag-stats">
     <div><strong>${a.p}</strong>Listings</div>
     <div><strong>${a.y} yrs</strong>Experience</div>
     <div><strong>4.9★</strong>Rating</div>
    </div>
   </div>
  </a>`).join('');

  const io = new IntersectionObserver((es, o) => es.forEach(e => { if (e.isIntersecting) { const d = e.target.dataset.delay || 0; e.target.style.transitionDelay = d + 'ms'; e.target.classList.add('is-in'); o.unobserve(e.target) } }), { threshold: .1, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.reveal,.ag-card').forEach(el => io.observe(el));

  document.querySelectorAll('.ag-tab').forEach(t => t.addEventListener('click', () => {
    document.querySelectorAll('.ag-tab').forEach(x => x.classList.remove('active')); t.classList.add('active');
    const city = t.textContent;
    document.querySelectorAll('.ag-card').forEach(card => {
      const cardCity = card.querySelector('.ag-city').textContent;
      if (city === 'All cities' || cardCity === city) card.style.display = '';
      else card.style.display = 'none';
    });
  }));

  // Search agencies
  const searchInput = document.getElementById('agency-search') || document.querySelector('.head-search input');
  if (searchInput) {
    searchInput.addEventListener('input', e => {
      const q = e.target.value.toLowerCase().trim();
      document.querySelectorAll('.ag-card').forEach(card => {
        const name = card.querySelector('.ag-name').textContent.toLowerCase();
        const city = card.querySelector('.ag-city').textContent.toLowerCase();
        card.style.display = (name.includes(q) || city.includes(q)) ? '' : 'none';
      });
    });
  }

  // Follow button toggle
  document.querySelectorAll('[data-follow]').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const isFollowing = btn.classList.toggle('following');
      btn.textContent = isFollowing ? 'Following' : 'Follow';
      if (isFollowing) { btn.style.background = 'var(--crimson)'; btn.style.color = '#fff'; btn.style.borderColor = 'var(--crimson)'; }
      else { btn.style.background = ''; btn.style.color = ''; btn.style.borderColor = ''; }
    });
  });
})();

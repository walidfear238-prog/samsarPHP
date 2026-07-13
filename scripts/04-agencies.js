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

  const grid = document.getElementById('ag-grid');

  const io = new IntersectionObserver((es, o) => es.forEach(e => { if (e.isIntersecting) { const d = e.target.dataset.delay || 0; e.target.style.transitionDelay = d + 'ms'; e.target.classList.add('is-in'); o.unobserve(e.target) } }), { threshold: .1, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.reveal').forEach(el => io.observe(el));

  // Resolve a profile_image path the same way the rest of the app does
  function logoUrl(path) {
    if (!path) return null;
    if (/^https?:\/\//.test(path)) return path;
    if (path.startsWith('uploads/')) return path;
    return 'uploads/profile/' + path;
  }

  function yearsSince(dateStr) {
    if (!dateStr) return 0;
    const then = new Date(String(dateStr).replace(' ', 'T'));
    if (isNaN(then)) return 0;
    return Math.max(0, Math.floor((Date.now() - then.getTime()) / (1000 * 60 * 60 * 24 * 365)));
  }

  // Fetch the real agencies (users with role = 'agency') from the database
  fetch('api/get-agencies.php')
    .then(res => res.json())
    .then(ags => {
      if (!Array.isArray(ags) || ags.length === 0) {
        grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:var(--graphite);padding:40px 0">' + (window.t ? window.t('agencies.js.none_yet') : 'No agencies yet.') + '</p>';
        return;
      }

      grid.innerHTML = ags.map((a, i) => {
        const name = a.name || (window.t ? window.t('agencies.js.default_name') : 'Agency');
        const city = a.city || '';
        const listings = a.listings || 0;
        const years = yearsSince(a.joined);
        const logo = logoUrl(a.logo);
        const avatarFallback = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=C72C41&color=fff&size=200`;
        const bio = window.t ? (city ? window.t('agencies.js.bio_with_city').replace('{name}', name).replace('{city}', city) : window.t('agencies.js.bio_no_city').replace('{name}', name)) : `${name} — real estate agency${city ? ` based in ${city}` : ' on SAMSAR'}.`;

        return `
  <a class="ag-card" href="05-agency-profile.php?id=${a.id}" style="transition-delay:${i * 70}ms">
   <div class="ag-cover"></div>
   <div class="ag-body">
    <div class="ag-top">
     <img class="ag-logo" src="${logo || avatarFallback}" alt="" onerror="this.onerror=null;this.src='${avatarFallback}'"/>
     <div><div class="ag-name">${name}</div><div class="ag-city">${city}</div></div>
    </div>
    <p class="ag-bio">${bio}</p>
    <div class="ag-stats">
     <div><strong>${listings}</strong>${window.t ? window.t('agencyprofile.listings') : 'Listings'}</div>
     <div><strong>${years} ${window.t ? window.t('agencies.js.yrs') : 'yrs'}</strong>${window.t ? window.t('agencies.js.experience') : 'Experience'}</div>
     <div><strong>—</strong>${window.t ? window.t('agencyprofile.rating') : 'Rating'}</div>
    </div>
   </div>
  </a>`;
      }).join('');

      document.querySelectorAll('.ag-card').forEach(el => io.observe(el));
    })
    .catch(() => {
      grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:var(--graphite);padding:40px 0">' + (window.t ? window.t('agencies.js.load_error') : 'Unable to load agencies right now.') + '</p>';
    });

  document.querySelectorAll('.ag-tab').forEach(t => t.addEventListener('click', () => {
    document.querySelectorAll('.ag-tab').forEach(x => x.classList.remove('active')); t.classList.add('active');
    const isAll = t.dataset.all === 'true';
    const city = t.textContent;
    document.querySelectorAll('.ag-card').forEach(card => {
      const cardCity = card.querySelector('.ag-city').textContent;
      if (isAll || cardCity === city) card.style.display = '';
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
      btn.textContent = isFollowing ? (window.t ? window.t('propdetails.following') : 'Following') : (window.t ? window.t('propdetails.follow') : 'Follow');
      if (isFollowing) { btn.style.background = 'var(--crimson)'; btn.style.color = '#fff'; btn.style.borderColor = 'var(--crimson)'; }
      else { btn.style.background = ''; btn.style.color = ''; btn.style.borderColor = ''; }
    });
  });
})();

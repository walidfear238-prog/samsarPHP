(function () {
  'use strict';

  // ── Cursor ──────────────────────────────────────────────────────────────────
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine    = matchMedia('(pointer: fine)').matches;

  if (fine && !reduced) {
    const r = document.querySelector('.cursor');
    const d = document.querySelector('.cursor-dot');
    if (r && d) {
      const t  = { x: innerWidth / 2, y: innerHeight / 2 };
      const rp = { ...t };
      const dp = { ...t };
      addEventListener('mousemove', e => { t.x = e.clientX; t.y = e.clientY; }, { passive: true });
      (function loop() {
        rp.x += (t.x - rp.x) * .18; rp.y += (t.y - rp.y) * .18;
        dp.x += (t.x - dp.x) * .32; dp.y += (t.y - dp.y) * .32;
        r.style.transform = `translate3d(${rp.x - 18}px,${rp.y - 18}px,0)`;
        d.style.transform = `translate3d(${dp.x - 2.5}px,${dp.y - 2.5}px,0)`;
        requestAnimationFrame(loop);
      })();
      document.querySelectorAll('a,button,input,select,textarea').forEach(el => {
        el.addEventListener('mouseenter', () => { r.classList.add('is-hover');    d.classList.add('is-hover');    });
        el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover'); });
      });
    }
  }

  // ── Nav scroll ──────────────────────────────────────────────────────────────
  const nav = document.querySelector('.nav');
  if (nav) addEventListener('scroll', () => nav.classList.toggle('is-scrolled', scrollY > 10), { passive: true });

  // ── Reveal on scroll ────────────────────────────────────────────────────────
  const io = new IntersectionObserver((entries, observer) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.style.transitionDelay = (e.target.dataset.delay || 0) + 'ms';
        e.target.classList.add('is-in');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });

  document.querySelectorAll('.reveal').forEach(el => io.observe(el));

  // Force-reveal everything still hidden after content is injected.
  function revealAll() {
    document.querySelectorAll('.reveal:not(.is-in)').forEach(el => {
      el.style.transitionDelay = (el.dataset.delay || 0) + 'ms';
      el.classList.add('is-in');
    });
  }

  // ── Gallery ─────────────────────────────────────────────────────────────────
  function setupGallery(mainImg, thumbContainer) {
    if (!mainImg || !thumbContainer) return;
    const thumbs = thumbContainer.querySelectorAll('.g-thumb');
    thumbs.forEach(thumb => {
      thumb.addEventListener('click', function () {
        thumbs.forEach(x => x.classList.remove('active'));
        this.classList.add('active');
        const img = this.querySelector('img');
        if (img) {
          mainImg.style.opacity = '0';
          setTimeout(() => { mainImg.src = img.src; mainImg.style.opacity = '1'; }, 200);
        }
      });
    });
  }

  // ── Save button ─────────────────────────────────────────────────────────────
  const saveBtn = document.getElementById('detail-save-btn');
  if (saveBtn) {
    saveBtn.addEventListener('click', function () {
      const svg  = this.querySelector('svg');
      const span = this.querySelector('span');
      if (this.classList.toggle('saved')) {
        this.style.background   = 'var(--crimson)';
        this.style.color        = '#fff';
        this.style.borderColor  = 'var(--crimson)';
        if (svg)  svg.setAttribute('fill', 'currentColor');
        if (span) span.textContent = ' Saved';
      } else {
        this.style.background  = '';
        this.style.color       = '';
        this.style.borderColor = '';
        if (svg)  svg.setAttribute('fill', 'none');
        if (span) span.textContent = ' Save';
      }
    });
  }

  // ── Follow button ────────────────────────────────────────────────────────────
  document.querySelectorAll('[data-follow]').forEach(btn => {
    btn.addEventListener('click', function () {
      const on = this.classList.toggle('following');
      this.textContent    = on ? 'Following' : 'Follow';
      this.style.background  = on ? 'var(--crimson)' : '';
      this.style.color       = on ? '#fff' : '';
      this.style.borderColor = on ? 'var(--crimson)' : '';
    });
  });

  // ── Contact form ─────────────────────────────────────────────────────────────
  const form = document.getElementById('contact-form');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const b = this.querySelector('button[type="submit"]');
      if (!b) return;
      const orig = b.textContent;
      b.textContent = 'Sent ✓';
      b.disabled = true;
      setTimeout(() => { b.textContent = orig; b.disabled = false; this.reset(); }, 1800);
    });
  }

  // ── Book a visit ─────────────────────────────────────────────────────────────
  const bookBtn = document.getElementById('book-visit-btn');
  if (bookBtn) {
    bookBtn.addEventListener('click', function () {
      const id = new URLSearchParams(window.location.search).get('id');
      if (id) window.location.href = `09-book-visit.php?property_id=${id}`;
    });
  }

  // ── Helpers ──────────────────────────────────────────────────────────────────
  function set(id, value, fallback = '—') {
    const el = document.getElementById(id);
    if (el) el.textContent = (value !== null && value !== undefined && value !== '') ? value : fallback;
  }

  function fmt(price) {
    return new Intl.NumberFormat('fr-MA').format(price);
  }

  // ── FIX: Helper to get correct image URL ──────────────────────────────────
  function getImageUrl(filename) {
    if (!filename) return 'https://placehold.co/600x400/f5f0eb/8ba3b0?text=No+Image';
    
    // If it's already a full URL, return it
    if (filename.startsWith('http://') || filename.startsWith('https://')) {
      return filename;
    }
    
    // If it already has the uploads path, return as is
    if (filename.startsWith('uploads/')) {
      return filename;
    }
    
    // Otherwise, prepend the uploads path for property images
    return `uploads/property_images/${filename}`;
  }

  // ── Similar properties ───────────────────────────────────────────────────────
  function populateSimilarProperties(similars) {
    const container = document.getElementById('similar-properties');
    if (!container) return;

    if (!Array.isArray(similars) || similars.length === 0) {
      container.innerHTML = '<p style="color:var(--graphite);font-size:14px;grid-column:1/-1">No similar properties found.</p>';
      return;
    }

    container.innerHTML = similars.map(p => {
      const imageSrc = p.main_image ? getImageUrl(p.main_image) : 'https://placehold.co/400x300/f5f0eb/8ba3b0?text=No+Image';
      return `
      <a href="03-property-details.php?id=${p.id}" class="sim-card">
        <div class="sim-img">
          <img
            src="${imageSrc}"
            alt="${escHtml(p.title || 'Property')}"
            loading="lazy"
          />
        </div>
        <span>${escHtml((p.property_type || 'Property').toUpperCase())}</span>
        <h3>${escHtml(p.title || 'Untitled')}</h3>
        <span>${escHtml(p.city || '')}</span>
        <strong>${p.price ? fmt(p.price) + ' MAD' : 'Price on request'}</strong>
      </a>
    `}).join('');
  }

  // Minimal HTML escaping to prevent XSS from DB data
  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  // ── Main populate ────────────────────────────────────────────────────────────
  function populatePropertyDetails(p) {
    // Page title
    document.title = `${p.title || 'Property'} · SAMSAR`;

    // Breadcrumb
    set('breadcrumb-title', p.title, 'Property');

    // Hero
    const locationParts = [p.city, p.district].filter(Boolean);
    set('property-location', locationParts.join(' · '), 'Morocco');
    set('property-title', p.title, 'Untitled Property');

    const metaParts = [
      p.bedrooms    ? `${p.bedrooms} bedrooms`  : null,
      p.bathrooms   ? `${p.bathrooms} bathrooms` : null,
      p.area        ? `${p.area} m²`            : null,
      p.property_type || null,
    ].filter(Boolean);
    set('property-meta', metaParts.join(' · '), 'Luxury property');

    const priceEl = document.getElementById('property-price');
    if (priceEl) {
      priceEl.innerHTML = p.price
        ? `${fmt(p.price)} <small>MAD</small>`
        : 'Price on request';
    }

    // ── FIX: Gallery with proper image paths ──────────────────────────────────
    const mainImg      = document.getElementById('g-main-img');
    const thumbContainer = document.getElementById('g-thumbs');
    
    // Process images to add correct paths
    let images = Array.isArray(p.images) ? p.images.filter(Boolean) : [];
    images = images.map(img => getImageUrl(img));
    
    const mainSrc = p.main_image ? getImageUrl(p.main_image) : (images[0] || null);

    if (mainImg) {
      mainImg.src = mainSrc || 'https://placehold.co/900x600/f5f0eb/8ba3b0?text=No+Image';
      mainImg.alt = p.title || 'Property';
    }

    if (thumbContainer) {
      if (images.length > 0) {
        thumbContainer.innerHTML = images.map((src, i) => `
          <button class="g-thumb${i === 0 ? ' active' : ''}">
            <img src="${src}" alt="${escHtml(p.title || 'Property')} · ${i + 1}" loading="lazy">
          </button>`).join('');
        setupGallery(mainImg, thumbContainer);
      } else {
        thumbContainer.innerHTML = '';
      }
    }

    // Key facts
    set('fact-bedrooms', p.bedrooms);
    set('fact-bathrooms', p.bathrooms);
    set('fact-area',  p.area ? `${p.area} m²` : null);
    set('fact-type',  p.property_type);

    const factStatus = document.getElementById('fact-status');
    if (factStatus) {
      const labels = {
        available: 'Available', rented: 'Rented', sold: 'Sold',
        pending: 'Pending', draft: 'Draft', sale: 'For Sale', rent: 'For Rent'
      };
      factStatus.textContent = labels[p.status] || p.status || '—';
      factStatus.style.color = ({
        available: '#22c55e', sold: '#ef4444', rented: '#f97316'
      })[p.status] || 'inherit';
    }

    const factDate = document.getElementById('fact-date');
    if (factDate && p.created_at) {
      factDate.textContent = new Date(p.created_at).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
      });
    }

    // Description
    const descEl = document.getElementById('property-description');
    if (descEl) {
      if (p.description && p.description.trim()) {
        descEl.innerHTML = '';
        p.description.trim().split(/\n+/).filter(s => s.trim()).forEach(para => {
          const pEl = document.createElement('p');
          pEl.textContent = para.trim();
          descEl.appendChild(pEl);
        });
      } else {
        descEl.innerHTML = '<p style="color:#999">No description provided for this property.</p>';
      }
    }

    // Features
    const featuresList = document.getElementById('property-features');
    if (featuresList) {
      const features = [
        p.bedrooms     && `${p.bedrooms} Bedrooms`,
        p.bathrooms    && `${p.bathrooms} Bathrooms`,
        p.area         && `${p.area} m² Total Area`,
        p.property_type && `Type: ${p.property_type}`,
        p.city         && `City: ${p.city}`,
        p.district     && `District: ${p.district}`,
      ].filter(Boolean);
      featuresList.innerHTML = features.length
        ? features.map(f => `<li>${escHtml(f)}</li>`).join('')
        : '<li style="color:#999">No features listed.</li>';
    }

    // ── FIX: Agent card with proper avatar path ──────────────────────────────
    const agentImg = document.getElementById('agent-avatar');
    if (agentImg) {
      if (p.agent_avatar) {
        // If agent_avatar already has the path, use it, otherwise prepend
        if (p.agent_avatar.startsWith('uploads/') || p.agent_avatar.startsWith('http')) {
          agentImg.src = p.agent_avatar;
        } else {
          agentImg.src = `uploads/profile/${p.agent_avatar}`;
        }
      } else {
        agentImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(p.agentName || 'Agent')}&background=C72C41&color=fff&size=80`;
      }
      agentImg.alt = p.agentName || 'Agent';
    }

    set('agent-name',     p.agentName, 'Agency');
    set('agent-location', p.city ? `Listing agency · ${p.city}` : 'Listing agency');

    const bio = p.agencyName
      ? `${p.agencyName} — Specialists in ${p.property_type || 'luxury'} properties.`
      : p.agentName
        ? `${p.agentName} — Professional real estate agent.`
        : 'Professional real estate agency.';
    set('agent-bio', bio);

    const waLink = document.getElementById('whatsapp-link');
    if (waLink && p.agent_phone) {
      const phone = p.agent_phone.replace(/\D/g, '');
      if (phone) waLink.href = `https://wa.me/${phone}`;
    }

    const agencyLink = document.getElementById('agency-profile-link');
    if (agencyLink && p.user_id) agencyLink.href = `05-agency-profile.php?id=${p.user_id}`;

    if (saveBtn && p.id) saveBtn.dataset.propertyId = p.id;

    // Populate similar properties from API response
    populateSimilarProperties(p.similar || []);

    // Force-reveal everything now that content is in the DOM
    setTimeout(revealAll, 80);
  }

  // ── Load ─────────────────────────────────────────────────────────────────────
  async function loadPropertyDetails() {
    const id = new URLSearchParams(window.location.search).get('id');

    if (!id) {
      const c = document.querySelector('main .container');
      if (c) c.innerHTML = `
        <div style="text-align:center;padding:80px 20px">
          <h2>No property selected</h2>
          <a href="02-properties.php" style="display:inline-block;margin-top:20px;padding:12px 30px;
             background:#1a1a1a;color:#fff;text-decoration:none;border-radius:4px;">Browse listings</a>
        </div>`;
      return;
    }

    try {
      // ── FIX: Correct API path ──────────────────────────────────────────────
const res = await fetch(`api/property-details.php?id=${id}`);

      const text = await res.text();

      if (!res.ok) {
        let msg = `Server error ${res.status}`;
        try { msg = JSON.parse(text).error || msg; } catch (_) { msg += ': ' + text.slice(0, 200); }
        throw new Error(msg);
      }

      let data;
      try {
        data = JSON.parse(text);
      } catch (_) {
        throw new Error('API returned invalid JSON. Check for PHP errors in api/property-details.php.');
      }

      if (data.error) throw new Error(data.error);

      populatePropertyDetails(data);

    } catch (err) {
      console.error('[SAMSAR] Property load failed:', err.message);
      const c = document.querySelector('main .container');
      if (c) {
        const crumbs = c.querySelector('.crumbs');
        c.innerHTML = '';
        if (crumbs) c.appendChild(crumbs);
        
        c.innerHTML += `
          <div style="text-align:center;padding:60px 20px">
            <h2 style="color:#dc3545">Unable to load property</h2>
            <p style="color:#666;margin-top:8px;font-size:14px">${escHtml(err.message)}</p>
            <a href="02-properties.php" style="display:inline-block;margin-top:24px;padding:12px 30px;
               background:#1a1a1a;color:#fff;text-decoration:none;border-radius:4px;">Back to listings</a>
          </div>`;
      }
    }
  }

  loadPropertyDetails();
})();
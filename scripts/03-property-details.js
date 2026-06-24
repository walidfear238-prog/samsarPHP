/**
 * SAMSAR — Property Details Page
 * All API paths are RELATIVE (no BASE_URL) so they resolve correctly
 * regardless of your local folder name.
 *
 * Folder layout assumed:
 *   samsar/
 *   ├── 03-property-details.php          ← the page
 *   ├── api/
 *   │   ├── property-details.php         ← main data endpoint
 *   │   └── follow/
 *   │       ├── check-follow.php
 *   │       ├── follow.php
 *   │       └── unfollow.php
 *   └── scripts/
 *       └── 03-property-details.js       ← this file
 */

'use strict';

(function () {

  // ─────────────────────────────────────────────────────────────────────────
  // API ENDPOINTS  — relative paths, always correct from the page
  // ─────────────────────────────────────────────────────────────────────────
  const API = {
    PROPERTY     : 'api/property-details.php',       // GET  ?id=
    CHECK_FOLLOW : 'api/follow/check-follow.php',    // GET  ?user_id=
    FOLLOW       : 'api/follow/follow.php',           // POST following_id
    UNFOLLOW     : 'api/follow/unfollow.php',         // POST following_id
  };


  // ─────────────────────────────────────────────────────────────────────────
  // UTILITIES
  // ─────────────────────────────────────────────────────────────────────────

  /** HTML-escape a value before inserting into innerHTML */
  function esc(val) {
    return String(val ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  /** Set textContent of an element by id */
  function setText(id, val, fallback = '—') {
    const el = document.getElementById(id);
    if (el) el.textContent = (val !== null && val !== undefined && val !== '') ? val : fallback;
  }

  /** Format a number in Moroccan locale */
  function fmt(n) {
    return new Intl.NumberFormat('fr-MA').format(n);
  }

  /**
   * Build the full relative URL for a property image.
   * - If it already starts with http(s) → return as-is
   * - If it already starts with "uploads/" → return as-is (PHP already prefixed it)
   * - Otherwise → prepend the standard image directory
   */
  function imgUrl(path) {
    if (!path) return null;
    if (/^https?:\/\//.test(path))   return path;
    if (path.startsWith('uploads/')) return path;
    return 'uploads/property_images/' + path;
  }

  const PLACEHOLDER = 'https://placehold.co/900x600/f5f0eb/8ba3b0?text=No+Image';
  const PLACEHOLDER_SIM = 'https://placehold.co/400x280/f5f0eb/8ba3b0?text=No+Image';


  // ─────────────────────────────────────────────────────────────────────────
  // TOAST
  // ─────────────────────────────────────────────────────────────────────────
  let _toastTimer;

  function toast(msg, type = 'neutral', ms = 2800) {
    let el = document.getElementById('_sm-toast');
    if (!el) {
      el = document.createElement('div');
      el.id = '_sm-toast';
      Object.assign(el.style, {
        position   : 'fixed',
        bottom     : '28px',
        left       : '50%',
        transform  : 'translateX(-50%) translateY(14px)',
        padding    : '11px 26px',
        borderRadius: '999px',
        fontSize   : '13px',
        fontWeight : '500',
        letterSpacing: '.02em',
        opacity    : '0',
        transition : 'opacity .25s ease, transform .25s ease',
        zIndex     : '9999',
        pointerEvents: 'none',
        whiteSpace : 'nowrap',
      });
      document.body.appendChild(el);
    }

    const colors = {
      success : ['#22c55e', '#fff'],
      error   : ['#ef4444', '#fff'],
      warning : ['#f59e0b', '#fff'],
      neutral : ['#1a1a1a', '#fff'],
    };
    const [bg, fg] = colors[type] || colors.neutral;
    el.style.background = bg;
    el.style.color      = fg;
    el.textContent      = msg;

    el.style.opacity   = '1';
    el.style.transform = 'translateX(-50%) translateY(0)';

    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => {
      el.style.opacity   = '0';
      el.style.transform = 'translateX(-50%) translateY(14px)';
    }, ms);
  }


  // ─────────────────────────────────────────────────────────────────────────
  // CUSTOM CURSOR
  // ─────────────────────────────────────────────────────────────────────────
  const _hoverTargets = new WeakSet();

  function addHover(el) {
    if (_hoverTargets.has(el)) return;
    _hoverTargets.add(el);
    const ring = document.querySelector('.cursor');
    const dot  = document.querySelector('.cursor-dot');
    if (!ring || !dot) return;
    el.addEventListener('mouseenter', () => { ring.classList.add('is-hover'); dot.classList.add('is-hover'); });
    el.addEventListener('mouseleave', () => { ring.classList.remove('is-hover'); dot.classList.remove('is-hover'); });
  }

  function initCursor() {
    if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (!matchMedia('(pointer: fine)').matches) return;

    const ring = document.querySelector('.cursor');
    const dot  = document.querySelector('.cursor-dot');
    if (!ring || !dot) return;

    const m  = { x: innerWidth / 2,  y: innerHeight / 2 };
    const rp = { ...m };
    const dp = { ...m };

    addEventListener('mousemove', e => { m.x = e.clientX; m.y = e.clientY; }, { passive: true });

    (function tick() {
      rp.x += (m.x - rp.x) * .18; rp.y += (m.y - rp.y) * .18;
      dp.x += (m.x - dp.x) * .32; dp.y += (m.y - dp.y) * .32;
      ring.style.transform = `translate3d(${rp.x - 18}px,${rp.y - 18}px,0)`;
      dot.style.transform  = `translate3d(${dp.x - 2.5}px,${dp.y - 2.5}px,0)`;
      requestAnimationFrame(tick);
    })();

    document.querySelectorAll('a,button,input,select,textarea').forEach(addHover);
  }


  // ─────────────────────────────────────────────────────────────────────────
  // NAV SCROLL
  // ─────────────────────────────────────────────────────────────────────────
  function initNav() {
    const nav = document.querySelector('.nav');
    if (!nav) return;
    addEventListener('scroll', () => nav.classList.toggle('is-scrolled', scrollY > 10), { passive: true });
  }


  // ─────────────────────────────────────────────────────────────────────────
  // SCROLL REVEAL
  // ─────────────────────────────────────────────────────────────────────────
  function initReveal() {
    const io = new IntersectionObserver((entries, obs) => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        e.target.style.transitionDelay = (e.target.dataset.delay || 0) + 'ms';
        e.target.classList.add('is-in');
        obs.unobserve(e.target);
      });
    }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });

    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
  }

  function revealAll() {
    document.querySelectorAll('.reveal:not(.is-in)').forEach(el => {
      el.style.transitionDelay = (el.dataset.delay || 0) + 'ms';
      el.classList.add('is-in');
    });
  }


  // ─────────────────────────────────────────────────────────────────────────
  // GALLERY  — event delegation so it works with dynamically built thumbnails
  // ─────────────────────────────────────────────────────────────────────────
  function initGallery() {
    const mainImg  = document.getElementById('g-main-img');
    const thumbBox = document.getElementById('g-thumbs');
    if (!mainImg || !thumbBox) return;

    mainImg.style.transition = 'opacity .2s ease';

    thumbBox.addEventListener('click', e => {
      const thumb = e.target.closest('.g-thumb');
      if (!thumb) return;

      thumbBox.querySelectorAll('.g-thumb').forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');

      const img = thumb.querySelector('img');
      if (img) {
        mainImg.style.opacity = '0';
        setTimeout(() => {
          mainImg.src = img.src;
          mainImg.alt = img.alt || '';
          mainImg.style.opacity = '1';
        }, 200);
      }
    });
  }


  // ─────────────────────────────────────────────────────────────────────────
  // SAVE BUTTON
  // ─────────────────────────────────────────────────────────────────────────
  function initSave() {
    const btn = document.getElementById('detail-save-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
      const saved = this.classList.toggle('saved');
      const svg   = this.querySelector('svg');
      const span  = this.querySelector('span');

      if (saved) {
        this.style.cssText = 'background:var(--crimson,#C72C41);color:#fff;border-color:var(--crimson,#C72C41)';
        if (svg)  svg.setAttribute('fill', 'currentColor');
        if (span) span.textContent = ' Saved';
        toast('Property saved!', 'success');
      } else {
        this.style.cssText = '';
        if (svg)  svg.setAttribute('fill', 'none');
        if (span) span.textContent = ' Save';
        toast('Removed from saved.');
      }
    });
  }


  // ─────────────────────────────────────────────────────────────────────────
  // FOLLOW SYSTEM
  // ─────────────────────────────────────────────────────────────────────────

  function applyFollowUI(btn, following) {
    btn.classList.toggle('following', following);
    btn.textContent  = following ? 'Following' : 'Follow';
    btn.style.background  = following ? 'var(--crimson,#C72C41)' : '';
    btn.style.color       = following ? '#fff' : '';
    btn.style.borderColor = following ? 'var(--crimson,#C72C41)' : '';
  }

  async function initFollow(userId) {
    if (!userId) {
      console.warn('[SAMSAR] initFollow: no userId supplied');
      return;
    }

    const rawBtns = Array.from(document.querySelectorAll('[data-follow]'));
    if (!rawBtns.length) {
      console.warn('[SAMSAR] initFollow: no [data-follow] buttons found');
      return;
    }

    console.log(`[SAMSAR] Follow: setting up for agent user_id=${userId}`);

    // ── 1. Check current follow status ──────────────────────────────────────
    let isFollowing = false;

    try {
      const checkUrl = `${API.CHECK_FOLLOW}?user_id=${encodeURIComponent(userId)}`;
      console.log('[SAMSAR] Check follow URL:', checkUrl);

      const r = await fetch(checkUrl, { credentials: 'same-origin' });
      const raw = await r.text();

      console.log('[SAMSAR] check-follow raw response:', raw.slice(0, 200));

      if (r.status === 401) {
        // Not logged in — that's fine, button shows "Follow" and clicking prompts sign-in
        console.log('[SAMSAR] User not logged in; follow button will prompt sign-in on click.');
      } else if (r.ok) {
        const d = JSON.parse(raw);
        if (d.success) isFollowing = !!d.is_following;
      }
    } catch (err) {
      console.warn('[SAMSAR] Could not check follow status:', err.message);
    }

    // ── 2. Stamp userId, replace buttons to clear any stale listeners ────────
    const freshBtns = rawBtns.map(btn => {
      btn.dataset.followUserId = String(userId);
      const fresh = btn.cloneNode(true);   // carries data-follow-user-id across
      btn.replaceWith(fresh);
      applyFollowUI(fresh, isFollowing);
      addHover(fresh);                     // re-register cursor hover
      return fresh;
    });

    // ── 3. Click handler ──────────────────────────────────────────────────────
    freshBtns.forEach(btn => {
      btn.addEventListener('click', async function () {
        if (this.disabled) return;

        const currentlyFollowing = this.classList.contains('following');
        const targetId = this.dataset.followUserId;
        const endpoint = currentlyFollowing ? API.UNFOLLOW : API.FOLLOW;

        console.log(`[SAMSAR] Follow click → POST ${endpoint} following_id=${targetId}`);

        // Optimistic update
        this.disabled = true;
        applyFollowUI(this, !currentlyFollowing);

        try {
          const fd = new FormData();
          fd.append('following_id', targetId);

          const r = await fetch(endpoint, {
            method     : 'POST',
            body       : fd,
            credentials: 'same-origin',
          });

          const raw = await r.text();
          console.log(`[SAMSAR] ${endpoint} response [${r.status}]:`, raw.slice(0, 300));

          // ── Handle non-JSON (PHP errors, wrong path, etc.) ────────────────
          let d;
          try { d = JSON.parse(raw); }
          catch (_) {
            console.error('[SAMSAR] Non-JSON response — check PHP errors above:', raw.slice(0, 400));
            throw new Error('Unexpected server response (not JSON). See console.');
          }

          if (r.status === 401) {
            // Not logged in
            applyFollowUI(this, currentlyFollowing);
            toast('Please sign in to follow this agent.', 'warning');
          } else if (d && d.success) {
            isFollowing = !currentlyFollowing;
            toast(currentlyFollowing ? 'Unfollowed.' : 'Now following!', 'success');
          } else {
            // Server returned success:false with a message
            applyFollowUI(this, currentlyFollowing);
            toast(d?.message || 'Something went wrong.', 'error');
          }
        } catch (err) {
          console.error('[SAMSAR] Follow network/parse error:', err.message);
          applyFollowUI(this, currentlyFollowing);
          toast('Network error. Please try again.', 'error');
        } finally {
          this.disabled = false;
        }
      });
    });
  }


  // ─────────────────────────────────────────────────────────────────────────
  // SIMILAR PROPERTIES
  // ─────────────────────────────────────────────────────────────────────────
  function renderSimilar(list) {
    const el = document.getElementById('similar-properties');
    if (!el) return;

    if (!list || !list.length) {
      el.innerHTML = '<p style="color:#999;grid-column:1/-1;text-align:center;padding:20px 0">No similar properties found.</p>';
      return;
    }

    el.innerHTML = list.map(p => {
      const src   = imgUrl(p.main_image) || PLACEHOLDER_SIM;
      const price = p.price ? `${fmt(p.price)} MAD` : 'Price on request';
      return `
        <a href="03-property-details.php?id=${esc(p.id)}" class="sim-card">
          <div class="sim-img">
            <img src="${esc(src)}"
                 alt="${esc(p.title || 'Property')}"
                 loading="lazy"
                 onerror="this.src='${PLACEHOLDER_SIM}'" />
          </div>
          <span class="sim-type">${esc((p.property_type || 'Property').toUpperCase())}</span>
          <h3>${esc(p.title || 'Untitled')}</h3>
          <span class="sim-city">${esc(p.city || '')}</span>
          <strong>${esc(price)}</strong>
        </a>`;
    }).join('');

    // Attach cursor hover to new links
    el.querySelectorAll('a').forEach(addHover);
  }


  // ─────────────────────────────────────────────────────────────────────────
  // POPULATE PAGE WITH API DATA
  // ─────────────────────────────────────────────────────────────────────────
  function populate(p) {
    document.title = `${p.title || 'Property'} · SAMSAR`;

    // ── Breadcrumb + hero ────────────────────────────────────────────────────
    setText('breadcrumb-title', p.title, 'Property');
    setText('property-location',
      [p.city, p.district].filter(Boolean).join(' · '),
      'Morocco'
    );
    setText('property-title', p.title, 'Untitled Property');

    const meta = [
      p.bedrooms  ? `${p.bedrooms} bed`   : null,
      p.bathrooms ? `${p.bathrooms} bath`  : null,
      p.area      ? `${p.area} m²`         : null,
      p.property_type || null,
    ].filter(Boolean).join(' · ');
    setText('property-meta', meta, '');

    const priceEl = document.getElementById('property-price');
    if (priceEl) {
      priceEl.innerHTML = p.price
        ? `${fmt(p.price)} <small>MAD</small>`
        : 'Price on request';
    }

    // ── Gallery ──────────────────────────────────────────────────────────────
    const mainImg = document.getElementById('g-main-img');
    const thumbBox = document.getElementById('g-thumbs');

    const images  = (Array.isArray(p.images) ? p.images : []).map(imgUrl).filter(Boolean);
    const mainSrc = imgUrl(p.main_image) || images[0] || PLACEHOLDER;

    if (mainImg) {
      mainImg.src = mainSrc;
      mainImg.alt = p.title || 'Property';
      mainImg.onerror = () => { mainImg.src = PLACEHOLDER; };
    }

    if (thumbBox) {
      thumbBox.innerHTML = images.length
        ? images.map((src, i) => `
            <button class="g-thumb${i === 0 ? ' active' : ''}">
              <img src="${esc(src)}"
                   alt="${esc(p.title || 'Property')} ${i + 1}"
                   loading="lazy"
                   onerror="this.src='${PLACEHOLDER_SIM}'" />
            </button>`).join('')
        : '';
      thumbBox.querySelectorAll('button').forEach(addHover);
    }

    // ── Key facts ────────────────────────────────────────────────────────────
    setText('fact-bedrooms', p.bedrooms);
    setText('fact-bathrooms', p.bathrooms);
    setText('fact-area',   p.area ? `${p.area} m²` : null);
    setText('fact-type',   p.property_type);

    const statusEl = document.getElementById('fact-status');
    if (statusEl) {
      const labels = {
        available: 'Available', sale: 'For Sale', rent: 'For Rent',
        sold: 'Sold', rented: 'Rented', pending: 'Pending', draft: 'Draft',
      };
      const colors = {
        available: '#22c55e', sale: '#22c55e',
        sold: '#ef4444', rented: '#f97316',
      };
      statusEl.textContent  = labels[p.status] || p.status || '—';
      statusEl.style.color  = colors[p.status] || 'inherit';
    }

    const dateEl = document.getElementById('fact-date');
    if (dateEl && p.created_at) {
      dateEl.textContent = new Date(p.created_at).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
      });
    }

    // ── Description ──────────────────────────────────────────────────────────
    const descEl = document.getElementById('property-description');
    if (descEl) {
      if (p.description && p.description.trim()) {
        descEl.innerHTML = p.description.trim()
          .split(/\n+/)
          .filter(s => s.trim())
          .map(para => `<p>${esc(para.trim())}</p>`)
          .join('');
      } else {
        descEl.innerHTML = '<p style="color:#999">No description provided for this property.</p>';
      }
    }

    // ── Features ─────────────────────────────────────────────────────────────
    const featEl = document.getElementById('property-features');
    if (featEl) {
      const feats = [
        p.bedrooms    && `${p.bedrooms} Bedrooms`,
        p.bathrooms   && `${p.bathrooms} Bathrooms`,
        p.area        && `${p.area} m² Total Area`,
        p.property_type && `Type: ${p.property_type}`,
        p.city        && `City: ${p.city}`,
        p.district    && `District: ${p.district}`,
        p.status      && `Status: ${p.status}`,
      ].filter(Boolean);

      featEl.innerHTML = feats.length
        ? feats.map(f => `<li>${esc(f)}</li>`).join('')
        : '<li style="color:#999">No features listed.</li>';
    }

    // ── Agent card ───────────────────────────────────────────────────────────
    const avatarEl = document.getElementById('agent-avatar');
    if (avatarEl) {
      if (p.agent_avatar) {
        avatarEl.src = /^https?:\/\//.test(p.agent_avatar) || p.agent_avatar.startsWith('uploads/')
          ? p.agent_avatar
          : `uploads/profile/${p.agent_avatar}`;
      } else {
        avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(p.agentName || 'Agent')}&background=C72C41&color=fff&size=80`;
      }
      avatarEl.alt    = p.agentName || 'Agent';
      avatarEl.onerror = () => {
        avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(p.agentName || 'A')}&background=C72C41&color=fff&size=80`;
      };
    }

    setText('agent-name',     p.agentName, 'Agency');
    setText('agent-location', p.city ? `Listing agency · ${p.city}` : 'Listing agency');

    const bio = p.agencyName
      ? `${p.agencyName} — Specialists in ${p.property_type || 'luxury'} properties in ${p.city || 'Morocco'}.`
      : p.agentName
        ? `${p.agentName} — Professional real estate agent.`
        : 'Professional real estate agency.';
    setText('agent-bio', bio);

    // ── WhatsApp link ─────────────────────────────────────────────────────────
    const waEl = document.getElementById('whatsapp-link');
    if (waEl && p.agent_phone) {
      const phone = p.agent_phone.replace(/\D/g, '');
      if (phone) waEl.href = `https://wa.me/${phone}`;
    }

    // ── Agency profile link ───────────────────────────────────────────────────
    const agencyLink = document.getElementById('agency-profile-link');
    if (agencyLink && p.user_id) agencyLink.href = `05-agency-profile.php?id=${p.user_id}`;

    // ── Save button: stamp property id ────────────────────────────────────────
    const saveBtn = document.getElementById('detail-save-btn');
    if (saveBtn && p.id) saveBtn.dataset.propertyId = p.id;

    // ── Follow system ─────────────────────────────────────────────────────────
    initFollow(p.user_id);

    // ── Similar properties ────────────────────────────────────────────────────
    renderSimilar(p.similar || []);

    // ── Trigger reveal animations ─────────────────────────────────────────────
    setTimeout(revealAll, 80);
  }


  // ─────────────────────────────────────────────────────────────────────────
  // ERROR STATE
  // ─────────────────────────────────────────────────────────────────────────
  function showError(msg) {
    const c = document.querySelector('main .container');
    if (!c) return;
    const crumbs = c.querySelector('.crumbs');
    c.innerHTML = '';
    if (crumbs) c.appendChild(crumbs);
    c.insertAdjacentHTML('beforeend', `
      <div style="text-align:center;padding:80px 20px">
        <div style="font-size:48px;margin-bottom:16px">🏠</div>
        <h2 style="color:#dc3545;margin-bottom:8px">Unable to load property</h2>
        <p style="color:#666;font-size:13px;max-width:480px;margin:0 auto 24px;line-height:1.6">
          ${esc(msg)}
        </p>
        <a href="02-properties.php"
           style="display:inline-block;padding:12px 32px;background:#1a1a1a;
                  color:#fff;text-decoration:none;border-radius:4px;font-size:14px">
          Back to listings
        </a>
      </div>`);
  }


  // ─────────────────────────────────────────────────────────────────────────
  // MAIN LOAD — fetches property-details.php and populates the page
  // ─────────────────────────────────────────────────────────────────────────
  async function load() {
    const id = new URLSearchParams(location.search).get('id');

    if (!id) {
      showError('No property ID in the URL. Add ?id=123 to the address bar.');
      return;
    }

    const url = `${API.PROPERTY}?id=${encodeURIComponent(id)}`;
    console.log('[SAMSAR] → GET', url);

    try {
      const res = await fetch(url, { credentials: 'same-origin' });
      const raw = await res.text();

      // Always log what we got so PHP errors are visible in the console
      console.log(`[SAMSAR] ← HTTP ${res.status} | first 400 chars:`, raw.slice(0, 400));

      if (!res.ok) {
        // Try to extract a clean error message if PHP returned JSON
        let msg = `Server error ${res.status}`;
        try { msg = JSON.parse(raw).error || msg; } catch (_) { msg += ': ' + raw.slice(0, 200); }
        throw new Error(msg);
      }

      // Parse JSON
      let data;
      try {
        data = JSON.parse(raw);
      } catch (_) {
        throw new Error(
          'API returned non-JSON. Open the console to see the raw response and check for PHP errors.'
        );
      }

      if (data.error) throw new Error(data.error);

      populate(data);

    } catch (err) {
      console.error('[SAMSAR] load() failed:', err.message);
      showError(err.message);
    }
  }


  // ─────────────────────────────────────────────────────────────────────────
  // CONTACT FORM  (basic — prevents default, shows toast)
  // ─────────────────────────────────────────────────────────────────────────
  function initContactForm() {
    const form = document.getElementById('contact-form');
    if (!form) return;
    form.addEventListener('submit', e => {
      e.preventDefault();
      toast('Message sent! The agent will contact you soon.', 'success', 3500);
      form.reset();
    });
  }


  // ─────────────────────────────────────────────────────────────────────────
  // BOOT
  // ─────────────────────────────────────────────────────────────────────────
  initCursor();
  initNav();
  initReveal();
  initGallery();   // delegates events, safe to call before thumbnails exist
  initSave();
  initContactForm();
  load();           // fetches data → calls populate() → calls initFollow()

})();
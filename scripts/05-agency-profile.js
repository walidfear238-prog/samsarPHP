(function () {
    'use strict';

    // ─────────────────────────────────────────────────────────────────────
    // API ENDPOINTS — relative paths, resolve correctly from this page
    // ─────────────────────────────────────────────────────────────────────
    const API = {
        CHECK_FOLLOW  : 'api/follow/check-follow.php',    // GET  ?user_id=
        COUNT_FOLLOWER: 'api/follow/count-follower.php',  // GET  ?user_id=  (public)
        FOLLOW        : 'api/follow/follow.php',          // POST following_id
        UNFOLLOW      : 'api/follow/unfollow.php',         // POST following_id
    };

    // The logged-in user's id, or 0 if not authenticated. Set by 05-agency-profile.php.
    const CURRENT_USER_ID = parseInt(window.currentUserId, 10) || 0;

    // ─────────────────────────────────────────────────────────────────────
    // TOAST — small transient notification, bottom-center
    // ─────────────────────────────────────────────────────────────────────
    let _toastTimer;
    function toast(msg, type = 'neutral', ms = 3200) {
        let el = document.getElementById('_sm-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = '_sm-toast';
            Object.assign(el.style, {
                position: 'fixed', bottom: '28px', left: '50%',
                transform: 'translateX(-50%) translateY(14px)',
                padding: '11px 26px', borderRadius: '999px',
                fontSize: '13px', fontWeight: '500', letterSpacing: '.02em',
                opacity: '0', transition: 'opacity .25s ease, transform .25s ease',
                zIndex: '9999', pointerEvents: 'none', whiteSpace: 'nowrap',
                maxWidth: '90vw', overflow: 'hidden', textOverflow: 'ellipsis',
            });
            document.body.appendChild(el);
        }
        const colors = {
            success: ['#22c55e', '#fff'],
            error  : ['#ef4444', '#fff'],
            warning: ['#f59e0b', '#fff'],
            neutral: ['#1a1a1a', '#fff'],
        };
        const [bg, fg] = colors[type] || colors.neutral;
        el.style.background = bg;
        el.style.color = fg;
        el.textContent = msg;
        el.style.opacity = '1';
        el.style.transform = 'translateX(-50%) translateY(0)';
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateX(-50%) translateY(14px)';
        }, ms);
    }

    /** Send the visitor to sign in, then bring them straight back here. */
    function redirectToLogin() {
        const back = location.pathname + location.search;
        location.href = `08-login.php?redirect=${encodeURIComponent(back)}`;
    }

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

    const io = new IntersectionObserver((es, o) => es.forEach(e => { if (e.isIntersecting) { const d = e.target.dataset.delay || 0; e.target.style.transitionDelay = d + 'ms'; e.target.classList.add('is-in'); o.unobserve(e.target) } }), { threshold: .1 });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));

    function setText(id, val, fallback) {
        const el = document.getElementById(id);
        if (el) el.textContent = (val !== null && val !== undefined && val !== '') ? val : (fallback !== undefined ? fallback : '–');
    }

    // Resolve stored paths the same way the rest of the app does
    function logoUrl(path) {
        if (!path) return null;
        if (/^https?:\/\//.test(path)) return path;
        if (path.startsWith('uploads/')) return path;
        return 'uploads/profile/' + path;
    }
    function propImgUrl(path) {
        if (!path) return null;
        if (/^https?:\/\//.test(path)) return path;
        if (path.startsWith('uploads/')) return path;
        return 'uploads/property_images/' + path;
    }
    const PLACEHOLDER = 'https://placehold.co/600x450/f5f0eb/8ba3b0?text=No+Image';

    function formatPrice(price) {
        const n = parseFloat(price);
        return Number.isFinite(n) ? new Intl.NumberFormat('fr-MA').format(n) : (price || '0');
    }

    // Only this agency's own properties - real data from the database
    function renderProperties(properties) {
        const grid = document.getElementById('prop-grid');
        if (!grid) return;
        if (!properties.length) {
            grid.innerHTML = '<p style="grid-column:1/-1;color:var(--graphite)">' + (window.t ? window.t('agencyprofile.js.no_listings') : 'No listings yet.') + '</p>';
            return;
        }
        grid.innerHTML = properties.map((p, i) => {
            const img = propImgUrl(p.img) || PLACEHOLDER;
            const loc = [p.district, p.city].filter(Boolean).join(' · ') || (window.t ? window.t('properties.js.default_city') : 'Morocco');
            return `
  <a class="p-card" href="03-property-details.php?id=${p.id}" style="transition-delay:${i * 70}ms">
   <div class="p-img"><img src="${img}" alt="${p.title || ''}" loading="lazy" onerror="this.onerror=null;this.src='${PLACEHOLDER}'"/></div>
   <div class="p-body">
    <span class="p-loc">${loc}</span>
    <h3 class="p-title">${p.title || (window.t ? window.t('properties.js.default_title') : 'Property')}</h3>
    <span class="p-price">${formatPrice(p.price)} <small>${window.t ? window.t('unit.mad') : 'MAD'}</small></span>
   </div>
  </a>`;
        }).join('');
        document.querySelectorAll('.p-card').forEach(el => io.observe(el));
    }

    function renderNotFound() {
        setText('prof-eyebrow', 'SAMSAR');
        setText('prof-name', window.t ? window.t('agencyprofile.js.not_found') : 'Agency not found');
        setText('prof-location', '');
        setText('about-text', window.t ? window.t('agencyprofile.js.not_available') : 'This agency profile is not available.');
        const grid = document.getElementById('prop-grid');
        if (grid) grid.innerHTML = '<p style="grid-column:1/-1;color:var(--graphite)">' + (window.t ? window.t('agencyprofile.js.could_not_find') : 'This agency could not be found.') + '</p>';
        // No valid agency on this page — the follow/contact buttons have nothing to act on.
        hideActionButtons();
    }

    function hideActionButtons() {
        const followBtn = document.getElementById('follow-btn');
        const followCount = document.getElementById('follow-count');
        if (followBtn) followBtn.hidden = true;
        if (followCount) followCount.hidden = true;
        document.querySelectorAll('[data-contact-agency]').forEach(btn => { btn.hidden = true; });
    }

    // ─────────────────────────────────────────────────────────────────────
    // FOLLOW / UNFOLLOW
    // ─────────────────────────────────────────────────────────────────────

    function setBtnLoading(btn, loading) {
        btn.classList.toggle('is-loading', loading);
        btn.disabled = loading;
    }

    /** Paint the follow button + count for the given state. */
    function applyFollowUI(btn, following, count) {
        const dt = window.t || function (k, f) { return f; };
        btn.classList.toggle('following', following);
        btn.setAttribute('aria-pressed', following ? 'true' : 'false');
        const label = btn.querySelector('.btn-label');
        if (label) label.textContent = following
            ? dt('agencyprofile.unfollow', 'Unfollow')
            : dt('propdetails.follow', 'Follow');

        const countEl = document.getElementById('follow-count');
        if (countEl && typeof count === 'number') {
            countEl.hidden = false;
            const followersWord = dt('dash.followers', 'Followers');
            countEl.textContent = `${new Intl.NumberFormat('fr-MA').format(count)} ${followersWord.toLowerCase()}`;
        }
    }

    /**
     * Wires the Follow / Unfollow button for `agencyId`.
     * - Fetches persisted follow status + follower count on load.
     * - Handles loading state, optimistic update + rollback on failure.
     * - Unauthenticated visitors are sent to sign in when they click.
     */
    async function initFollow(agencyId, agencyName) {
        const btn = document.getElementById('follow-btn');
        if (!btn || !agencyId) return;

        // An agency can't follow itself.
        if (CURRENT_USER_ID && CURRENT_USER_ID === Number(agencyId)) {
            btn.hidden = true;
            const countEl = document.getElementById('follow-count');
            if (countEl) countEl.hidden = true;
            return;
        }

        let isFollowing = false;
        let followerCount = null;

        // Follower count is public — always safe to fetch, logged in or not.
        try {
            const r = await fetch(`${API.COUNT_FOLLOWER}?user_id=${encodeURIComponent(agencyId)}`, { credentials: 'same-origin' });
            if (r.ok) {
                const d = await r.json();
                if (d && d.success) followerCount = Number(d.count) || 0;
            }
        } catch (err) {
            console.warn('[SAMSAR] Could not load follower count:', err.message);
        }

        if (CURRENT_USER_ID) {
            try {
                const r = await fetch(`${API.CHECK_FOLLOW}?user_id=${encodeURIComponent(agencyId)}`, { credentials: 'same-origin' });
                if (r.ok) {
                    const d = await r.json();
                    if (d && d.success) isFollowing = !!d.is_following;
                }
            } catch (err) {
                console.warn('[SAMSAR] Could not check follow status:', err.message);
            }
        }

        applyFollowUI(btn, isFollowing, followerCount);

        btn.addEventListener('click', async () => {
            if (btn.disabled) return;

            // Not signed in — send them to log in, then back to this page.
            if (!CURRENT_USER_ID) {
                toast(window.t ? window.t('agencyprofile.js.signin_required', 'Please sign in to continue.') : 'Please sign in to continue.', 'warning');
                setTimeout(redirectToLogin, 900);
                return;
            }

            const wasFollowing = btn.classList.contains('following');
            const endpoint = wasFollowing ? API.UNFOLLOW : API.FOLLOW;
            const dt = window.t || function (k, f) { return f; };

            // Optimistic update
            setBtnLoading(btn, true);
            applyFollowUI(btn, !wasFollowing, followerCount === null ? null : followerCount + (wasFollowing ? -1 : 1));

            try {
                const fd = new FormData();
                fd.append('following_id', agencyId);

                const r = await fetch(endpoint, { method: 'POST', body: fd, credentials: 'same-origin' });
                const raw = await r.text();

                let d;
                try { d = JSON.parse(raw); }
                catch (_) { throw new Error('Unexpected server response (not JSON).'); }

                if (r.status === 401) {
                    applyFollowUI(btn, wasFollowing, followerCount);
                    toast(dt('agencyprofile.js.signin_required', 'Please sign in to continue.'), 'warning');
                    setTimeout(redirectToLogin, 900);
                } else if (d && d.success) {
                    isFollowing = !wasFollowing;
                    if (typeof d.followers_count === 'number') followerCount = d.followers_count;
                    else if (followerCount !== null) followerCount += wasFollowing ? -1 : 1;
                    applyFollowUI(btn, isFollowing, followerCount);

                    toast(
                        wasFollowing
                            ? `${dt('agencyprofile.js.unfollowed_prefix', 'You have unfollowed')} ${agencyName}`
                            : `${dt('agencyprofile.js.now_following_prefix', 'You are now following')} ${agencyName}`,
                        'success'
                    );
                } else {
                    // Server responded but declined the action (e.g. already following)
                    applyFollowUI(btn, wasFollowing, followerCount);
                    toast(d?.message || dt('common.something_wrong', 'Something went wrong.'), 'error');
                }
            } catch (err) {
                console.error('[SAMSAR] Follow network/parse error:', err.message);
                applyFollowUI(btn, wasFollowing, followerCount);
                toast(window.t ? window.t('common.network_error') : 'Network error. Please try again.', 'error');
            } finally {
                setBtnLoading(btn, false);
            }
        });
    }

    // ─────────────────────────────────────────────────────────────────────
    // CONTACT AGENCY — routes to the Messages page with the agency preselected
    // ─────────────────────────────────────────────────────────────────────
    function initContact(agencyId, agencyName, agencyAvatar) {
        const buttons = Array.from(document.querySelectorAll('[data-contact-agency]'));
        if (!buttons.length || !agencyId) return;

        // An agency can't message itself.
        if (CURRENT_USER_ID && CURRENT_USER_ID === Number(agencyId)) {
            buttons.forEach(btn => { btn.hidden = true; });
            return;
        }

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.disabled) return;

                if (!CURRENT_USER_ID) {
                    toast(window.t ? window.t('agencyprofile.js.signin_required', 'Please sign in to continue.') : 'Please sign in to continue.', 'warning');
                    setTimeout(redirectToLogin, 900);
                    return;
                }

                setBtnLoading(btn, true);
                const params = new URLSearchParams({
                    agencyId  : String(agencyId),
                    agencyName: agencyName || '',
                });
                if (agencyAvatar) params.set('agencyAvatar', agencyAvatar);
                location.href = `messages.php?${params.toString()}`;
            });
        });
    }

    // Load the real agency (by id) and its own properties from the database
    const agencyId = new URLSearchParams(location.search).get('id');

    if (!agencyId) {
        renderNotFound();
    } else {
        fetch(`api/get-agency-details.php?id=${encodeURIComponent(agencyId)}`)
            .then(res => { if (!res.ok) throw new Error('not found'); return res.json(); })
            .then(a => {
                document.title = `${a.name} · SAMSAR`;

                const fallbackAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(a.name)}&background=C72C41&color=fff&size=200`;
                const resolvedAvatar = logoUrl(a.logo) || fallbackAvatar;

                const logoEl = document.getElementById('prof-logo');
                if (logoEl) {
                    logoEl.src = resolvedAvatar;
                    logoEl.alt = a.name + ' logo';
                    logoEl.onerror = () => { logoEl.onerror = null; logoEl.src = fallbackAvatar; };
                }

                // Wire up the Follow and Contact agency buttons with the real agency data.
                initFollow(a.id, a.name);
                initContact(a.id, a.name, resolvedAvatar);

                setText('prof-eyebrow', (a.is_verified ? (window.t ? window.t('agencyprofile.js.verified_samsar') : 'Verified samsar') : (window.t ? window.t('agencyprofile.js.samsar_agency') : 'SAMSAR agency')) + (a.joinYear ? ` · ${window.t ? window.t('agencyprofile.js.since') : 'Since'} ${a.joinYear}` : ''));
                setText('prof-name', a.name, window.t ? window.t('agencyprofile.js.not_found_short') : 'Agency');
                setText('prof-location', [a.city, ...(a.districts || [])].filter(Boolean).join(' · '), window.t ? window.t('properties.js.default_city') : 'Morocco');

                setText('stat-listings', a.listingsCount ?? 0);
                setText('stat-years', `${a.yearsOnPlatform ?? 0} ${window.t ? window.t('agencies.js.yrs') : 'yrs'}`);
                setText('stat-rating', '—');
                setText('stat-reviews', window.t ? window.t('agencyprofile.zero_reviews') : '0 reviews');
                setText('stat-languages', '—');

                setText('tab-listings-count', a.listingsCount ?? 0);
                setText('tab-reviews-count', 0);

                setText('about-heading', (window.t ? window.t('agencyprofile.js.about_prefix') : 'About') + ' ' + a.name);
                const listingsWord = window.t ? window.t('agencyprofile.listings').toLowerCase() : (a.listingsCount === 1 ? 'listing' : 'listings');
                setText('about-text', `${a.name} ${window.t ? window.t('agencyprofile.js.is_agency_on_samsar') : 'is a real estate agency on SAMSAR'}${a.city ? `, ${window.t ? window.t('agencyprofile.js.based_in') : 'based in'} ${a.city}` : ''}${a.joinYear ? `, ${window.t ? window.t('agencyprofile.js.on_platform_since') : 'on the platform since'} ${a.joinYear}` : ''}, ${window.t ? window.t('agencyprofile.js.with') : 'with'} ${a.listingsCount ?? 0} ${window.t ? window.t('agencyprofile.js.active') : 'active'} ${listingsWord}.`);

                const specEl = document.getElementById('about-specialties');
                if (specEl) {
                    specEl.innerHTML = (a.specialties && a.specialties.length)
                        ? a.specialties.map(s => `<li>${s}</li>`).join('')
                        : '<li>' + (window.t ? window.t('agencyprofile.js.no_specialties') : 'No specialties listed yet.') + '</li>';
                }

                const reviewsEl = document.getElementById('reviews-list');
                if (reviewsEl) reviewsEl.innerHTML = '<p style="color:var(--graphite)">' + (window.t ? window.t('agencyprofile.js.no_reviews') : 'No reviews yet.') + '</p>';

                const phoneEl = document.getElementById('contact-phone');
                if (phoneEl) {
                    if (a.phone) { phoneEl.textContent = a.phone; phoneEl.href = `tel:${a.phone.replace(/\s+/g, '')}`; }
                    else { phoneEl.textContent = '–'; phoneEl.removeAttribute('href'); }
                }
                const emailEl = document.getElementById('contact-email');
                if (emailEl) {
                    if (a.email) { emailEl.textContent = a.email; emailEl.href = `mailto:${a.email}`; }
                    else { emailEl.textContent = '–'; emailEl.removeAttribute('href'); }
                }
                setText('contact-office', a.city, '–');
                setText('contact-hours', '–');

                renderProperties(a.properties || []);
            })
            .catch(() => renderNotFound());
    }

    // tabs
    document.querySelectorAll('.tab').forEach(t => t.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        document.querySelector(`[data-panel="${t.dataset.tab}"]`).classList.add('active');
    }));
})();

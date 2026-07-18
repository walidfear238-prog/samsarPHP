(function () {
    'use strict';

    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const fine = matchMedia('(pointer: fine)').matches;

    // Global properties array - will be filled from API
    let props = [];

    // Pagination config
    const PAGE_SIZE = 6;

    // State management
    const state = {
        type: 'all',
        status: 'all',
        bd: 0,
        ba: 0,
        q: '',
        min: 0,
        max: Infinity,
        cities: [],
        features: [],
        page: 1
    };

    // DOM elements
    const grid = document.getElementById('grid');
    const countEl = document.getElementById('result-count');
    const panel = document.getElementById('filter-panel');
    const paginationEl = document.getElementById('pagination');

    // Helper functions
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function moneyToNumber(value) {
        if (!value) return 0;
        const n = parseInt(String(value).replace(/[^0-9]/g, ''), 10);
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
            if (state.type !== 'all') {
                const propType = (p.property_type || '').toLowerCase();
                if (propType !== state.type) return false;
            }
            if (state.status !== 'all') {
                const propStatus = (p.status || '').toLowerCase();
                if (propStatus !== state.status) return false;
            }
            if (state.bd > 0 && (p.bedrooms || 0) < state.bd) return false;
            if (state.ba > 0 && (p.bathrooms || 0) < state.ba) return false;
            const priceNum = parseInt(p.price) || 0;
            if (state.min > 0 && priceNum < state.min) return false;
            if (state.max < Infinity && priceNum > state.max) return false;
            if (state.cities.length && p.city && !state.cities.includes(p.city.toLowerCase())) return false;
            if (state.q.trim()) {
                const searchText = ((p.title || '') + ' ' + (p.city || '') + ' ' + (p.property_type || '')).toLowerCase();
                if (!searchText.includes(state.q.toLowerCase())) return false;
            }
            return true;
        });
    }

    // --- Pagination helpers ---

    function getTotalPages(totalItems) {
        return Math.max(1, Math.ceil(totalItems / PAGE_SIZE));
    }

    function getPageFromUrl() {
        const params = new URLSearchParams(window.location.search);
        const p = parseInt(params.get('page'), 10);
        return Number.isFinite(p) && p > 0 ? p : 1;
    }

    function updateUrlPage(page, push) {
        const url = new URL(window.location.href);
        if (page > 1) {
            url.searchParams.set('page', page);
        } else {
            url.searchParams.delete('page');
        }
        const method = push ? 'pushState' : 'replaceState';
        window.history[method]({ page }, '', url);
    }

    function scrollToResultsTop() {
        const target = document.querySelector('.results-head') || grid;
        if (target && target.scrollIntoView) {
            target.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'start' });
        }
    }

    function goToPage(n) {
        const totalPages = getTotalPages(filteredList().length);
        n = Math.max(1, Math.min(n, totalPages));
        if (n === state.page) return;
        state.page = n;
        updateUrlPage(n, true);

        if (grid && !reduced) {
            grid.classList.add('is-paging');
            setTimeout(() => {
                render();
                grid.classList.remove('is-paging');
                scrollToResultsTop();
            }, 180);
        } else {
            render();
            scrollToResultsTop();
        }
    }

    function renderPagination(totalItems, totalPages) {
        if (!paginationEl) return;

        if (totalItems === 0 || totalPages <= 1) {
            paginationEl.innerHTML = '';
            paginationEl.classList.add('is-hidden');
            return;
        }
        paginationEl.classList.remove('is-hidden');

        const current = state.page;
        const isMobile = window.innerWidth <= 680;
        const delta = isMobile ? 1 : 2;

        let start = Math.max(2, current - delta);
        let end = Math.min(totalPages - 1, current + delta);

        const pages = [1];
        if (start > 2) pages.push('…');
        for (let i = start; i <= end; i++) pages.push(i);
        if (end < totalPages - 1) pages.push('…');
        if (totalPages > 1) pages.push(totalPages);

        const firstLabel = window.t ? window.t('properties.pagination.first', 'First page') : 'First page';
        const lastLabel = window.t ? window.t('properties.pagination.last', 'Last page') : 'Last page';
        const prevLabel = window.t ? window.t('properties.pagination.prev', '← Prev') : '← Prev';
        const nextLabel = window.t ? window.t('properties.pagination.next', 'Next →') : 'Next →';

        const chevronsDouble = (flip) => `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="${flip ? 'transform:rotate(180deg)' : ''}"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>`;
        const chevronSingle = (flip) => `<svg class="chev-single" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="${flip ? 'transform:rotate(180deg)' : ''}"><polyline points="15 18 9 12 15 6"></polyline></svg>`;

        let html = '';
        html += `<button type="button" class="page-btn page-edge" data-action="first" aria-label="${firstLabel}" ${current === 1 ? 'disabled' : ''}>${chevronsDouble(false)}</button>`;
        html += `<button type="button" class="page-btn" data-action="prev" ${current === 1 ? 'disabled' : ''}>${chevronSingle(false)}<span>${prevLabel}</span></button>`;

        pages.forEach(p => {
            if (p === '…') {
                html += `<span class="page-num page-ellipsis" aria-hidden="true">…</span>`;
            } else {
                html += `<button type="button" class="page-num${p === current ? ' active' : ''}" data-page="${p}" ${p === current ? 'aria-current="page"' : ''}>${p}</button>`;
            }
        });

        html += `<button type="button" class="page-btn" data-action="next" ${current === totalPages ? 'disabled' : ''}><span>${nextLabel}</span>${chevronSingle(true)}</button>`;
        html += `<button type="button" class="page-btn page-edge" data-action="last" aria-label="${lastLabel}" ${current === totalPages ? 'disabled' : ''}>${chevronsDouble(true)}</button>`;

        paginationEl.innerHTML = html;
    }

    function formatPrice(price) {
        if (!price) return '0';
        const num = parseInt(price);
        return num.toLocaleString();
    }

    function getStatusBadge(p) {
        const status = (p.status || '').toLowerCase();
        if (status === 'rent') return window.t ? window.t('card.forrent') : 'For Rent';
        if (status === 'rented') return window.t ? window.t('propstatus.rented') : 'Rented';
        if (status === 'sold') return window.t ? window.t('propstatus.sold') : 'Sold';
        return window.t ? window.t('card.forsale') : 'For Sale';
    }

    function getBadgeClass(p) {
        const status = (p.status || '').toLowerCase();
        if (status === 'sold') return 'crimson';
        if (status === 'rented') return 'crimson';
        return '';
    }

    function getImageUrl(p) {
        if (p.imgs) return p.imgs;
        if (p.img) return `uploads/property_images/${p.img}`;
        return 'https://placehold.co/600x400/eef2f5/8ba3b0?text=No+Image';
    }

    // Show small green toast message
    function showToast(message, isError = false) {
        // Remove existing toast if any
        const existingToast = document.querySelector('.favorite-toast');
        if (existingToast) existingToast.remove();
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'favorite-toast';
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: ${isError ? '#dc3545' : '#28a745'};
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            z-index: 10000;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-family: 'Inter', sans-serif;
        `;
        
        // Add animation styles if not already present
        if (!document.querySelector('#toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes fadeOut {
                    from {
                        opacity: 1;
                    }
                    to {
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        document.body.appendChild(toast);
        
        // Auto remove after 2 seconds
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }

    // RENDER FUNCTION - displays properties from database
    function render() {
        if (!grid) return;
        const list = filteredList();

        if (list.length === 0) {
            grid.innerHTML = `
                <div class="empty-results" style="grid-column:1/-1; text-align:center; padding:4rem;">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <circle cx="11" cy="11" r="8" stroke="currentColor"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <h3>${window.t ? window.t('properties.js.empty.title') : 'No properties found'}</h3>
                    <p>${window.t ? window.t('properties.js.empty.text') : 'Try removing one or more filters to see more listings.'}</p>
                </div>
            `;
            if (countEl) countEl.textContent = '0';
            renderPagination(0, 1);
            return;
        }

        // Paginate: clamp current page to a valid range, then slice out this page's items
        const totalPages = getTotalPages(list.length);
        if (state.page > totalPages) state.page = totalPages;
        if (state.page < 1) state.page = 1;
        const startIdx = (state.page - 1) * PAGE_SIZE;
        const pageItems = list.slice(startIdx, startIdx + PAGE_SIZE);
        updateUrlPage(state.page, false);

        grid.innerHTML = pageItems.map((p, i) => {
            const title = escapeHtml(p.title) || (window.t ? window.t('properties.js.default_title') : 'Property');
            const city = escapeHtml(p.city) || (window.t ? window.t('properties.js.default_city') : 'Morocco');
            const propertyType = escapeHtml(p.property_type) || (window.t ? window.t('properties.js.default_title') : 'Property');
            const bedrooms = p.bedrooms || 0;
            const bathrooms = p.bathrooms || 0;
            const area = p.area || 0;
            const price = formatPrice(p.price);
            const badgeText = getStatusBadge(p);
            const badgeClass = getBadgeClass(p);
            const imgUrl = getImageUrl(p);

            return `
                <article class="card is-in" style="transition-delay:${Math.min(i * 30, 300)}ms">
                    <div class="card-media">
                        <img src="${imgUrl}" alt="${title}" loading="lazy" onerror="this.src='https://placehold.co/600x400/eef2f5/8ba3b0?text=No+Image'"/>
                        <span class="card-badge ${badgeClass}">${badgeText}</span>
                        <button class="card-fav" data-fav data-property-id="${p.id}" aria-label="Save">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="card-loc">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>${city} · ${propertyType}</span>
                        </div>
                        <h3 class="card-title">
                            <a href="03-property-details.php?id=${p.id}">${title}</a>
                        </h3>
                        <div class="card-specs">
                            <span>${bedrooms} ${window.t ? window.t('unit.bd') : 'bd'}</span>
                            <span>${bathrooms} ${window.t ? window.t('unit.ba') : 'ba'}</span>
                            <span>${area} m²</span>
                        </div>
                        <div class="card-foot">
                            <span class="card-price">${price} <small>${window.t ? window.t('unit.mad') : 'MAD'}</small></span>
                            <a class="view-details" href="03-property-details.php?id=${p.id}">
                                ${window.t ? window.t('card.viewdetails') : 'View details'} <span class="arrow">→</span>
                            </a>
                        </div>
                    </div>
                </article>
            `;
        }).join('');

        if (countEl) countEl.textContent = list.length;

        // Attach favorite button listeners
        grid.querySelectorAll('[data-fav]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                let property_id = this.getAttribute('data-property-id');
                
                if (!property_id) {
                    console.error('No property ID found');
                    return;
                }
                
                const formData = new FormData();
                formData.append('property_id', property_id);
                
                fetch('api/favorits/add-to-favorit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.classList.toggle('active');
                        showToast(window.t ? ('✓ ' + window.t('properties.js.added_fav')) : '✓ Added to favorites');
                    } else {
                        if (data.message === 'User not logged in' || data.message === (window.t ? window.t('api.err.user_not_logged_in') : '')) {
                            showToast(window.t ? window.t('properties.js.login_to_fav') : 'Please login to add favorites', true);
                            setTimeout(() => {
                                window.location.href = '08-login.php';
                            }, 1500);
                        } else if (data.message === 'Property already in favorites' || data.message === (window.t ? window.t('api.favorites.already') : '')) {
                            showToast(window.t ? window.t('api.favorites.already') : 'Property already in favorites', true);
                        } else {
                            showToast(data.message, true);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(window.t ? window.t('properties.js.add_fav_error') : 'Error adding to favorites', true);
                });
            });
        });

        renderPagination(list.length, totalPages);
    }

    function setActive(groupSelector, activeEl) {
        document.querySelectorAll(groupSelector).forEach(x => x.classList.remove('active'));
        activeEl.classList.add('active');
    }

    function loadProperties() {
        fetch("api/upload-all-properties.php")
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                return res.json();
            })
            .then(data => {
                if (data && Array.isArray(data)) {
                    props = data;
                    state.page = getPageFromUrl();
                    render();
                } else {
                    console.error('Invalid data format from API');
                    showError(window.t ? window.t('properties.js.no_data') : 'No properties found in database');
                }
            })
            .catch(error => {
                console.error('Error loading properties:', error);
                showError(window.t ? window.t('properties.js.load_failed') : 'Failed to load properties. Please check the API connection.');
            });
    }

    function showError(message) {
        if (!grid) return;
        grid.innerHTML = `
            <div class="empty-results" style="grid-column:1/-1; text-align:center; padding:4rem;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <circle cx="12" cy="16" r="0.5" fill="currentColor" stroke="none"/>
                </svg>
                <h3>${window.t ? window.t('common.error') : 'Error'}</h3>
                <p>${message}</p>
            </div>
        `;
        if (countEl) countEl.textContent = '0';
    }

    // Initialize event listeners
    if (panel) {
        panel.addEventListener('click', e => {
            const chip = e.target.closest('.filter-chip');
            if (chip) {
                e.preventDefault();
                setActive('.filter-chip', chip);
                state.type = chip.dataset.v || 'all';
                state.page = 1;
                render();
                return;
            }
            const seg = e.target.closest('.seg-btn');
            if (seg) {
                e.preventDefault();
                setActive('.seg-btn', seg);
                state.status = seg.dataset.v || 'all';
                state.page = 1;
                render();
                return;
            }
            const pill = e.target.closest('.pill');
            if (pill) {
                e.preventDefault();
                const row = pill.closest('.pill-row');
                if (row) {
                    row.querySelectorAll('.pill').forEach(x => x.classList.remove('active'));
                    pill.classList.add('active');
                    const group = row.dataset.group;
                    if (group === 'bd') state.bd = parseInt(pill.dataset.v) || 0;
                    if (group === 'ba') state.ba = parseInt(pill.dataset.v) || 0;
                    state.page = 1;
                    render();
                }
            }
        });
    }

    const searchInput = document.getElementById('f-search');
    if (searchInput) searchInput.addEventListener('input', e => { state.q = e.target.value; state.page = 1; render(); });

    ['f-min-price', 'f-max-price'].forEach(id => {
        const input = document.getElementById(id);
        if (input) input.addEventListener('input', () => { state.page = 1; render(); });
    });

    document.querySelectorAll('[data-city], [data-feature]').forEach(input => input.addEventListener('change', () => { state.page = 1; render(); }));

    const applyBtn = document.getElementById('apply-filters');
    if (applyBtn) applyBtn.addEventListener('click', () => { state.page = 1; render(); });

    const resetBtn = document.getElementById('reset-filters');
    if (resetBtn) resetBtn.addEventListener('click', () => {
        state.type = 'all';
        state.status = 'all';
        state.bd = 0;
        state.ba = 0;
        state.q = '';
        state.min = 0;
        state.max = Infinity;
        state.cities = [];
        state.features = [];
        state.page = 1;

        if (searchInput) searchInput.value = '';
        const minInput = document.getElementById('f-min-price');
        const maxInput = document.getElementById('f-max-price');
        if (minInput) minInput.value = '';
        if (maxInput) maxInput.value = '';

        document.querySelectorAll('[data-city], [data-feature]').forEach(x => x.checked = false);
        document.querySelectorAll('.filter-chip,.seg-btn,.pill').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('[data-v="all"],[data-v="0"]').forEach(el => el.classList.add('active'));
        render();
    });

    // Pagination controls: page numbers, prev/next, first/last
    if (paginationEl) {
        paginationEl.addEventListener('click', e => {
            const btn = e.target.closest('[data-page], [data-action]');
            if (!btn || btn.disabled) return;
            e.preventDefault();

            const totalPages = getTotalPages(filteredList().length);
            let target = state.page;
            if (btn.dataset.page) {
                target = parseInt(btn.dataset.page, 10);
            } else if (btn.dataset.action === 'prev') {
                target = state.page - 1;
            } else if (btn.dataset.action === 'next') {
                target = state.page + 1;
            } else if (btn.dataset.action === 'first') {
                target = 1;
            } else if (btn.dataset.action === 'last') {
                target = totalPages;
            }
            goToPage(target);
        });
    }

    // Keep the pagination window (and mobile/desktop sizing) in sync with the browser back/forward buttons
    window.addEventListener('popstate', () => {
        state.page = getPageFromUrl();
        render();
    });

    // Re-render pagination on resize so the number of visible page pills adapts (fewer on mobile)
    let paginationResizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(paginationResizeTimer);
        paginationResizeTimer = setTimeout(() => {
            renderPagination(filteredList().length, getTotalPages(filteredList().length));
        }, 150);
    });

    const ftToggle = document.getElementById('ft-toggle');
    if (ftToggle && panel) ftToggle.addEventListener('click', () => panel.classList.toggle('is-open'));

    document.querySelectorAll('.vt-btn').forEach(b => b.addEventListener('click', () => {
        document.querySelectorAll('.vt-btn').forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        if (grid) grid.classList.toggle('list-view', b.dataset.view === 'list');
    }));

    const nav = document.querySelector('.nav');
    if (nav) window.addEventListener('scroll', () => nav.classList.toggle('is-scrolled', window.scrollY > 10), { passive: true });

    // Custom cursor
    if (fine && !reduced) {
        const r = document.querySelector('.cursor'), d = document.querySelector('.cursor-dot');
        if (r && d) {
            const t = { x: window.innerWidth / 2, y: window.innerHeight / 2 }, rp = { ...t }, dp = { ...t };
            window.addEventListener('mousemove', e => { t.x = e.clientX; t.y = e.clientY; }, { passive: true });
            (function loop() {
                rp.x += (t.x - rp.x) * 0.18;
                rp.y += (t.y - rp.y) * 0.18;
                dp.x += (t.x - dp.x) * 0.32;
                dp.y += (t.y - dp.y) * 0.32;
                r.style.transform = `translate3d(${rp.x - 18}px,${rp.y - 18}px,0)`;
                d.style.transform = `translate3d(${dp.x - 2.5}px,${dp.y - 2.5}px,0)`;
                requestAnimationFrame(loop);
            })();
            document.querySelectorAll('a,button,input,select,label,.card').forEach(el => {
                el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover'); });
                el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover'); });
            });
        }
    }

    // Start - load properties from database
    loadProperties();
})();
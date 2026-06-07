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
    document.querySelectorAll('a,button,input,select,textarea').forEach(el => {
      el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover') });
      el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover') });
    });
  }
  const nav = document.querySelector('.nav');
  addEventListener('scroll', () => nav.classList.toggle('is-scrolled', scrollY > 10), { passive: true });

  // gallery swap
  const main = document.getElementById('g-main-img');
  document.querySelectorAll('.g-thumb').forEach(t => t.addEventListener('click', () => {
    document.querySelectorAll('.g-thumb').forEach(x => x.classList.remove('active'));
    t.classList.add('active');
    const src = t.querySelector('img').src.replace('w=600', 'w=1600');
    main.style.opacity = '0';
    setTimeout(() => { main.src = src; main.style.opacity = '1' }, 200);
  }));

  // reveals
  const io = new IntersectionObserver((es, o) => es.forEach(e => { if (e.isIntersecting) { const d = e.target.dataset.delay || 0; e.target.style.transitionDelay = d + 'ms'; e.target.classList.add('is-in'); o.unobserve(e.target) } }), { threshold: .12, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.reveal').forEach(el => io.observe(el));

  // Save / Favorite button toggle
  const saveBtn = document.getElementById('detail-save-btn');
  if (saveBtn) {
    saveBtn.addEventListener('click', e => {
      e.preventDefault();
      const svg = saveBtn.querySelector('svg');
      if (saveBtn.classList.toggle('saved')) {
        saveBtn.style.background = 'var(--crimson)';
        saveBtn.style.color = '#fff';
        saveBtn.style.borderColor = 'var(--crimson)';
        svg.setAttribute('fill', 'currentColor');
        saveBtn.firstChild.textContent = ' Saved';
      } else {
        saveBtn.style.background = '';
        saveBtn.style.color = '';
        saveBtn.style.borderColor = '';
        svg.setAttribute('fill', 'none');
        saveBtn.firstChild.textContent = ' Save';
      }
    });
  }

  // Follow button toggle (works anywhere via [data-follow])
  document.querySelectorAll('[data-follow]').forEach(btn => {
    btn.addEventListener('click', () => {
      const isFollowing = btn.classList.toggle('following');
      btn.textContent = isFollowing ? 'Following' : 'Follow';
      if (isFollowing) { btn.style.background = 'var(--crimson)'; btn.style.color = '#fff'; btn.style.borderColor = 'var(--crimson)'; }
      else { btn.style.background = ''; btn.style.color = ''; btn.style.borderColor = ''; }
    });
  });

  // contact form
  const form = document.getElementById('contact-form');
  form.addEventListener('submit', e => {
    e.preventDefault();
    const b = form.querySelector('button[type="submit"]'), o = b.textContent;
    b.textContent = 'Sent — agency will reply soon ✓'; b.disabled = true;
    setTimeout(() => { b.textContent = o; b.disabled = false; form.reset() }, 1800);
  });
})();

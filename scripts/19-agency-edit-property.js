(function(){
 'use strict';
 const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
 const fine = matchMedia('(pointer: fine)').matches;

 /* ===== CUSTOM CURSOR ===== */
 if(fine && !reduced){
  const r = document.querySelector('.cursor');
  const d = document.querySelector('.cursor-dot');
  const t = {x: innerWidth/2, y: innerHeight/2}, rp = {...t}, dp = {...t};
  addEventListener('mousemove', e => { t.x = e.clientX; t.y = e.clientY; }, {passive:true});
  (function loop(){
    rp.x += (t.x - rp.x) * .18;
    rp.y += (t.y - rp.y) * .18;
    dp.x += (t.x - dp.x) * .32;
    dp.y += (t.y - dp.y) * .32;
    r.style.transform = `translate3d(${rp.x-16}px, ${rp.y-16}px, 0)`;
    d.style.transform = `translate3d(${dp.x-2.5}px, ${dp.y-2.5}px, 0)`;
    requestAnimationFrame(loop);
  })();
  document.querySelectorAll('a, button, input, select, textarea, label').forEach(el => {
    el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover'); });
    el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover'); });
  });
 }

 /* ===== FEATURE TAGS TOGGLE ===== */
 document.querySelectorAll('.tag-pill').forEach(t => {
  t.addEventListener('click', () => t.classList.toggle('active'));
 });

 /* ===== THUMB REMOVE (existing photos) ===== */
 function bindThumbRemove(scope){
  scope.querySelectorAll('.thumb-rm').forEach(b => {
    if(b.dataset.bound) return;
    b.dataset.bound = '1';
    b.addEventListener('click', e => {
      e.preventDefault();
      const th = b.closest('.thumb');
      th.style.opacity = '0';
      th.style.transform = 'scale(.9)';
      setTimeout(() => {
        const wasCover = th.classList.contains('cover');
        th.remove();
        if(wasCover){
          const first = document.querySelector('#thumbs .thumb');
          if(first) first.classList.add('cover');
        }
      }, 250);
    });
  });
 }
 bindThumbRemove(document);

 /* ===== UPLOAD ===== */
 const up = document.getElementById('upload');
 const file = document.getElementById('file');
 const thumbs = document.getElementById('thumbs');

 up.addEventListener('click', () => file.click());

 ['dragenter','dragover'].forEach(ev =>
  up.addEventListener(ev, e => { e.preventDefault(); up.classList.add('drag'); })
 );
 ['dragleave','drop'].forEach(ev =>
  up.addEventListener(ev, e => { e.preventDefault(); up.classList.remove('drag'); })
 );
 up.addEventListener('drop', e => handleFiles(e.dataTransfer.files));
 file.addEventListener('change', e => handleFiles(e.target.files));

 function handleFiles(files){
  Array.from(files).forEach(f => {
    if(!f.type.startsWith('image/')) return;
    const url = URL.createObjectURL(f);
    const d = document.createElement('div');
    d.className = 'thumb';
    d.innerHTML = `
      <img src="${url}" alt=""/>
      <button type="button" class="thumb-rm" aria-label="Remove">✕</button>
    `;
    thumbs.appendChild(d);
    bindThumbRemove(d);
  });
 }

 /* ===== TOAST ===== */
 function showToast(){
  const t = document.getElementById('toast');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2200);
 }

 /* ===== UNPUBLISH TOGGLE ===== */
 const pill = document.getElementById('status-pill');
 const unBtn = document.getElementById('unpublish');
 if(unBtn && pill){
  unBtn.addEventListener('click', () => {
    if(pill.classList.contains('unpublished')){
      pill.classList.remove('unpublished');
      pill.textContent = 'Published';
      unBtn.textContent = 'Unpublish';
    } else {
      pill.classList.add('unpublished');
      pill.textContent = 'Unpublished';
      unBtn.textContent = 'Republish';
    }
  });
 }

 /* ===== DELETE CONFIRM ===== */
 const cf = document.getElementById('confirm');
 document.getElementById('delete-btn').addEventListener('click', () => cf.classList.add('is-open'));
 cf.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', () => cf.classList.remove('is-open')));
 document.getElementById('cf-confirm').addEventListener('click', () => {
  cf.classList.remove('is-open');
  setTimeout(() => { location.href = '17-agency-properties.php'; }, 300);
 });
 addEventListener('keydown', e => { if(e.key === 'Escape') cf.classList.remove('is-open'); });

 /* ===== SAVE ===== */
 document.getElementById('prop-form').addEventListener('submit', e => {
  e.preventDefault();
  showToast();
 });
})();

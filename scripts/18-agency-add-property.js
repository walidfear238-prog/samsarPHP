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

 /* ===== IMAGE UPLOAD (drag/drop + click) ===== */
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
    const isFirst = thumbs.children.length === 0;
    const d = document.createElement('div');
    d.className = 'thumb' + (isFirst ? ' cover' : '');
    d.innerHTML = `
      <img src="${url}" alt=""/>
      <button type="button" class="thumb-rm" aria-label="Remove">✕</button>
    `;
    d.querySelector('.thumb-rm').addEventListener('click', e => {
      e.preventDefault();
      d.style.opacity = '0';
      d.style.transform = 'scale(.9)';
      setTimeout(() => {
        d.remove();
        const first = thumbs.querySelector('.thumb');
        if(first) first.classList.add('cover');
      }, 250);
    });
    thumbs.appendChild(d);
  });
 }

 /* ===== TOAST ===== */
 function showToast(){
  const t = document.getElementById('toast');
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2200);
 }

 /* ===== SAVE DRAFT ===== */
 const draftBtn = document.getElementById('save-draft');
 if(draftBtn){
  draftBtn.addEventListener('click', () => {
    const orig = draftBtn.textContent;
    draftBtn.textContent = 'Draft saved ✓';
    draftBtn.disabled = true;
    setTimeout(() => { draftBtn.textContent = orig; draftBtn.disabled = false; }, 1600);
  });
 }

 /* ===== FORM SUBMIT ===== */
 document.getElementById('prop-form').addEventListener('submit', e => {
  e.preventDefault();
  showToast();
  setTimeout(() => { location.href = '17-agency-properties.php'; }, 1400);
 });
})();

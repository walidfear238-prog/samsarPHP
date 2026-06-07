(function(){
 'use strict';
 const reduced=matchMedia('(prefers-reduced-motion: reduce)').matches;
 const fine=matchMedia('(pointer: fine)').matches;
 requestAnimationFrame(()=>document.body.classList.add('is-entering'));
 setTimeout(()=>document.body.classList.remove('is-entering'),900);
 if(!reduced){document.addEventListener('click',e=>{const a=e.target.closest('a');if(!a)return;const href=a.getAttribute('href');if(!href||href.startsWith('#')||href.startsWith('mailto:')||href.startsWith('tel:')||a.target==='_blank')return;const url=new URL(a.href,location.href);if(url.origin!==location.origin)return;e.preventDefault();document.body.classList.add('is-leaving');setTimeout(()=>location.href=a.href,700)})}
 if(fine&&!reduced){
  const r=document.querySelector('.cursor'),d=document.querySelector('.cursor-dot');
  const t={x:innerWidth/2,y:innerHeight/2},rp={...t},dp={...t};
  addEventListener('mousemove',e=>{t.x=e.clientX;t.y=e.clientY},{passive:true});
  (function loop(){rp.x+=(t.x-rp.x)*.18;rp.y+=(t.y-rp.y)*.18;dp.x+=(t.x-dp.x)*.32;dp.y+=(t.y-dp.y)*.32;
   r.style.transform=`translate3d(${rp.x-16}px,${rp.y-16}px,0)`;d.style.transform=`translate3d(${dp.x-2.5}px,${dp.y-2.5}px,0)`;
   requestAnimationFrame(loop)})();
  document.querySelectorAll('a,button,input').forEach(el=>{
   el.addEventListener('mouseenter',()=>{r.classList.add('is-hover');d.classList.add('is-hover')});
   el.addEventListener('mouseleave',()=>{r.classList.remove('is-hover');d.classList.remove('is-hover')});
  });
 }
 const favs=[
  {t:'Villa Tazri',l:'Palmeraie · Marrakech',p:'12,400,000',u:'MAD',b:5,ba:6,a:620,img:'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=700&q=80',note:'Schedule visit week of Dec 8.'},
  {t:'Riad Souira',l:'Medina · Essaouira',p:'38,000',u:'MAD / mo',b:4,ba:4,a:310,img:'https://images.unsplash.com/photo-1542718610-a1d656d1884c?auto=format&fit=crop&w=700&q=80'},
  {t:'Penthouse Lumière',l:'Anfa · Casablanca',p:'7,950,000',u:'MAD',b:3,ba:3,a:240,img:'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=700&q=80',note:'Compare to Villa Atlas before deciding.'},
  {t:'Villa Atlas',l:'Souissi · Rabat',p:'18,500,000',u:'MAD',b:6,ba:7,a:740,img:'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=700&q=80'},
  {t:'Riad Yasmine',l:'Medina · Marrakech',p:'5,200,000',u:'MAD',b:6,ba:5,a:380,img:'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=700&q=80'},
  {t:'Villa Ocean',l:'Essaouira Coast',p:'22,000,000',u:'MAD',b:7,ba:8,a:920,img:'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=700&q=80'},
  {t:'Apartment Marina',l:'Tangier Hills',p:'2,800,000',u:'MAD',b:2,ba:2,a:120,img:'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=700&q=80'},
  {t:'Riad Bahia',l:'Medina · Fès',p:'4,100,000',u:'MAD',b:5,ba:4,a:290,img:'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=700&q=80'},
 ];
 const grid=document.getElementById('fav-grid');
 grid.innerHTML=favs.map((p,i)=>`
  <article class="fav-card" style="transition-delay:${i*60}ms">
   <div class="fav-img">
    <img src="${p.img}" alt="${p.t}" loading="lazy"/>
    <span class="fav-check" data-check>✓</span>
    <div class="fav-tools">
     <button class="f-icon heart-on" data-heart aria-label="Remove">♥</button>
     <a class="f-icon" href="15-user-messages.php" aria-label="Message">✉</a>
    </div>
   </div>
   <div class="fav-body">
    <span class="fav-loc">${p.l}</span>
    <h3 class="fav-title"><a href="03-property-details.php">${p.t}</a></h3>
    <div class="fav-specs"><span>${p.b} bd</span><span>${p.ba} ba</span><span>${p.a} m²</span></div>
    <div class="fav-foot"><span class="fav-price">${p.p} <small>${p.u}</small></span><a href="03-property-details.php" style="color:var(--crimson);font-size:13px;font-weight:500">View →</a></div>
    ${p.note?`<div class="fav-note">📝 ${p.note}</div>`:''}
   </div>
  </article>`).join('');

 grid.querySelectorAll('[data-check]').forEach(c=>c.addEventListener('click',e=>{e.preventDefault();c.classList.toggle('checked')}));
 grid.querySelectorAll('[data-heart]').forEach(h=>h.addEventListener('click',e=>{
  e.preventDefault();const card=h.closest('.fav-card');
  card.style.transform='translateY(20px)';card.style.opacity='0';
  setTimeout(()=>{card.remove();const c=document.querySelectorAll('.fav-card').length;document.getElementById('count').textContent=c;if(c===0)document.getElementById('empty').hidden=false},400);
 }));

 const io=new IntersectionObserver((es,o)=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('is-in');o.unobserve(e.target)}}),{threshold:.05});
 document.querySelectorAll('.fav-card').forEach(el=>io.observe(el));

 document.querySelectorAll('.ct').forEach(t=>t.addEventListener('click',()=>{document.querySelectorAll('.ct').forEach(x=>x.classList.remove('active'));t.classList.add('active')}));
})();

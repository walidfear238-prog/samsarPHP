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
   r.style.transform=`translate3d(${rp.x-18}px,${rp.y-18}px,0)`;d.style.transform=`translate3d(${dp.x-2.5}px,${dp.y-2.5}px,0)`;
   requestAnimationFrame(loop)})();
  document.querySelectorAll('a,button,input,select,textarea').forEach(el=>{
   el.addEventListener('mouseenter',()=>{r.classList.add('is-hover');d.classList.add('is-hover')});
   el.addEventListener('mouseleave',()=>{r.classList.remove('is-hover');d.classList.remove('is-hover')});
  });
 }
 const nav=document.querySelector('.nav');
 addEventListener('scroll',()=>nav.classList.toggle('is-scrolled',scrollY>10),{passive:true});
 const io=new IntersectionObserver((es,o)=>es.forEach(e=>{if(e.isIntersecting){const d=e.target.dataset.delay||0;e.target.style.transitionDelay=d+'ms';e.target.classList.add('is-in');o.unobserve(e.target)}}),{threshold:.12});
 document.querySelectorAll('.reveal').forEach(el=>io.observe(el));

 const form=document.getElementById('contact-form');
 form.addEventListener('submit',e=>{
  e.preventDefault();
  const b=form.querySelector('button[type="submit"]'),o=b.textContent;
  b.textContent='Sent — we\'ll reply within 24h ✓';b.disabled=true;
  setTimeout(()=>{b.textContent=o;b.disabled=false;form.reset()},2000);
 });
})();

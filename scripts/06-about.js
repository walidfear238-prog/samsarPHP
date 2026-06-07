(function(){
 'use strict';
 const reduced=matchMedia('(prefers-reduced-motion: reduce)').matches;
 const fine=matchMedia('(pointer: fine)').matches;
 // PAGE TRANSITION — handled by samsar-transitions.js
 if(fine&&!reduced){
  const r=document.querySelector('.cursor'),d=document.querySelector('.cursor-dot');
  const t={x:innerWidth/2,y:innerHeight/2},rp={...t},dp={...t};
  addEventListener('mousemove',e=>{t.x=e.clientX;t.y=e.clientY},{passive:true});
  (function loop(){rp.x+=(t.x-rp.x)*.18;rp.y+=(t.y-rp.y)*.18;dp.x+=(t.x-dp.x)*.32;dp.y+=(t.y-dp.y)*.32;
   r.style.transform=`translate3d(${rp.x-18}px,${rp.y-18}px,0)`;d.style.transform=`translate3d(${dp.x-2.5}px,${dp.y-2.5}px,0)`;
   requestAnimationFrame(loop)})();
  document.querySelectorAll('a,button').forEach(el=>{
   el.addEventListener('mouseenter',()=>{r.classList.add('is-hover');d.classList.add('is-hover')});
   el.addEventListener('mouseleave',()=>{r.classList.remove('is-hover');d.classList.remove('is-hover')});
  });
 }
 const nav=document.querySelector('.nav');
 addEventListener('scroll',()=>nav.classList.toggle('is-scrolled',scrollY>10),{passive:true});
 const io=new IntersectionObserver((es,o)=>es.forEach(e=>{if(e.isIntersecting){const d=e.target.dataset.delay||0;e.target.style.transitionDelay=d+'ms';e.target.classList.add('is-in');o.unobserve(e.target)}}),{threshold:.12,rootMargin:'0px 0px -40px 0px'});
 document.querySelectorAll('.reveal').forEach(el=>io.observe(el));
})();

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
   r.style.transform=`translate3d(${rp.x-16}px,${rp.y-16}px,0)`;d.style.transform=`translate3d(${dp.x-2.5}px,${dp.y-2.5}px,0)`;
   requestAnimationFrame(loop)})();
  document.querySelectorAll('a,button,input').forEach(el=>{
   el.addEventListener('mouseenter',()=>{r.classList.add('is-hover');d.classList.add('is-hover')});
   el.addEventListener('mouseleave',()=>{r.classList.remove('is-hover');d.classList.remove('is-hover')});
  });
 }
 // simple SVG chart
 const svg=document.getElementById('chart-svg');
 const w=600,h=200,pad=20;
 const series=[
  {color:'#C72C41',data:[18,22,30,26,38,42,35,48,52,46,58,62,55,68,72,80,76,84,90,98,92,104,118,124,130,138,142,156]},
  {color:'#1A1A1A',data:[10,14,18,16,22,24,20,28,32,26,34,38,32,40,44,48,44,52,56,60,56,64,72,76,80,86,90,96]},
  {color:'#D9D9D9',data:[4,6,8,7,10,11,9,12,14,11,15,16,14,17,19,21,19,23,24,26,24,28,32,34,36,38,40,43]},
 ];
 const max=Math.max(...series.flatMap(s=>s.data));
 const stepX=(w-pad*2)/(series[0].data.length-1);
 // grid lines
 let svgStr='';
 for(let i=0;i<5;i++){const y=pad+(h-pad*2)*i/4;svgStr+=`<line x1="${pad}" x2="${w-pad}" y1="${y}" y2="${y}" stroke="#F5F5F5" stroke-width="1"/>`}
 // areas + lines
 series.forEach(s=>{
  const pts=s.data.map((v,i)=>[pad+i*stepX,h-pad-((h-pad*2)*v/max)]);
  const d='M '+pts.map(p=>p.join(',')).join(' L ');
  const area=d+` L ${pad+(s.data.length-1)*stepX},${h-pad} L ${pad},${h-pad} Z`;
  svgStr+=`<path d="${area}" fill="${s.color}" opacity="0.06"/>`;
  svgStr+=`<path d="${d}" fill="none" stroke="${s.color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="${w*3}" stroke-dashoffset="${w*3}"><animate attributeName="stroke-dashoffset" to="0" dur="1.4s" fill="freeze"/></path>`;
 });
 svg.innerHTML=svgStr;

 const io=new IntersectionObserver((es,o)=>es.forEach(e=>{if(e.isIntersecting){const d=e.target.dataset.delay||0;e.target.style.transitionDelay=d+'ms';e.target.classList.add('is-in');o.unobserve(e.target)}}),{threshold:.1});
 document.querySelectorAll('.reveal').forEach(el=>io.observe(el));
 document.querySelectorAll('.chip').forEach(c=>c.addEventListener('click',()=>{document.querySelectorAll('.chip').forEach(x=>x.classList.remove('active'));c.classList.add('active')}));
})();

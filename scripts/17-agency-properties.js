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
  document.querySelectorAll('a,button,input,select').forEach(el=>{
   el.addEventListener('mouseenter',()=>{r.classList.add('is-hover');d.classList.add('is-hover')});
   el.addEventListener('mouseleave',()=>{r.classList.remove('is-hover');d.classList.remove('is-hover')});
  });
 }
 const props=[
  {id:1,t:'Villa Tazri',l:'Palmeraie · Marrakech',img:'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=200&q=80',s:'published',p:'12,400,000 MAD',v:284,lds:14,d:'Oct 28'},
  {id:2,t:'Riad Yasmine',l:'Medina · Marrakech',img:'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=200&q=80',s:'published',p:'5,200,000 MAD',v:211,lds:9,d:'Oct 12'},
  {id:3,t:'Villa Ourika',l:'Ourika Valley',img:'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=200&q=80',s:'published',p:'8,900,000 MAD',v:168,lds:6,d:'Sep 30'},
  {id:4,t:'Riad Bahia',l:'Medina · Marrakech',img:'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=200&q=80',s:'published',p:'4,100,000 MAD',v:142,lds:5,d:'Sep 18'},
  {id:5,t:'Villa Tanger Sun',l:'Tangier Hills',img:'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=200&q=80',s:'published',p:'9,800,000 MAD',v:88,lds:2,d:'Aug 22'},
  {id:6,t:'Riad El Fenn',l:'Medina · Marrakech',img:'https://images.unsplash.com/photo-1542718610-a1d656d1884c?auto=format&fit=crop&w=200&q=80',s:'published',p:'6,500,000 MAD',v:64,lds:3,d:'Jul 14'},
  {id:7,t:'Villa Atlas Lodge',l:'Amizmiz',img:'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=200&q=80',s:'draft',p:'—',v:0,lds:0,d:'Draft'},
  {id:8,t:'Villa Nour (Sold)',l:'Palmeraie · Marrakech',img:'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=200&q=80',s:'sold',p:'15,200,000 MAD',v:412,lds:0,d:'Closed Nov 12'},
 ];
 const body=document.getElementById('prop-body');
 function render(filter){
  const arr=filter==='all'?props:props.filter(p=>p.s===filter);
  body.innerHTML=arr.map(p=>`
   <tr data-id="${p.id}">
    <td><input type="checkbox"/></td>
    <td><div class="prop-cell"><img src="${p.img}" alt=""/><div><strong>${p.t}</strong><span>${p.l}</span></div></div></td>
    <td><span class="status ${p.s}">${p.s}</span></td>
    <td><b style="font-family:var(--font-d);font-weight:500">${p.p}</b></td>
    <td>${p.v.toLocaleString()}</td>
    <td>${p.lds}</td>
    <td><span style="color:var(--graphite);font-size:13px">${p.d}</span></td>
    <td><div class="row-actions"><a class="r-btn r-btn-text" href="03-property-details.php" title="View">View</a><a class="r-btn r-btn-text edit" href="19-agency-edit-property.php" title="Edit">Edit</a><button class="r-btn danger" data-del="${p.id}" title="Delete">✕</button></div></td>
   </tr>`).join('');
  body.querySelectorAll('[data-del]').forEach(b=>b.addEventListener('click',e=>{
   e.stopPropagation();pendingDel=+b.dataset.del;document.getElementById('confirm').classList.add('is-open');
  }));
 }
 let pendingDel=null;
 render('all');
 document.querySelectorAll('.chip').forEach(c=>c.addEventListener('click',()=>{document.querySelectorAll('.chip').forEach(x=>x.classList.remove('active'));c.classList.add('active');render(c.dataset.s)}));
 document.getElementById('ck-all').addEventListener('change',e=>{body.querySelectorAll('input[type="checkbox"]').forEach(c=>c.checked=e.target.checked)});
 const cf=document.getElementById('confirm');
 cf.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click',()=>cf.classList.remove('is-open')));
 document.getElementById('cf-confirm').addEventListener('click',()=>{
  if(pendingDel){const i=props.findIndex(p=>p.id===pendingDel);if(i>-1)props.splice(i,1);render(document.querySelector('.chip.active').dataset.s)}
  cf.classList.remove('is-open');
 });
 addEventListener('keydown',e=>{if(e.key==='Escape')cf.classList.remove('is-open')});
})();

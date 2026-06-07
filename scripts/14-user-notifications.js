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
  document.querySelectorAll('a,button').forEach(el=>{
   el.addEventListener('mouseenter',()=>{r.classList.add('is-hover');d.classList.add('is-hover')});
   el.addEventListener('mouseleave',()=>{r.classList.remove('is-hover');d.classList.remove('is-hover')});
  });
 }
 const notifs=[
  {t:'messages',icon:'✉',crimson:true,unread:true,title:'Atlas Real Estate',body:' replied to your message about Villa Tazri.',sub:'"We can arrange a viewing this Saturday at 14:00. Confirm?"',time:'2h ago'},
  {t:'updates',icon:'↓',crimson:true,unread:true,title:'Price reduced ',body:'on Villa Atlas — now 18,500,000 MAD (was 19,800,000).',time:'5h ago'},
  {t:'updates',icon:'✦',crimson:true,unread:true,title:'New match ',body:'in your "Marrakech villa" alert: Riad Yasmine — 5.2M MAD.',time:'1d ago'},
  {t:'follows',icon:'+',unread:true,title:'Anfa Properties ',body:'started following your activity.',time:'1d ago'},
  {t:'messages',icon:'✉',unread:true,title:'Médina Heritage',body:' sent you a quote for Riad Bahia.',time:'2d ago'},
  {t:'updates',icon:'⌖',title:'Viewing reminder: ',body:'Penthouse Lumière, tomorrow at 11:00.',sub:'12 Rue de Verdun, Casablanca',time:'2d ago'},
  {t:'updates',icon:'★',title:'Villa Tazri ',body:'has 3 new viewing requests this week — moving fast.',time:'3d ago'},
  {t:'follows',icon:'+',title:'You are now following ',body:'Atlas Real Estate.',time:'5d ago'},
  {t:'updates',icon:'✓',title:'Your search ',body:'"Essaouira coast under 25M MAD" has been saved.',time:'1w ago'},
 ];
 const list=document.getElementById('notif-list');
 function render(filter){
  const arr=filter==='all'?notifs:filter==='unread'?notifs.filter(n=>n.unread):notifs.filter(n=>n.t===filter);
  list.innerHTML=arr.map((n,i)=>`
   <li class="notif ${n.unread?'unread':''}" data-type="${n.t}" style="animation-delay:${i*40}ms">
    <span class="n-icon ${n.crimson?'crimson':''}">${n.icon}</span>
    <div class="n-body"><strong>${n.title}</strong><p>${n.body}</p>${n.sub?`<span class="sub">${n.sub}</span>`:''}</div>
    <span class="n-time">${n.time}</span>
   </li>`).join('');
  document.getElementById('ct-all').textContent=notifs.length;
  document.getElementById('ct-unread').textContent=notifs.filter(n=>n.unread).length;
  list.querySelectorAll('.notif').forEach(li=>li.addEventListener('click',()=>{
   li.classList.remove('unread');document.getElementById('ct-unread').textContent=document.querySelectorAll('.notif.unread').length;
  }));
 }
 render('all');
 document.querySelectorAll('.ft').forEach(t=>t.addEventListener('click',()=>{document.querySelectorAll('.ft').forEach(x=>x.classList.remove('active'));t.classList.add('active');render(t.dataset.f)}));
 document.getElementById('mark-all').addEventListener('click',()=>{notifs.forEach(n=>n.unread=false);render(document.querySelector('.ft.active').dataset.f)});
})();

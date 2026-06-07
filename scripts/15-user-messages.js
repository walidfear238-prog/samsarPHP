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
 const convos=[
  {id:1,n:'Atlas Real Estate',sub:'Karim · Online now',avatar:'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80',last:'We can arrange a viewing this Saturday at 14:00.',ts:'2h',unread:2,
   msgs:[{day:'Yesterday'},{me:false,t:'Hello Yassine, thank you for your interest in Villa Tazri.',ts:'11:42'},{me:true,t:'Hi Karim — I\'d like to schedule a viewing. I\'m in Marrakech next week.',ts:'11:50'},{me:false,t:'Excellent. Tuesday or Saturday work best for us.',ts:'12:05'},{day:'Today'},{me:true,t:'Saturday afternoon is perfect.',ts:'09:30'},{me:false,t:'Great. We can arrange a viewing this Saturday at 14:00.',ts:'10:48'},{me:false,t:'Bring your ID and the agency will provide bottled water 🙂',ts:'10:48'}]},
  {id:2,n:'Anfa Properties',sub:'Sofia · Replied 1d ago',avatar:'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80',last:'The penthouse is still available — would you like floor plans?',ts:'1d',unread:1,msgs:[{me:false,t:'Hi Yassine, the penthouse is still available.',ts:'Mon 14:20'},{me:false,t:'Would you like floor plans?',ts:'Mon 14:21'}]},
  {id:3,n:'Médina Heritage',sub:'Hicham · Replied 2d ago',avatar:'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=200&q=80',last:'Quote attached — 4.1M MAD with 6 months payment plan.',ts:'2d',unread:0,msgs:[{me:true,t:'Could you send me a quote for Riad Bahia?',ts:'Sat 09:15'},{me:false,t:'Quote attached — 4.1M MAD with 6 months payment plan.',ts:'Sat 18:40'}]},
  {id:4,n:'Tangier Hills Realty',sub:'Replied 5d ago',avatar:'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=200&q=80',last:'Thanks for visiting our agency last week.',ts:'5d',unread:0,msgs:[{me:false,t:'Thanks for visiting our agency last week. Let us know if you have follow-up questions.',ts:'Nov 22'}]},
 ];
 const list=document.getElementById('convos');
 const body=document.getElementById('chat-body');
 let active=convos[0];
 function renderList(){
  list.innerHTML=convos.map(c=>`
   <li class="convo ${c.id===active.id?'active':''}" data-id="${c.id}">
    <img src="${c.avatar}" alt=""/>
    <div class="convo-info"><strong>${c.n}</strong><p>${c.last}</p></div>
    <div class="convo-meta"><span>${c.ts}</span>${c.unread?`<span class="badge">${c.unread}</span>`:''}</div>
   </li>`).join('');
  list.querySelectorAll('.convo').forEach(li=>li.addEventListener('click',()=>{
   active=convos.find(c=>c.id===+li.dataset.id);active.unread=0;renderList();renderChat();
  }));
 }
 function renderChat(){
  document.getElementById('chat-avatar').src=active.avatar;
  document.getElementById('chat-name').textContent=active.n;
  document.getElementById('chat-sub').textContent=active.sub;
  body.innerHTML=active.msgs.map(m=>m.day?`<span class="day-divider">${m.day}</span>`:`<div class="bubble ${m.me?'me':'them'}">${m.t}<span class="ts">${m.ts}</span></div>`).join('');
  body.scrollTop=body.scrollHeight;
 }
 renderList();renderChat();
 document.getElementById('chat-form').addEventListener('submit',e=>{
  e.preventDefault();const inp=document.getElementById('msg-input');const v=inp.value.trim();if(!v)return;
  const now=new Date(),ts=now.getHours().toString().padStart(2,'0')+':'+now.getMinutes().toString().padStart(2,'0');
  active.msgs.push({me:true,t:v,ts});active.last=v;active.ts='now';inp.value='';renderList();renderChat();
  setTimeout(()=>{active.msgs.push({me:false,t:'Thanks for your message — I\'ll get back to you shortly.',ts});renderList();renderChat()},1400);
 });
})();

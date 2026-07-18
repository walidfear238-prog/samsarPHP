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
 const statusEl=document.getElementById('form-status');
 const fieldIdByError={name:'name',email:'email',phone:'phone',topic:'topic',message:'msg'};

 function clearFieldErrors(){
  Object.values(fieldIdByError).forEach(id=>{
   const el=document.getElementById(id);
   if(el)el.closest('.field')?.classList.remove('has-error');
  });
 }

 function showStatus(text,isError){
  statusEl.textContent=text;
  statusEl.classList.remove('is-error','is-success');
  statusEl.classList.add(isError?'is-error':'is-success','is-visible');
 }

 form.addEventListener('submit',async e=>{
  e.preventDefault();

  if(!form.checkValidity()){form.reportValidity();return;}

  clearFieldErrors();
  statusEl.classList.remove('is-visible');

  const b=form.querySelector('button[type="submit"]'),originalHTML=b.innerHTML;
  b.disabled=true;
  b.innerHTML='<span>'+(window.t?window.t('contact.js.sending','Sending…'):'Sending…')+'</span>';

  const payload={
   name:document.getElementById('name').value.trim(),
   email:document.getElementById('email').value.trim(),
   phone:document.getElementById('phone').value.trim(),
   topic:document.getElementById('topic').value.trim(),
   message:document.getElementById('msg').value.trim(),
   website:document.getElementById('website').value // honeypot, should stay empty
  };

  try{
   const res=await fetch('api/send-contact.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify(payload)
   });
   const data=await res.json().catch(()=>({}));

   if(res.ok&&data.success){
    showStatus(window.t?window.t('contact.js.sent','Sent — we\'ll reply within 24h ✓'):'Sent — we\'ll reply within 24h ✓',false);
    form.reset();
    setTimeout(()=>statusEl.classList.remove('is-visible'),5000);
   }else{
    if(data.errors){
     Object.keys(data.errors).forEach(key=>{
      const id=fieldIdByError[key];
      const el=id&&document.getElementById(id);
      if(el)el.closest('.field')?.classList.add('has-error');
     });
    }
    showStatus(data.message||'Something went wrong. Please try again.',true);
   }
  }catch(err){
   showStatus('Network error — please check your connection and try again.',true);
  }finally{
   b.disabled=false;
   b.innerHTML=originalHTML;
  }
 });
})();

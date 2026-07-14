(function(){
 'use strict';
 const reduced=matchMedia('(prefers-reduced-motion: reduce)').matches;
 const fine=matchMedia('(pointer: fine)').matches;

 if(fine&&!reduced){
  const r=document.querySelector('.cursor'),d=document.querySelector('.cursor-dot');
  const t={x:innerWidth/2,y:innerHeight/2},rp={...t},dp={...t};
  addEventListener('mousemove',e=>{t.x=e.clientX;t.y=e.clientY},{passive:true});
  (function loop(){rp.x+=(t.x-rp.x)*.18;rp.y+=(t.y-rp.y)*.18;dp.x+=(t.x-dp.x)*.32;dp.y+=(t.y-dp.y)*.32;
   r.style.transform=`translate3d(${rp.x-18}px,${rp.y-18}px,0)`;d.style.transform=`translate3d(${dp.x-2.5}px,${dp.y-2.5}px,0)`;
   requestAnimationFrame(loop)})();
  document.querySelectorAll('a,button,input').forEach(el=>{
   el.addEventListener('mouseenter',()=>{r.classList.add('is-hover');d.classList.add('is-hover')});
   el.addEventListener('mouseleave',()=>{r.classList.remove('is-hover');d.classList.remove('is-hover')});
  });
 }

 // Read email from query params
 const params=new URLSearchParams(location.search);
 const email=params.get('email');
 if(email)document.getElementById('email-display').textContent=email;

 // Code input auto-advance
 const inputs=document.querySelectorAll('.code-input input');
 inputs.forEach((inp,i)=>{
  inp.addEventListener('input',e=>{
   const v=e.target.value;
   if(v.length===1){
    inp.classList.add('filled');
    if(i<inputs.length-1)inputs[i+1].focus();
   }
  });
  inp.addEventListener('keydown',e=>{
   if(e.key==='Backspace'&&!inp.value&&i>0){
    inputs[i-1].focus();inputs[i-1].classList.remove('filled');
   }
  });
  // Paste support
  inp.addEventListener('paste',e=>{
   e.preventDefault();
   const text=(e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
   text.split('').forEach((c,j)=>{if(inputs[j]){inputs[j].value=c;inputs[j].classList.add('filled')}});
   if(inputs[text.length-1])inputs[text.length-1].focus();
  });
 });

 // Verify button — submits the code to the server for real. The form
 // posts to 20-verify-email.php, which checks the code against the DB
 // and marks the account verified before redirecting to the login page.
 const verifyForm=document.getElementById('verify-form');
 const verifyBtn=document.getElementById('verify-btn');
 if(verifyForm&&verifyBtn){
  verifyForm.addEventListener('submit',e=>{
   const code=Array.from(inputs).map(i=>i.value).join('');
   if(code.length<6){
    e.preventDefault();
    inputs[code.length].focus();
    return;
   }
   // Let the form submit normally; just show a busy state while it does.
   verifyBtn.disabled=true;
   verifyBtn.querySelector('span:first-child').textContent=window.t?window.t('verify.js.verifying'):'Verifying…';
  });
 }

 // Resend
 document.getElementById('resend').addEventListener('click',()=>{
  const btn=document.getElementById('resend');
  const orig=btn.innerHTML;
  btn.innerHTML='<strong>'+(window.t?window.t('verify.js.resent'):'Email resent ✓')+'</strong>';
  btn.disabled=true;
  setTimeout(()=>{btn.innerHTML=orig;btn.disabled=false},3000);
 });
})();

/* =====================================================
   SAMSAR · Dashboard Scripts (Unified)
   Shared logic for dashboard.php, my-properties.php,
   add-property.php, edit-property.php, messages.php,
   notifications.php, favorites.php, following.php,
   profile.php
   ===================================================== */

(function(){
  'use strict';

  // ---------- DATA STORE (localStorage) ----------
  const Store = {
    get(key, fallback){
      try { const v = localStorage.getItem('samsar_'+key); return v ? JSON.parse(v) : fallback; }
      catch(e){ return fallback; }
    },
    set(key, val){ localStorage.setItem('samsar_'+key, JSON.stringify(val)); }
  };

  // Seed demo data on first load
  if(!localStorage.getItem('samsar_seeded')){
    Store.set('properties', [
      {id:1, title:'Villa Tazri', price:'12,400,000 MAD', city:'Marrakech', type:'Villa', status:'available', beds:5, baths:6, area:620, img:'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=600&q=80'},
      {id:2, title:'Riad Souira', price:'38,000 MAD/mo', city:'Essaouira', type:'Riad', status:'rented', beds:4, baths:4, area:310, img:'https://images.unsplash.com/photo-1542718610-a1d656d1884c?auto=format&fit=crop&w=600&q=80'},
      {id:3, title:'Penthouse Lumière', price:'7,950,000 MAD', city:'Casablanca', type:'Apartment', status:'available', beds:3, baths:3, area:240, img:'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=600&q=80'},
      {id:4, title:'Riad Yasmine', price:'5,200,000 MAD', city:'Marrakech', type:'Riad', status:'available', beds:6, baths:5, area:380, img:'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=600&q=80'},
    ]);
    Store.set('favorites', [2,4]);
    Store.set('conversations', [
      {id:1, name:'Karim B.', avatar:'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80', unread:1, messages:[
        {me:false,text:'Is the price negotiable on Riad Souira?',ts:'2h'},
        {me:true,text:'Hello Karim — let me check with the owner and revert.',ts:'2h'},
        {me:false,text:'Thank you 🙏',ts:'1h'}
      ]},
      {id:2, name:'Élise M.', avatar:'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=100&q=80', unread:0, messages:[
        {me:false,text:'I would like to book a visit for Saturday.',ts:'1d'}
      ]},
      {id:3, name:'Atlas Real Estate', avatar:'https://images.unsplash.com/photo-1572021335469-31706a17aaef?auto=format&fit=crop&w=100&q=80', unread:0, messages:[
        {me:false,text:'Welcome to the community!',ts:'3d'},
        {me:true,text:'Thank you!',ts:'3d'}
      ]}
    ]);
    Store.set('notifications', [
      {id:1, text:'<strong>Karim B.</strong> sent you a message about Riad Souira', time:'2h', read:false},
      {id:2, text:'<strong>Élise M.</strong> saved your Riad Yasmine to favorites', time:'5h', read:false},
      {id:3, text:'Your property <strong>Villa Tazri</strong> was viewed 24 times today', time:'1d', read:false},
      {id:4, text:'<strong>Atlas Real Estate</strong> listed a new Apartment in Casablanca', time:'2d', read:true},
      {id:5, text:'Welcome to SAMSAR! Complete your profile for 2× more views.', time:'3d', read:true}
    ]);
    Store.set('following', [
      {id:1, name:'Atlas Real Estate', city:'Marrakech', avatar:'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=100&q=80', listings:86},
      {id:2, name:'Élise M.', city:'Casablanca', avatar:'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=100&q=80', listings:2}
    ]);
    Store.set('seeded', true);
  }

  // Expose Store globally
  window.SamsarStore = Store;

  // ---------- DASHBOARD STATS ----------
  const setText = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val; };

  function paintOverview(){
    const props      = Store.get('properties', []);
    const favs       = Store.get('favorites', []);
    const convos     = Store.get('conversations', []);
    const notifs     = Store.get('notifications', []);
    const following  = Store.get('following', []);

    setText('stat-listings',     props.length);
    setText('stat-followers',    following.length * 14); // demo multiplier
    setText('stat-favorites',    favs.length);
    setText('stat-notifications',notifs.filter(n=>!n.read).length);

    // Notifications dropdown list
    const notifList = document.getElementById('notif-list');
    if(notifList){
      notifList.innerHTML = notifs.map(n => `
        <li class="notif-item ${n.read?'':'unread'}">
          <span class="notif-dot ${n.read?'':'red'}"></span>
          <div>
            <p>${n.text}</p>
            <span class="notif-time">${n.time} ago</span>
          </div>
        </li>`).join('');
    }

    // Sidebar badge counts
    // FIX: Only set bdg-msg from localStorage when chat.js is NOT active
    // (i.e. not on messages.php). On messages.php, chat.js manages this badge
    // via real API data and will overwrite us anyway — but skipping it here
    // prevents the brief flash of stale demo counts.
    if (!window.currentUserId || !document.getElementById('chat-list')) {
      setText('bdg-msg', convos.filter(c=>c.unread).length);
    }

    setText('bdg-fav',      favs.length);
    setText('bdg-notif',    notifs.filter(n=>!n.read).length);
    setText('bdg-notif-2',  notifs.filter(n=>!n.read).length);
  }

  paintOverview();

  // ---------- LOGOUT ----------
  // FIX: Was redirecting to '08-login.php', which only goes to the login page
  // without destroying the PHP session. Now correctly points to logout.php,
  // which calls session_destroy() before redirecting.
  document.querySelectorAll('[data-logout]').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      if(window.SamsarTransition) SamsarTransition.leave(() => location.href = 'logout.php');
      else location.href = 'logout.php';
    });
  });

  // ---------- FOLLOW TOGGLE ----------
  document.addEventListener('click', e => {
    const fbtn = e.target.closest('[data-follow]');
    if(!fbtn) return;
    e.preventDefault();
    const isFollowing = fbtn.classList.toggle('following');
    fbtn.textContent = isFollowing ? 'Following' : 'Follow';
    if(isFollowing){
      fbtn.style.background  = '#C72C41';
      fbtn.style.color       = '#fff';
      fbtn.style.borderColor = '#C72C41';
    } else {
      fbtn.style.background  = '';
      fbtn.style.color       = '';
      fbtn.style.borderColor = '';
    }
  });

  // Expose for other scripts
  window.SamsarApp = { paintOverview, Store };
})();
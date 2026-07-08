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

  // Turns a MySQL timestamp into a short relative time label
  function timeAgo(dateStr){
    if(!dateStr) return '';
    const then = new Date(dateStr.replace(' ', 'T'));
    const diff = Math.max(0, Math.floor((Date.now() - then.getTime()) / 1000));
    if(diff < 60) return 'just now';
    if(diff < 3600) return Math.floor(diff/60) + 'm';
    if(diff < 86400) return Math.floor(diff/3600) + 'h';
    return Math.floor(diff/86400) + 'd';
  }

  function paintOverview(){
    const props = Store.get('properties', []);
    const favs = Store.get('favorites', []);
    const following = Store.get('following', []);

    setText('stat-listings', props.length);
    setText('stat-followers', following.length * 14); // demo
    setText('stat-favorites', favs.length);
    setText('bdg-fav', favs.length);

    refreshMessagingData();
  }

  // Pulls live unread counts and the latest notifications from the database
  function refreshMessagingData(){
    fetch('api/get-unread-counts.php')
      .then(r => r.ok ? r.json() : null)
      .then(counts => {
        if(!counts) return;
        setText('stat-notifications', counts.unread_messages);
        setText('bdg-msg', counts.unread_messages);
        setText('bdg-notif', counts.unread_notifications);
        setText('bdg-notif-2', counts.unread_notifications);
      })
      .catch(() => {});

    const notifList = document.getElementById('notif-list');
    if(notifList){
      fetch('api/get-notifications.php?limit=5')
        .then(r => r.ok ? r.json() : [])
        .then(notifs => {
          if(!Array.isArray(notifs)) return;
          notifList.innerHTML = notifs.length ? notifs.map(n => `
            <li class="notif-item ${n.read?'':'unread'}">
              <span class="notif-dot ${n.read?'':'red'}"></span>
              <div>
                <p>${n.title ? '<strong>'+n.title+'</strong> ' : ''}${n.text}</p>
                <span class="notif-time">${timeAgo(n.created_at)} ago</span>
              </div>
            </li>`).join('') : '<li class="notif-empty">No notifications yet.</li>';
        })
        .catch(() => {});
    }
  }

  paintOverview();

  // ---------- LOGOUT ----------
  document.querySelectorAll('[data-logout]').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      if(window.SamsarTransition) SamsarTransition.leave(() => location.href = '08-login.php');
      else location.href = '08-login.php';
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
      fbtn.style.background = '#C72C41';
      fbtn.style.color = '#fff';
      fbtn.style.borderColor = '#C72C41';
    } else {
      fbtn.style.background = '';
      fbtn.style.color = '';
      fbtn.style.borderColor = '';
    }
  });

  // Expose for other scripts
  window.SamsarApp = { paintOverview, refreshMessagingData, Store };
})();
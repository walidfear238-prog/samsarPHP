<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Following</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600;700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/dashboard-shell.css" />
  <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
  <div class="cursor"></div>
  <div class="cursor-dot"></div>

  <div class="dashboard-shell">
    <aside class="dashboard-sidebar">
      <a class="dashboard-brand" href="index.php">
        <svg class="dashboard-brand-mark" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z" />
        </svg>
        <span class="dashboard-brand-word">SAMSAR</span>
      </a>
      <nav class="dashboard-nav">
        <div class="dashboard-group">MAIN</div>
        <a class="dashboard-link" href="dashboard.php"><span class="ico">⌂</span>Overview</a>
        <a class="dashboard-link" href="my-properties.php"><span class="ico">▤</span>My Properties</a>
        <a class="dashboard-link" href="add-property.php"><span class="ico">+</span>Add Property</a>
        <div class="dashboard-group">SOCIAL</div>
        <a class="dashboard-link" href="messages.php"><span class="ico">✉</span>Messages <em
            class="dashboard-badge red" id="bdg-msg">0</em></a>
        <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span>Favorites <em
            class="dashboard-badge grey" id="bdg-fav">0</em></a>
        <a class="dashboard-link active" href="following.php"><span class="ico">👥</span>Following</a>
        <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span>Notifications <em
            class="dashboard-badge red" id="bdg-notif-2">0</em></a>
      </nav>
      <div class="dashboard-side-foot">
        <div class="dashboard-user">
          <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80"
            alt="Avatar" />
          <div><strong>Yassine A.</strong><span>User</span></div>
        </div>
        <a class="dashboard-signout" href="08-login.php" data-logout>Sign out →</a>
      </div>
    </aside>

    <main class="dashboard-main">
      <header class="dashboard-head">
        <div>
          <h1>Following</h1>
          <p>Accounts you follow across SAMSAR.</p>
        </div>
      </header>
      <div id="follow-grid" class="follow-grid"></div>
    </main>
  </div>

  <style>
    .follow-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 18px
    }

    .follow-card {
      background: #fff;
      border: 1px solid #ececec;
      border-radius: 14px;
      padding: 18px;
      display: flex;
      flex-direction: column;
      gap: 14px
    }

    .follow-card-top {
      display: flex;
      gap: 14px;
      align-items: center
    }

    .follow-card-top img {
      width: 54px;
      height: 54px;
      border-radius: 50%;
      object-fit: cover
    }

    .follow-card-top strong {
      display: block;
      font-size: 15px
    }

    .follow-card-top span {
      display: block;
      font-size: 12px;
      color: #888
    }

    .follow-action {
      margin-top: auto
    }

    .btn-sm {
      padding: 8px 16px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
      border: 1px solid #C72C41;
      background: #fff;
      color: #C72C41;
      cursor: pointer;
      transition: all .2s;
      width: 100%
    }

    .btn-sm:hover {
      background: #C72C41;
      color: #fff
    }

    .btn-sm.following {
      background: #C72C41;
      color: #fff
    }

    .follow-empty {
      padding: 60px 20px;
      text-align: center;
      color: #888
    }
  </style>

  <script src="scripts/samsar-transitions.js"></script>
  <script src="scripts/dashboard-shell.js"></script>
  <script src="scripts/dashboard.js"></script>
  <script>
    (function () {
      const Store = window.SamsarStore;
      const grid = document.getElementById('follow-grid');

      function render() {
        const list = Store.get('following', []);
        if (!list.length) {
          grid.innerHTML = '<div class="content-card follow-empty" style="grid-column:1/-1"><h3 style="font-family:Fraunces,serif;margin:0 0 6px">Not following anyone yet</h3><p style="margin:0">Find people to follow on the marketplace.</p></div>';
          return;
        }
        grid.innerHTML = list.map(f => `
      <div class="follow-card">
        <div class="follow-card-top">
          <img src="${f.avatar}" alt="${f.name}"/>
          <div>
            <strong>${f.name}</strong>
            <span>${f.city} · ${f.listings} listings</span>
          </div>
        </div>
        <div class="follow-action">
          <button class="btn-sm following" data-id="${f.id}">Following</button>
        </div>
      </div>
    `).join('');

        grid.querySelectorAll('.btn-sm').forEach(btn => {
          btn.addEventListener('click', () => {
            const isFollowing = btn.classList.toggle('following');
            btn.textContent = isFollowing ? 'Following' : 'Follow';
            if (isFollowing) {
              btn.style.background = '#C72C41';
              btn.style.color = '#fff';
            } else {
              btn.style.background = '';
              btn.style.color = '';
            }
          });
        });
      }

      render();
    })();
  </script>
</body>

</html>
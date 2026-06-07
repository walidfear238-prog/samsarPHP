<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Favorites</title>
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
        <a class="dashboard-link active" href="favorites.php"><span class="ico">♡</span>Favorites <em
            class="dashboard-badge grey" id="bdg-fav">0</em></a>
        <a class="dashboard-link" href="following.php"><span class="ico">👥</span>Following</a>
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
          <h1>Favorites</h1>
          <p>Your saved properties.</p>
        </div>
      </header>
      <div id="fav-grid" class="fav-grid"></div>
    </main>
  </div>

  <style>
    .fav-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 18px
    }

    .fav-card {
      background: #fff;
      border: 1px solid #ececec;
      border-radius: 14px;
      overflow: hidden;
      transition: all .3s
    }

    .fav-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 14px 30px -16px rgba(0, 0, 0, .15)
    }

    .fav-card img {
      width: 100%;
      height: 180px;
      object-fit: cover
    }

    .fav-body {
      padding: 16px
    }

    .fav-body strong {
      display: block;
      font-family: Fraunces, serif;
      font-size: 18px;
      margin-bottom: 4px
    }

    .fav-body span {
      display: block;
      font-size: 11px;
      letter-spacing: .08em;
      color: #888;
      text-transform: uppercase;
      margin-bottom: 10px
    }

    .fav-price {
      color: #C72C41;
      font-weight: 600;
      margin-bottom: 14px
    }

    .fav-actions {
      display: flex;
      gap: 8px;
      justify-content: space-between
    }

    .fav-btn {
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 500;
      border: 1px solid #e5e5e5;
      background: #fff;
      cursor: pointer;
      transition: all .2s
    }

    .fav-btn:hover {
      border-color: #1A1A1A
    }

    .fav-btn.unfav {
      color: #C72C41;
      border-color: rgba(199, 44, 65, .3)
    }

    .fav-btn.unfav:hover {
      background: #C72C41;
      color: #fff;
      border-color: #C72C41
    }

    .fav-empty {
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
      const grid = document.getElementById('fav-grid');

      function render() {
        const favs = Store.get('favorites', []);
        const props = Store.get('properties', []);
        const favProps = props.filter(p => favs.includes(p.id));

        if (!favProps.length) {
          grid.innerHTML = '<div class="content-card fav-empty" style="grid-column:1/-1"><h3 style="font-family:Fraunces,serif;margin:0 0 6px">No favorites yet</h3><p style="margin:0">Browse properties and tap the heart to save them here.</p></div>';
          return;
        }

        grid.innerHTML = favProps.map(p => `
      <div class="fav-card">
        <img src="${p.img}" alt="${p.title}"/>
        <div class="fav-body">
          <strong>${p.title}</strong>
          <span>${p.city} · ${p.type}</span>
          <div class="fav-price">${p.price}</div>
          <div class="fav-actions">
            <a class="fav-btn" href="03-property-details.php">View</a>
            <button class="fav-btn unfav" data-unfav="${p.id}">Remove</button>
          </div>
        </div>
      </div>
    `).join('');

        grid.querySelectorAll('[data-unfav]').forEach(btn => {
          btn.addEventListener('click', () => {
            const id = parseInt(btn.dataset.unfav);
            const favs = Store.get('favorites', []).filter(x => x !== id);
            Store.set('favorites', favs);
            render();
            if (window.SamsarApp) SamsarApp.paintOverview();
          });
        });
      }

      render();
    })();
  </script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · 21 Following</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/21-following.css" />
  <link rel="stylesheet" href="styles/samsar-transitions.css" />
  <style>
    .follow-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }

    .follow-card {
      background: #fff;
      padding: 24px;
      border-radius: 16px;
      border: 1px solid var(--mist);
      display: flex;
      align-items: center;
      gap: 16px;
      position: relative;
    }

    .follow-card img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
    }

    .fc-info h3 {
      font-family: "Fraunces", serif;
      font-size: 18px;
    }

    .fc-info p {
      font-size: 12px;
      color: var(--graphite);
    }

    .unfollow {
      margin-left: auto;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .1em;
      color: var(--crimson);
    }
  </style>
</head>

<body>
  <div class="cursor"></div>
  <div class="cursor-dot"></div>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="side-top"><a href="index.php" class="brand"><svg class="brand-mark" viewBox="0 0 100 100">
            <path
              d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z M38 38 L62 38 L62 50 L38 50 Z M38 60 L62 60 L62 72 L38 72 Z"
              fill-rule="evenodd" />
          </svg><span class="brand-word">SAMSAR</span></a></div>
      <nav class="side-nav">
        <div class="nav-group">Main</div>
        <a href="11-dashboard.php"><span class="ico">⌂</span> Overview</a>
        <a href="17-my-properties.php"><span class="ico">▤</span> My Properties</a>
        <a href="18-add-property.php"><span class="ico">+</span> Add Property</a>
        <div class="nav-group">Social</div>
        <a href="15-messages.php"><span class="ico">✉</span> Messages</a>
        <a href="13-favorites.php"><span class="ico">♡</span> Favorites</a>
        <a href="21-following.php" class="active"><span class="ico">👥</span> Following</a>
        <a href="14-notifications.php"><span class="ico">⌖</span> Notifications</a>
        <div class="nav-group">System</div>
        <a href="12-profile.php"><span class="ico">○</span> My Profile</a>
        <a href="22-settings.php"><span class="ico">⚙</span> Settings</a>
      </nav>
    </aside>
    <main class="main-content">
      <header class="dash-header">
        <div class="h-titles">
          <h1>Following</h1>
          <p>Vetted agencies and sellers you are following.</p>
        </div>
      </header>
      <div class="follow-grid">
        <div class="follow-card">
          <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=100&q=80" alt="" />
          <div class="fc-info">
            <h3>Anfa Properties</h3>
            <p>Casablanca · 124 listings</p>
          </div>
          <button class="unfollow">Unfollow</button>
        </div>
        <div class="follow-card">
          <img src="https://images.unsplash.com/photo-1572021335469-31706a17aaef?auto=format&fit=crop&w=100&q=80"
            alt="" />
          <div class="fc-info">
            <h3>Atlas Real Estate</h3>
            <p>Marrakech · 86 listings</p>
          </div>
          <button class="unfollow">Unfollow</button>
        </div>
      </div>
    </main>
  </div>
  <script src="scripts/samsar-transitions.js"></script>
  <script>
    (function () {
      'use strict';
      requestAnimationFrame(() => document.body.classList.add('is-entering'));
      document.querySelectorAll('.unfollow').forEach(btn => {
        btn.addEventListener('click', () => { btn.closest('.follow-card').remove(); });
      });
    })();
  </script>
</body>

</html>
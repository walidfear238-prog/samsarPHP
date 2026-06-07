<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Favorites</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/13-user-favorites.css" />
</head>

<body>
  <div class="page-trans"><span></span><span></span><span></span></div>
  <div class="cursor"></div>
  <div class="cursor-dot"></div>
  <div class="app">
    <aside class="side">
      <a href="index.php" class="brand"><svg class="brand-mark" viewBox="0 0 100 100">
          <path
            d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z M38 38 L62 38 L62 50 L38 50 Z M38 60 L62 60 L62 72 L38 72 Z"
            fill-rule="evenodd" />
        </svg><span class="brand-word">SAMSAR</span></a>
      <nav class="side-nav">
        <span class="nav-group">Account</span>
        <a href="11-user-dashboard.php"><span class="ico">⌂</span> Overview</a>
        <a href="13-user-favorites.php" class="active"><span class="ico">♡</span> Favorites <em>12</em></a>
        <a href="15-user-messages.php"><span class="ico">✉</span> Messages <em class="dot">3</em></a>
        <a href="14-user-notifications.php"><span class="ico">⌖</span> Notifications <em class="dot">5</em></a>
        <a href="12-user-profile.php"><span class="ico">○</span> Profile</a>
        <span class="nav-group">Explore</span>
        <a href="02-properties.php"><span class="ico">⌕</span> Browse properties</a>
        <a href="04-agencies.php"><span class="ico">▤</span> Agencies</a>
      </nav>
      <div class="side-foot">
        <div class="user-mini"><img
            src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80" alt="" />
          <div><strong>Yassine A.</strong><span>User</span></div>
        </div>
        <a href="08-login.php" class="sign-out">Sign out →</a>
      </div>
    </aside>

    <main class="main">
      <header class="topbar">
        <div>
          <h1>Saved properties <em id="count">12</em></h1>
          <p>Organised by collection. Drag to reorder. Removed items can be restored within 30 days.</p>
        </div>
        <div class="head-actions">
          <button class="btn btn-ghost">+ New collection</button>
          <button class="btn btn-primary">Compare selected <span class="arrow">→</span></button>
        </div>
      </header>

      <div class="coll-tabs">
        <button class="ct active" data-coll="all">All <em>12</em></button>
        <button class="ct" data-coll="mar">Marrakech shortlist <em>5</em></button>
        <button class="ct" data-coll="rab">Rabat finals <em>3</em></button>
        <button class="ct" data-coll="dream">Dream pile <em>4</em></button>
      </div>

      <div class="fav-grid" id="fav-grid"></div>

      <section class="empty" hidden id="empty">
        <span class="heart">♡</span>
        <h2>Your collection is empty.</h2>
        <p>Tap the heart on any property to save it here.</p>
        <a href="02-properties.php" class="btn btn-primary">Browse properties <span class="arrow">→</span></a>
      </section>
    </main>
  </div>
  <script src="scripts/13-user-favorites.js"></script>
</body>

</html>
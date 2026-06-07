<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/11-user-dashboard.css" />
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
        <div class="nav-group">Main</div>
        <a href="11-user-dashboard.php" class="active"><span class="ico">⌂</span> Overview</a>
        <a href="23-my-properties.php"><span class="ico">▤</span> My Properties</a>
        <a href="18-agency-add-property.php"><span class="ico">+</span> Add Property</a>
        <div class="nav-group">Social</div>
        <a href="15-user-messages.php"><span class="ico">✉</span> Messages <em class="dot">3</em></a>
        <a href="13-user-favorites.php"><span class="ico">♡</span> Favorites <em>12</em></a>
        <a href="21-following.php"><span class="ico">👥</span> Following</a>
        <a href="14-user-notifications.php"><span class="ico">⌖</span> Notifications <em class="dot">5</em></a>
        <div class="nav-group">System</div>
        <a href="12-user-profile.php"><span class="ico">○</span> My Profile</a>
        <a href="22-settings.php"><span class="ico">⚙</span> Settings</a>
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
          <h1>Welcome back, <em>Yassine</em>.</h1>
          <p>Here's what's happened since your last visit, 2 days ago.</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
          <a href="02-properties.php" class="btn btn-ghost">Browse properties</a>
          <a href="18-agency-add-property.php" class="btn btn-primary">+ Add Property</a>
        </div>
      </header>

      <section class="stats">
        <div class="stat reveal"><span class="lbl">Saved homes</span><strong>12</strong><a
            href="13-user-favorites.php">View →</a></div>
        <div class="stat reveal" data-delay="80"><span class="lbl">Unread messages</span><strong>3</strong><a
            href="15-user-messages.php">Open →</a></div>
        <div class="stat reveal" data-delay="160"><span class="lbl">Saved searches</span><strong>4</strong><a
            href="#">Manage →</a></div>
        <div class="stat reveal" data-delay="240"><span class="lbl">Properties viewed</span><strong>87</strong><a
            href="#">History →</a></div>
      </section>

      <div class="dash-grid">
        <section class="panel reveal">
          <div class="panel-head">
            <h2>Latest favourites</h2><a href="13-user-favorites.php">See all 12 →</a>
          </div>
          <div class="fav-list">
            <a href="03-property-details.php" class="fav-row"><img
                src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=200&q=80"
                alt="" />
              <div><strong>Villa Tazri</strong><span>Palmeraie · Marrakech</span></div><b>12,400,000 MAD</b>
            </a>
            <a href="03-property-details.php" class="fav-row"><img
                src="https://images.unsplash.com/photo-1542718610-a1d656d1884c?auto=format&fit=crop&w=200&q=80"
                alt="" />
              <div><strong>Riad Souira</strong><span>Medina · Essaouira</span></div><b>38,000 MAD/mo</b>
            </a>
            <a href="03-property-details.php" class="fav-row"><img
                src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=200&q=80"
                alt="" />
              <div><strong>Penthouse Lumière</strong><span>Anfa · Casablanca</span></div><b>7,950,000 MAD</b>
            </a>
          </div>
        </section>

        <section class="panel reveal" data-delay="120">
          <div class="panel-head">
            <h2>Recent activity</h2>
          </div>
          <ul class="activity">
            <li><span class="bullet"></span>
              <div><strong>Atlas Real Estate</strong> replied to your message about Villa Tazri.<span>2h ago</span>
              </div>
            </li>
            <li><span class="bullet"></span>
              <div>New listing matches your Marrakech alert: <strong>Riad Yasmine — 5.2M MAD</strong>.<span>1d
                  ago</span></div>
            </li>
            <li><span class="bullet"></span>
              <div>Price reduced on <strong>Villa Atlas</strong> — now 18.5M MAD (was 19.8M).<span>2d ago</span></div>
            </li>
            <li><span class="bullet"></span>
              <div>You saved <strong>Penthouse Lumière</strong> to favourites.<span>3d ago</span></div>
            </li>
            <li><span class="bullet"></span>
              <div><strong>Anfa Properties</strong> scheduled a viewing for Sat 14:00.<span>4d ago</span></div>
            </li>
          </ul>
        </section>

        <section class="panel wide reveal" data-delay="200">
          <div class="panel-head">
            <h2>Recommended for you</h2><span class="sub">Based on your Marrakech and Essaouira searches</span>
          </div>
          <div class="rec-grid" id="rec-grid"></div>
        </section>
      </div>
    </main>
  </div>
  <script src="scripts/11-user-dashboard.js"></script>
</body>

</html>
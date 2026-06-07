<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Notifications</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/14-user-notifications.css" />
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
        <a href="13-user-favorites.php"><span class="ico">♡</span> Favorites <em>12</em></a>
        <a href="15-user-messages.php"><span class="ico">✉</span> Messages <em class="dot">3</em></a>
        <a href="14-user-notifications.php" class="active"><span class="ico">⌖</span> Notifications <em
            class="dot">5</em></a>
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
          <h1>Notifications</h1>
          <p>Price drops, new matches, agency replies and viewing reminders — all in one place.</p>
        </div>
        <div class="head-actions">
          <button class="btn btn-ghost" id="mark-all">Mark all as read</button>
          <button class="btn btn-ghost">Settings</button>
        </div>
      </header>

      <div class="filter-tabs">
        <button class="ft active" data-f="all">All <em id="ct-all">9</em></button>
        <button class="ft" data-f="unread">Unread <em id="ct-unread">5</em></button>
        <button class="ft" data-f="messages">Messages</button>
        <button class="ft" data-f="updates">Property updates</button>
        <button class="ft" data-f="follows">Follows</button>
      </div>

      <ul class="notif-list" id="notif-list"></ul>
    </main>
  </div>
  <script src="scripts/14-user-notifications.js"></script>
</body>

</html>
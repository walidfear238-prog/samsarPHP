<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · My Properties</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/17-agency-properties.css" />
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
        </svg><span class="brand-word">SAMSAR</span><span class="agency-tag">AGENCY</span></a>
      <nav class="side-nav">
        <div class="nav-group">Main</div>
        <a href="16-agency-dashboard.php"><span class="ico">⌂</span> Overview</a>
        <a href="23-my-properties.php" class="active"><span class="ico">▤</span> My Properties</a>
        <a href="18-agency-add-property.php"><span class="ico">+</span> Add Property</a>
        <div class="nav-group">Social</div>
        <a href="15-user-messages.php"><span class="ico">✉</span> Messages <em class="dot">8</em></a>
        <a href="13-user-favorites.php"><span class="ico">♡</span> Favorites</a>
        <a href="21-following.php"><span class="ico">👥</span> Following</a>
        <a href="14-user-notifications.php"><span class="ico">⌖</span> Notifications</a>
        <div class="nav-group">System</div>
        <a href="12-user-profile.php"><span class="ico">○</span> My Profile</a>
        <a href="22-settings.php"><span class="ico"></span> Settings</a>
      </nav>
      <div class="side-foot">
        <div class="user-mini"><img
            src="https://images.unsplash.com/photo-1572021335469-31706a17aaef?auto=format&fit=crop&w=120&q=80" alt="" />
          <div><strong>Atlas Real Estate</strong><span>Marrakech · Verified</span></div>
        </div>
        <a href="08-login.php" class="sign-out">Sign out →</a>
      </div>
    </aside>

    <main class="main">
      <header class="topbar">
        <div>
          <h1>My Properties</h1>
          <p>Manage all your listings. Edit or remove any property.</p>
        </div>
        <a href="18-agency-add-property.php" class="btn btn-primary add-property-btn">+ Add Property</a>
      </header>

      <div class="tools">
        <div class="search-wrap"><input type="search" placeholder="Search by title, city or reference…" /></div>
        <div class="filters">
          <button class="chip active" data-s="all">All <em>8</em></button>
          <button class="chip" data-s="published">Published <em>6</em></button>
          <button class="chip" data-s="draft">Draft <em>1</em></button>
          <button class="chip" data-s="sold">Sold <em>1</em></button>
        </div>
        <select class="sort">
          <option>Newest first</option>
          <option>Most viewed</option>
          <option>Price ↑</option>
          <option>Price ↓</option>
        </select>
      </div>

      <div class="table-wrap">
        <table class="prop-table">
          <thead>
            <tr>
              <th><input type="checkbox" id="ck-all" /></th>
              <th>Property</th>
              <th>Status</th>
              <th>Price</th>
              <th>Views</th>
              <th>Leads</th>
              <th>Listed</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="prop-body"></tbody>
        </table>
      </div>
    </main>
  </div>

  <div class="confirm" id="confirm" aria-hidden="true">
    <div class="cf-backdrop" data-close></div>
    <div class="cf-panel">
      <h3>Delete this listing?</h3>
      <p>This will remove the listing from SAMSAR. You can restore it within 30 days from the Trash folder.</p>
      <div class="cf-actions"><button class="btn btn-ghost" data-close>Cancel</button><button class="btn btn-danger"
          id="cf-confirm">Delete</button></div>
    </div>
  </div>

  <script src="scripts/17-agency-properties.js"></script>
</body>

</html>
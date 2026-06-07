<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · My Properties</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/11-dashboard.css" />
  <link rel="stylesheet" href="styles/17-agency-properties.css" />
  <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
  <div class="page-trans"><span></span><span></span><span></span></div>
  <div class="cursor"></div>
  <div class="cursor-dot"></div>

  <div class="app-shell">
    <aside class="sidebar">
      <div class="side-top">
        <a href="index.php" class="brand">
          <svg class="brand-mark" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
            <path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z" />
          </svg>
          <span class="brand-word">SAMSAR</span>
        </a>
      </div>

      <nav class="side-nav">
        <div class="nav-group">MAIN</div>
        <a href="11-dashboard.php"><span class="ico"></span> Overview</a>
        <a href="23-my-properties.php" class="active"><span class="ico">▤</span> My Properties</a>
        <a href="18-agency-add-property.php"><span class="ico">+</span> Add Property</a>

        <div class="nav-group">SOCIAL</div>
        <a href="15-user-messages.php"><span class="ico">✉</span> Messages <em class="badge red">3</em></a>
        <a href="13-user-favorites.php"><span class="ico">♡</span> Favorites <em class="badge grey">12</em></a>
        <a href="21-following.php"><span class="ico"></span> Following</a>
        <a href="14-user-notifications.php"><span class="ico"></span> Notifications <em class="badge red">5</em></a>
      </nav>

      <div class="side-foot">
        <div class="user-pill">
          <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80"
            alt="Avatar" />
          <div class="u-info">
            <strong>Yassine A.</strong>
            <span>User</span>
          </div>
        </div>
        <a href="08-login.php" class="logout-btn">Sign out →</a>
      </div>
    </aside>

    <main class="main-content">
      <header class="dash-header">
        <div class="h-titles">
          <h1>My Properties</h1>
          <p>Manage your active and draft listings. Use the Edit button on any row to update a listing.</p>
        </div>
        <a href="add-property.php" class="btn btn-primary">+ Add Property</a>
      </header>

      <div class="tools">
        <div class="search-wrap"><input type="search" placeholder="Search by title, city or reference…" /></div>
        <div class="filters">
          <button class="chip active">All <em>8</em></button>
          <button class="chip">Published <em>6</em></button>
          <button class="chip">Draft <em>1</em></button>
          <button class="chip">Sold <em>1</em></button>
        </div>
        <button class="sort-btn">Newest first</button>
      </div>

      <div class="table-wrap">
        <table class="prop-table">
          <thead>
            <tr>
              <th><input type="checkbox" /></th>
              <th>Property</th>
              <th>Status</th>
              <th>Price</th>
              <th>Views</th>
              <th>Leads</th>
              <th>Listed</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="prop-body">
            <!-- Rows will be injected here -->
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal-overlay" id="delete-modal">
    <div class="modal-box">
      <h3>Delete Property?</h3>
      <p>Are you sure? This action cannot be undone.</p>
      <div class="modal-actions">
        <button class="btn btn-ghost" id="cancel-delete">Cancel</button>
        <button class="btn btn-danger" id="confirm-delete">Delete</button>
      </div>
    </div>
  </div>

  <script src="scripts/samsar-transitions.js"></script>
  <script src="scripts/23-my-properties.js"></script>
  <script>
    // Add Cursor support
    (function () {
      const fine = matchMedia('(pointer: fine)').matches;
      const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (fine && !reduced) {
        const r = document.querySelector('.cursor'), d = document.querySelector('.cursor-dot');
        const t = { x: innerWidth / 2, y: innerHeight / 2 }, rp = { ...t }, dp = { ...t };
        addEventListener('mousemove', e => { t.x = e.clientX; t.y = e.clientY; }, { passive: true });
        (function loop() {
          rp.x += (t.x - rp.x) * .18; rp.y += (t.y - rp.y) * .18;
          dp.x += (t.x - dp.x) * .32; dp.y += (t.y - dp.y) * .32;
          r.style.transform = `translate3d(${rp.x - 18}px, ${rp.y - 18}px, 0)`;
          d.style.transform = `translate3d(${dp.x - 2.5}px, ${dp.y - 2.5}px, 0)`;
          requestAnimationFrame(loop);
        })();
        document.querySelectorAll('a, button, input').forEach(el => {
          el.addEventListener('mouseenter', () => { r.classList.add('is-hover'); d.classList.add('is-hover'); });
          el.addEventListener('mouseleave', () => { r.classList.remove('is-hover'); d.classList.remove('is-hover'); });
        });
      }
    })();
  </script>
</body>

</html>
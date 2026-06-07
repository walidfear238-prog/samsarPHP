<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Dashboard</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/11-dashboard.css" />
  <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
  <div class="cursor"></div>
  <div class="cursor-dot"></div>

  <div class="app-shell">
    <!-- Sidebar -->
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
        <a href="11-dashboard.php" class="active"><span class="ico"></span> Overview</a>
        <a href="23-my-properties.php"><span class="ico">▤</span> My Properties</a>
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
            <span id="user-type-label">User</span>
          </div>
        </div>
        <a href="08-login.php" class="logout-btn">Sign out →</a>
      </div>
    </aside>

    <main class="main-content">
      <header class="dash-header">
        <div class="h-titles">
          <h1>Overview</h1>
          <p>Manage your listings, conversations, and social activity.</p>
        </div>
        <div class="h-actions">
          <a href="02-properties.php" class="btn btn-ghost">Browse Market</a>
          <a href="18-agency-add-property.php" class="btn btn-primary">+ Add Property</a>
        </div>
      </header>

      <!-- Stat Cards -->
      <div class="stat-grid">
        <div class="stat-card">
          <span class="s-label">Active Listings</span>
          <div class="s-value">12</div>
          <div class="s-trend up">+2 new</div>
        </div>
        <div class="stat-card">
          <span class="s-label">Followers</span>
          <div class="s-value">1.2k</div>
          <div class="s-trend up">+14 this week</div>
        </div>
        <div class="stat-card">
          <span class="s-label">Avg. Response Time</span>
          <div class="s-value">2h</div>
          <div class="s-trend">Top 10% in Casablanca</div>
        </div>
        <div class="stat-card">
          <span class="s-label">Favorites Received</span>
          <div class="s-value">342</div>
          <div class="s-trend up">+48%</div>
        </div>
      </div>

      <!-- Feed / Recent Activity -->
      <div class="dash-layout">
        <section class="feed-section">
          <div class="section-head">
            <h2>Following Feed</h2>
            <p>Latest from agencies and sellers you follow.</p>
          </div>

          <div class="feed-list">
            <article class="feed-item">
              <div class="f-user">
                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=100&q=80"
                  alt="Agency" />
                <div><strong>Anfa Properties</strong><span>Listed a new Apartment in Casablanca · 2h ago</span></div>
              </div>
              <a href="03-property-details.php" class="f-content">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=600&q=80"
                  alt="Property" />
                <div class="f-text">
                  <h3>Penthouse Lumière</h3>
                  <p>Anfa · 7,950,000 MAD</p>
                </div>
              </a>
            </article>
          </div>
        </section>

        <aside class="side-panel">
          <div class="panel-card">
            <h3>Quick Messages</h3>
            <div class="mini-chat-list">
              <div class="mc-item unread">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80"
                  alt="" />
                <div><strong>Karim B.</strong>
                  <p>Is the price negotiable?</p>
                </div>
              </div>
              <div class="mc-item">
                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=100&q=80"
                  alt="" />
                <div><strong>Élise M.</strong>
                  <p>I'd like to book a visit.</p>
                </div>
              </div>
            </div>
            <a href="15-user-messages.php" class="p-link">View all messages →</a>
          </div>
        </aside>
      </div>
    </main>
  </div>

  <script src="scripts/samsar-transitions.js"></script>
  <script src="scripts/11-dashboard.js"></script>
</body>

</html>
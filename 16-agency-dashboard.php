<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Agency Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/16-agency-dashboard.css" />
  <link rel="stylesheet" href="styles/samsar-transitions.css" />
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
        <a href="16-agency-dashboard.php" class="active"><span class="ico"></span> Overview</a>
        <a href="23-my-properties.php"><span class="ico">▤</span> My Properties</a>
        <a href="18-agency-add-property.php"><span class="ico">+</span> Add Property</a>
        <div class="nav-group">Social</div>
        <a href="15-user-messages.php"><span class="ico">✉</span> Messages <em class="dot">8</em></a>
        <a href="13-user-favorites.php"><span class="ico">♡</span> Favorites</a>
        <a href="21-following.php"><span class="ico">👥</span> Following</a>
        <a href="14-user-notifications.php"><span class="ico">⌖</span> Notifications <em class="dot">5</em></a>
        <div class="nav-group">System</div>
        <a href="12-user-profile.php"><span class="ico">○</span> My Profile</a>
        <a href="22-settings.php"><span class="ico">⚙</span> Settings</a>
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
          <h1>Good morning, <em>Karim</em>.</h1>
          <p>You closed 2 properties last week. Here's how things are moving today.</p>
        </div>
        <a href="18-agency-add-property.php" class="btn btn-primary">+ Add Property</a>
      </header>

      <section class="kpis">
        <div class="kpi reveal"><span class="lbl">Active listings</span><strong>124</strong><span class="trend up">+6
            this month</span></div>
        <div class="kpi reveal" data-delay="80"><span class="lbl">New leads (7d)</span><strong>38</strong><span
            class="trend up">+22%</span></div>
        <div class="kpi reveal" data-delay="160"><span class="lbl">Closed (Nov)</span><strong>7</strong><span
            class="trend up">+3 vs. Oct</span></div>
        <div class="kpi reveal" data-delay="240"><span class="lbl">Total MAD volume</span><strong>84.2M</strong><span
            class="trend up">+18%</span></div>
      </section>

      <div class="grid">
        <section class="card reveal">
          <div class="card-head">
            <h2>Lead activity · last 30 days</h2>
            <div class="filter-row"><button class="chip active">30d</button><button class="chip">90d</button><button
                class="chip">YTD</button></div>
          </div>
          <div class="chart"><svg viewBox="0 0 600 200" preserveAspectRatio="none" id="chart-svg"></svg></div>
          <div class="chart-legend">
            <div><span class="dot d1"></span> Views <b>12,840</b></div>
            <div><span class="dot d2"></span> Messages <b>312</b></div>
            <div><span class="dot d3"></span> Viewings <b>87</b></div>
          </div>
        </section>

        <section class="card reveal" data-delay="120">
          <div class="card-head">
            <h2>Top performing</h2><a href="17-agency-properties.php">All →</a>
          </div>
          <ul class="top-list">
            <li><img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=120&q=80"
                alt="" />
              <div><strong>Villa Tazri</strong><span>Palmeraie</span></div><b>284 views</b>
            </li>
            <li><img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=120&q=80"
                alt="" />
              <div><strong>Riad Yasmine</strong><span>Medina</span></div><b>211 views</b>
            </li>
            <li><img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=120&q=80"
                alt="" />
              <div><strong>Villa Ourika</strong><span>Ourika</span></div><b>168 views</b>
            </li>
            <li><img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=120&q=80"
                alt="" />
              <div><strong>Riad Bahia</strong><span>Medina</span></div><b>142 views</b>
            </li>
          </ul>
        </section>

        <section class="card reveal" data-delay="200">
          <div class="card-head">
            <h2>Recent leads</h2><a href="15-user-messages.php">Open inbox →</a>
          </div>
          <ul class="lead-list">
            <li><img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80"
                alt="" />
              <div><strong>Yassine El Amrani</strong><span>Asked about Villa Tazri · 2h ago</span></div><a
                class="lead-cta">Reply →</a>
            </li>
            <li><img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=80&q=80"
                alt="" />
              <div><strong>Élise Marchand</strong><span>Wants viewing for Riad Yasmine · 5h ago</span></div><a
                class="lead-cta">Reply →</a>
            </li>
            <li><img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=80&q=80"
                alt="" />
              <div><strong>Hicham Berrada</strong><span>Quote request for Villa Ourika · 1d ago</span></div><a
                class="lead-cta">Reply →</a>
            </li>
            <li><img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=80&q=80"
                alt="" />
              <div><strong>Carla Romano</strong><span>Long-term rental enquiry · 2d ago</span></div><a
                class="lead-cta">Reply →</a>
            </li>
          </ul>
        </section>

        <section class="card reveal wide" data-delay="280">
          <div class="card-head">
            <h2>Upcoming viewings · this week</h2>
          </div>
          <table class="sched">
            <thead>
              <tr>
                <th>When</th>
                <th>Property</th>
                <th>Client</th>
                <th>Broker</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Sat 14:00</strong><br /><span>Dec 6</span></td>
                <td>Villa Tazri · Palmeraie</td>
                <td>Yassine El Amrani</td>
                <td>Karim</td>
                <td><a class="link">Details →</a></td>
              </tr>
              <tr>
                <td><strong>Sat 17:30</strong><br /><span>Dec 6</span></td>
                <td>Riad Yasmine · Medina</td>
                <td>Élise Marchand</td>
                <td>Soukaina</td>
                <td><a class="link">Details →</a></td>
              </tr>
              <tr>
                <td><strong>Sun 11:00</strong><br /><span>Dec 7</span></td>
                <td>Villa Ourika</td>
                <td>Hicham Berrada</td>
                <td>Karim</td>
                <td><a class="link">Details →</a></td>
              </tr>
              <tr>
                <td><strong>Mon 09:30</strong><br /><span>Dec 8</span></td>
                <td>Riad Bahia · Medina</td>
                <td>James Hill</td>
                <td>Soukaina</td>
                <td><a class="link">Details →</a></td>
              </tr>
            </tbody>
          </table>
        </section>
      </div>
    </main>
  </div>
  <script src="scripts/samsar-transitions.js"></script>
  <script src="scripts/16-agency-dashboard.js"></script>
  <script>
    (function () {
      if (window.SamsarTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        var old = document.querySelector('.page-trans'); if (old) old.remove();
        setTimeout(function () { SamsarTransition.play('organic-morph', 'slow') }, 50);
      }
    })();
  </script>
</body>

</html>
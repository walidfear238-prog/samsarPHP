<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · My Profile</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/12-user-profile.css" />
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
        <a href="14-user-notifications.php"><span class="ico">⌖</span> Notifications <em class="dot">5</em></a>
        <a href="12-user-profile.php" class="active"><span class="ico">○</span> Profile</a>
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
          <h1>My profile</h1>
          <p>Keep your details up to date — agencies see them when you message.</p>
        </div>
      </header>

      <div class="layout">
        <section class="profile-panel reveal">
          <div class="avatar-row">
            <img class="avatar"
              src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=240&q=80"
              alt="Profile photo" />
            <div class="u-meta">
              <h2>Yassine El Amrani</h2>
              <div class="u-badges"><span class="badge">Private Seller</span><span
                  class="badge verified">Verified</span></div>
              <div class="social-stats">
                <span><strong>12</strong> Listings</span>
                <span><strong>840</strong> Followers</span>
                <span><strong>210</strong> Following</span>
              </div>
            </div>
            <div class="av-actions">
              <button class="btn btn-primary">Follow</button>
              <button class="btn btn-ghost">Message</button>
            </div>
          </div>

          <form id="prof-form">
            <div class="section-title">Personal details</div>
            <div class="row">
              <div class="field"><label>First name</label><input type="text" value="Yassine" /></div>
              <div class="field"><label>Last name</label><input type="text" value="El Amrani" /></div>
            </div>
            <div class="row">
              <div class="field"><label>Email</label><input type="email" value="yassine@email.com" /></div>
              <div class="field"><label>Phone</label><input type="tel" value="+212 6 12 34 56 78" /></div>
            </div>
            <div class="row">
              <div class="field"><label>City</label>
                <select>
                  <option>Casablanca</option>
                  <option>Marrakech</option>
                  <option>Rabat</option>
                  <option>Tangier</option>
                </select>
              </div>
              <div class="field"><label>Preferred language</label>
                <select>
                  <option>English</option>
                  <option>Français</option>
                  <option>العربية</option>
                </select>
              </div>
            </div>
            <div class="field"><label>About</label><textarea
                rows="4">Looking for a family villa in Marrakech or Rabat — 4+ bedrooms, pool, quiet neighbourhood. Long-term buyer, not a flipper.</textarea>
            </div>

            <div class="section-title">Search preferences</div>
            <div class="row">
              <div class="field"><label>Property types</label>
                <div class="tags"><span class="tag-pill active">Villa</span><span
                    class="tag-pill active">Riad</span><span class="tag-pill">Apartment</span><span
                    class="tag-pill">Land</span></div>
              </div>
              <div class="field"><label>Cities of interest</label>
                <div class="tags"><span class="tag-pill active">Marrakech</span><span
                    class="tag-pill active">Rabat</span><span class="tag-pill">Essaouira</span><span
                    class="tag-pill">Tangier</span></div>
              </div>
            </div>
            <div class="row">
              <div class="field"><label>Min budget (MAD)</label><input type="text" value="3,000,000" /></div>
              <div class="field"><label>Max budget (MAD)</label><input type="text" value="12,000,000" /></div>
            </div>

            <div class="section-title">Notifications</div>
            <label class="switch"><input type="checkbox" checked /><span>Email me when a matching property is
                listed</span></label>
            <label class="switch"><input type="checkbox" checked /><span>SMS me about scheduled viewings</span></label>
            <label class="switch"><input type="checkbox" /><span>Weekly market digest (Tuesdays)</span></label>

            <div class="form-foot">
              <button class="btn-link danger">Delete account</button>
              <div><button class="btn btn-ghost" type="reset">Cancel</button><button class="btn btn-primary"
                  type="submit">Save changes <span class="arrow">→</span></button></div>
            </div>
          </form>
        </section>

        <aside class="side-info reveal" data-delay="120">
          <div class="info-card">
            <h3>Account security</h3>
            <p>Last sign-in: Casablanca · 2 days ago.</p><button class="btn btn-ghost full">Change
              password</button><button class="btn btn-ghost full">Enable 2FA</button>
          </div>
          <div class="info-card">
            <h3>Connected accounts</h3>
            <ul class="conn">
              <li><span>Google</span><b>Connected</b></li>
              <li><span>Apple</span><a href="#">Connect</a></li>
              <li><span>Facebook</span><a href="#">Connect</a></li>
            </ul>
          </div>
        </aside>
      </div>
    </main>
  </div>
  <script src="scripts/12-user-profile.js"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Edit Property</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/11-dashboard.css" />
  <link rel="stylesheet" href="styles/24-edit-property.css" />
  <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
  <div class="cursor"></div>
  <div class="cursor-dot"></div>

  <div class="app-shell">
    <aside class="sidebar">
      <div class="side-top">
        <a href="index.php" class="brand">
          <svg class="brand-mark" viewBox="0 0 100 100">
            <path
              d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z M38 38 L62 38 L62 50 L38 50 Z M38 60 L62 60 L62 72 L38 72 Z"
              fill-rule="evenodd" />
          </svg>
          <span class="brand-word">SAMSAR</span>
        </a>
      </div>
      <nav class="side-nav">
        <div class="nav-group">Main</div>
        <a href="11-dashboard.php"><span class="ico">⌂</span> Overview</a>
        <a href="23-my-properties.php" class="active"><span class="ico">▤</span> My Properties</a>
        <a href="18-add-property.php"><span class="ico">+</span> Add Property</a>
        <div class="nav-group">Social</div>
        <a href="15-messages.php"><span class="ico">✉</span> Messages</a>
        <a href="13-favorites.php"><span class="ico">♡</span> Favorites</a>
        <a href="21-following.php"><span class="ico">👥</span> Following</a>
        <a href="14-notifications.php"><span class="ico">⌖</span> Notifications</a>
        <div class="nav-group">System</div>
        <a href="12-profile.php"><span class="ico">○</span> My Profile</a>
        <a href="22-settings.php"><span class="ico">⚙</span> Settings</a>
      </nav>
      <div class="side-foot">
        <div class="user-pill">
          <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80"
            alt="Avatar" />
          <div class="u-info"><strong>Yassine A.</strong><span id="user-type-label">Private Seller</span>
          </div>
        </div>
        <button class="logout-btn" id="logout-trigger">Sign out →</button>
      </div>
    </aside>

    <main class="main-content">
      <header class="dash-header">
        <div class="h-titles">
          <h1>Edit Property</h1>
          <p>Update the details of your listing.</p>
        </div>
      </header>

      <div class="edit-form-container">
        <form id="edit-form">
          <div class="form-group">
            <label for="prop-title">Property Title</label>
            <input type="text" id="prop-title" required placeholder="e.g. Villa Tazri">
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="prop-price">Price</label>
              <input type="text" id="prop-price" required placeholder="e.g. 12,400,000 MAD">
            </div>
            <div class="form-group">
              <label for="prop-city">City</label>
              <select id="prop-city">
                <option value="Marrakech">Marrakech</option>
                <option value="Casablanca">Casablanca</option>
                <option value="Rabat">Rabat</option>
                <option value="Tangier">Tangier</option>
                <option value="Fès">Fès</option>
                <option value="Essaouira">Essaouira</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="prop-type">Property Type</label>
              <select id="prop-type">
                <option value="Villa">Villa</option>
                <option value="Apartment">Apartment</option>
                <option value="Riad">Riad</option>
                <option value="Land">Land</option>
                <option value="Commercial">Commercial</option>
              </select>
            </div>
            <div class="form-group">
              <label for="prop-status">Status</label>
              <select id="prop-status">
                <option value="active">Available</option>
                <option value="sold">Sold</option>
                <option value="rented">Rented</option>
                <option value="pending">Pending</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="prop-desc">Description</label>
            <textarea id="prop-desc" rows="6" placeholder="Describe the property..."></textarea>
          </div>

          <div class="form-actions">
            <a href="23-my-properties.php" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script src="scripts/samsar-transitions.js"></script>
  <script src="scripts/24-edit-property.js"></script>
</body>

</html>
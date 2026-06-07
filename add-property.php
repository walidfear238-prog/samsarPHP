<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Add Property</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600;700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/dashboard-shell.css" />
  <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
  <div class="cursor"></div>
  <div class="cursor-dot"></div>

  <div class="dashboard-shell">
    <aside class="dashboard-sidebar">
      <a class="dashboard-brand" href="index.php">
        <svg class="dashboard-brand-mark" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z" />
        </svg>
        <span class="dashboard-brand-word">SAMSAR</span>
      </a>
      <nav class="dashboard-nav">
        <div class="dashboard-group">MAIN</div>
        <a class="dashboard-link" href="dashboard.php"><span class="ico">⌂</span>Overview</a>
        <a class="dashboard-link" href="my-properties.php"><span class="ico">▤</span>My Properties</a>
        <a class="dashboard-link active" href="add-property.php"><span class="ico">+</span>Add Property</a>
        <div class="dashboard-group">SOCIAL</div>
        <a class="dashboard-link" href="messages.php"><span class="ico">✉</span>Messages <em class="dashboard-badge red"
            id="bdg-msg">0</em></a>
        <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span>Favorites <em
            class="dashboard-badge grey" id="bdg-fav">0</em></a>
        <a class="dashboard-link" href="following.php"><span class="ico">👥</span>Following</a>
        <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span>Notifications <em
            class="dashboard-badge red" id="bdg-notif-2">0</em></a>
      </nav>
      <div class="dashboard-side-foot">
        <div class="dashboard-user">
          <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80"
            alt="Avatar" />
          <div><strong>Yassine A.</strong><span>User</span></div>
        </div>
        <a class="dashboard-signout" href="08-login.php" data-logout>Sign out →</a>
      </div>
    </aside>

    <main class="dashboard-main">
      <header class="dashboard-head">
        <div>
          <h1>Add Property</h1>
          <p>Create a new listing for sale or rent.</p>
        </div>
      </header>

      <form id="add-form" class="ap-form">
        <div class="content-card">
          <h3 class="ap-section-title">Basic information</h3>
          <div class="ap-grid">
            <label class="ap-field">
              <span>Property Title *</span>
              <input name="title" type="text" required placeholder="e.g. Villa Tazri — Palmeraie" />
            </label>
            <label class="ap-field">
              <span>Property Type *</span>
              <select name="type" required>
                <option value="Villa">Villa</option>
                <option value="Riad">Riad</option>
                <option value="Apartment">Apartment</option>
                <option value="Penthouse">Penthouse</option>
                <option value="Land">Land</option>
                <option value="Commercial">Commercial</option>
              </select>
            </label>
            <label class="ap-field">
              <span>Price (MAD) *</span>
              <input name="price" type="text" required placeholder="e.g. 12,400,000" />
            </label>
            <label class="ap-field">
              <span>Status *</span>
              <select name="status" required>
                <option value="available">For sale</option>
                <option value="rented">For rent</option>
                <option value="sold">Sold</option>
                <option value="pending">Pending</option>
              </select>
            </label>
          </div>
          <label class="ap-field" style="margin-top:14px;display:block">
            <span>Description</span>
            <textarea name="desc" rows="4" placeholder="Describe the property, location, and key features…"></textarea>
          </label>
        </div>

        <div class="content-card">
          <h3 class="ap-section-title">Location</h3>
          <div class="ap-grid">
            <label class="ap-field">
              <span>City *</span>
              <select name="city" required>
                <option>Casablanca</option>
                <option>Marrakech</option>
                <option>Rabat</option>
                <option>Tangier</option>
                <option>Fès</option>
                <option>Essaouira</option>
                <option>Agadir</option>
              </select>
            </label>
            <label class="ap-field">
              <span>District</span>
              <input name="district" type="text" placeholder="e.g. Palmeraie" />
            </label>
          </div>
        </div>

        <div class="content-card">
          <h3 class="ap-section-title">Specifications</h3>
          <div class="ap-grid ap-grid-4">
            <label class="ap-field">
              <span>Bedrooms</span>
              <input name="beds" type="number" min="0" value="3" />
            </label>
            <label class="ap-field">
              <span>Bathrooms</span>
              <input name="baths" type="number" min="0" value="2" />
            </label>
            <label class="ap-field">
              <span>Area (m²)</span>
              <input name="area" type="number" min="0" value="150" />
            </label>
            <label class="ap-field">
              <span>Upload Image</span>
              <input name="img" type="file" accept="image/*" />
            </label>
          </div>
        </div>

        <div class="ap-actions">
          <a href="my-properties.php" class="btn btn-ghost">Cancel</a>
          <button type="submit" class="btn btn-primary">Publish Listing</button>
        </div>
      </form>
    </main>
  </div>

  <style>
    .ap-form {
      display: flex;
      flex-direction: column;
      gap: 18px;
      max-width: 980px
    }

    .ap-section-title {
      font-family: Fraunces, serif;
      font-size: 20px;
      margin: 0 0 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid #f0f0f0
    }

    .ap-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px
    }

    .ap-grid-4 {
      grid-template-columns: repeat(4, 1fr)
    }

    .ap-field {
      display: flex;
      flex-direction: column;
      gap: 6px
    }

    .ap-field span {
      font-size: 11px;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: #666;
      font-weight: 600
    }

    .ap-field input,
    .ap-field select,
    .ap-field textarea {
      background: #fff;
      border: 1px solid #e5e5e5;
      border-radius: 10px;
      padding: 12px 14px;
      font-size: 14px;
      font-family: inherit;
      transition: all .2s
    }

    .ap-field input:focus,
    .ap-field select:focus,
    .ap-field textarea:focus {
      outline: none;
      border-color: #C72C41;
      box-shadow: 0 0 0 3px rgba(199, 44, 65, .12)
    }

    .ap-field textarea {
      resize: vertical;
      min-height: 90px;
      grid-column: 1 / -1
    }

    .ap-actions {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      margin-top: 8px
    }

    @media(max-width:780px) {

      .ap-grid,
      .ap-grid-4 {
        grid-template-columns: 1fr
      }
    }
  </style>

  <script src="scripts/samsar-transitions.js"></script>
  <script src="scripts/dashboard-shell.js"></script>
  <script src="scripts/dashboard.js"></script>
  <script>
    (function () {
      const Store = window.SamsarStore;
      const form = document.getElementById('add-form');

      form.addEventListener('submit', e => {
        e.preventDefault();
        const data = new FormData(form);
        const props = Store.get('properties', []);
        const id = Date.now();
        const fallbackImg =
          'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=80';
        props.unshift({
          id,
          title: data.get('title') || 'Untitled property',
          price: (data.get('price') || '0') + ' MAD' + (data.get('status') === 'rented' ?
            '/mo' : ''),
          type: data.get('type'),
          status: data.get('status'),
          city: data.get('city'),
          beds: parseInt(data.get('beds')) || 0,
          baths: parseInt(data.get('baths')) || 0,
          area: parseInt(data.get('area')) || 0,
          img: data.get('img') || fallbackImg
        });
        Store.set('properties', props);

        // Visual feedback
        const btn = form.querySelector('button[type="submit"]');
        const orig = btn.textContent;
        btn.textContent = 'Published ✓';
        btn.style.background = '#2D7D5A';
        btn.disabled = true;
        setTimeout(() => {
          if (window.SamsarTransition) SamsarTransition.leave(() => location.href =
            'my-properties.php');
          else location.href = 'my-properties.php';
        }, 900);
      });
    })();
  </script>
</body>

</html>
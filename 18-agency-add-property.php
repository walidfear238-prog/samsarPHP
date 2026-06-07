<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Add property</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/18-agency-add-property.css" />
  <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
  <div class="cursor"></div>
  <div class="cursor-dot"></div>

  <div class="app">
    <aside class="side">
      <a href="index.php" class="brand">
        <svg class="brand-mark" viewBox="0 0 100 100">
          <path
            d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z M38 38 L62 38 L62 50 L38 50 Z M38 60 L62 60 L62 72 L38 72 Z"
            fill-rule="evenodd" />
        </svg>
        <span class="brand-word">SAMSAR</span>
        <span class="agency-tag">AGENCY</span>
      </a>
      <nav class="side-nav">
        <div class="nav-group">Main</div>
        <a href="16-agency-dashboard.php"><span class="ico">⌂</span> Overview</a>
        <a href="23-my-properties.php"><span class="ico">▤</span> My Properties</a>
        <a href="18-agency-add-property.php" class="active"><span class="ico">+</span> Add Property</a>
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
        <div class="user-mini">
          <img src="https://images.unsplash.com/photo-1572021335469-31706a17aaef?auto=format&fit=crop&w=120&q=80"
            alt="" />
          <div><strong>Atlas Real Estate</strong><span>Marrakech · Verified</span></div>
        </div>
        <a href="08-login.php" class="sign-out">Sign out →</a>
      </div>
    </aside>

    <main class="main">
      <header class="topbar">
        <div>
          <a href="17-agency-properties.php" class="back">← Properties</a>
          <h1>New listing</h1>
          <p>Fill in the details below. Your draft is saved automatically every 30 seconds.</p>
        </div>
        <div class="head-actions">
          <button class="btn btn-ghost" type="button" id="save-draft">Save draft</button>
          <a href="17-agency-properties.php" class="btn btn-outline-danger">Cancel</a>
          <button class="btn btn-primary" form="prop-form">Add property <span class="arrow">→</span></button>
        </div>
      </header>

      <div class="steps">
        <span class="step active">1 · Details</span>
        <span class="step active">2 · Location</span>
        <span class="step active">3 · Images</span>
        <span class="step">4 · Review</span>
      </div>

      <form id="prop-form" class="layout">
        <section class="form-col">
          <div class="block">
            <h2>Property details</h2>
            <div class="row">
              <div class="field"><label>Title</label><input type="text" required
                  placeholder="e.g. Villa Tazri — Palmeraie" /></div>
              <div class="field"><label>Reference</label><input type="text" placeholder="ARE-2026-0142" /></div>
            </div>
            <div class="row">
              <div class="field"><label>Type</label>
                <select>
                  <option>Villa</option>
                  <option>Riad</option>
                  <option>Apartment</option>
                  <option>Penthouse</option>
                  <option>Land</option>
                  <option>Commercial</option>
                </select>
              </div>
              <div class="field"><label>Status</label>
                <select>
                  <option>For sale</option>
                  <option>For rent</option>
                  <option>Off-market</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="field"><label>Price (MAD)</label><input type="text" required placeholder="12,400,000" /></div>
              <div class="field"><label>Price visibility</label>
                <select>
                  <option>Public</option>
                  <option>Price on request</option>
                </select>
              </div>
            </div>
            <div class="field">
              <label>Description</label>
              <textarea rows="6"
                placeholder="Tell a story. Buyers spend 8× longer on listings with rich descriptions."></textarea>
            </div>
          </div>

          <div class="block">
            <h2>Specifications</h2>
            <div class="row-4">
              <div class="field"><label>Bedrooms</label><input type="number" min="0" value="5" /></div>
              <div class="field"><label>Bathrooms</label><input type="number" min="0" value="6" /></div>
              <div class="field"><label>Living (m²)</label><input type="number" min="0" value="620" /></div>
              <div class="field"><label>Land (m²)</label><input type="number" min="0" value="2400" /></div>
            </div>
            <div class="row-4">
              <div class="field"><label>Year built</label><input type="number" value="2019" /></div>
              <div class="field"><label>Floors</label><input type="number" value="2" /></div>
              <div class="field"><label>Parking</label><input type="number" value="4" /></div>
              <div class="field"><label>Energy class</label>
                <select>
                  <option>A</option>
                  <option>B</option>
                  <option>C</option>
                  <option>D</option>
                  <option>—</option>
                </select>
              </div>
            </div>

            <label class="mini-lbl">Features</label>
            <div class="tags">
              <span class="tag-pill active">Pool</span>
              <span class="tag-pill active">Garden</span>
              <span class="tag-pill">Hammam</span>
              <span class="tag-pill active">Garage</span>
              <span class="tag-pill">Sea view</span>
              <span class="tag-pill active">Mountain view</span>
              <span class="tag-pill">Gym</span>
              <span class="tag-pill active">Smart home</span>
              <span class="tag-pill">Cinema</span>
              <span class="tag-pill">Wine cellar</span>
              <span class="tag-pill active">Solar heating</span>
              <span class="tag-pill">Borehole well</span>
            </div>
          </div>

          <div class="block">
            <h2>Location</h2>
            <div class="row">
              <div class="field"><label>City</label>
                <select>
                  <option>Marrakech</option>
                  <option>Casablanca</option>
                  <option>Rabat</option>
                  <option>Tangier</option>
                  <option>Fès</option>
                  <option>Essaouira</option>
                </select>
              </div>
              <div class="field"><label>District</label><input type="text" placeholder="Palmeraie" /></div>
            </div>
            <div class="field"><label>Street address</label><input type="text" placeholder="Route de la Palmeraie" />
            </div>
            <div class="row">
              <div class="field"><label>Postal code</label><input type="text" placeholder="40000" /></div>
              <div class="field"><label>GPS (lat, lng)</label><input type="text" placeholder="31.6700, -7.9700" /></div>
            </div>

          </div>

          <div class="block">
            <h2>Photos & media</h2>
            <p class="block-sub">Drag to reorder. The first photo is your cover. Minimum 5 photos, 1920×1280
              recommended.</p>
            <div class="upload" id="upload">
              <span class="up-icon">⬆</span>
              <strong>Drop images here or click to browse</strong>
              <span>JPG, PNG, WebP — up to 25 MB each</span>
              <input type="file" multiple accept="image/*" hidden id="file" />
            </div>
            <div class="thumbs" id="thumbs"></div>
          </div>

          <div class="block">
            <h2>Visibility</h2>
            <label class="toggle-row"><input type="checkbox" checked /><span><strong>Publish
                  immediately</strong><em>Make the listing live as soon as you click "Add property"</em></span></label>
            <label class="toggle-row"><input type="checkbox" /><span><strong>Show on agency profile</strong><em>Feature
                  this property on Atlas Real Estate's public page</em></span></label>
            <label class="toggle-row"><input type="checkbox" /><span><strong>Allow direct viewing
                  requests</strong><em>Buyers can request a visit without your manual approval</em></span></label>
          </div>

          <!-- Bottom action bar -->
          <div class="form-foot">
            <a href="17-agency-properties.php" class="btn btn-outline-danger">Cancel</a>
            <div class="foot-right">
              <button class="btn btn-ghost" type="button">Save as draft</button>
              <button class="btn btn-primary" type="submit">Add property <span class="arrow">→</span></button>
            </div>
          </div>
        </section>

        <aside class="preview-col">
          <div class="preview-card">
            <h3>Live preview</h3>
            <div class="prev-img">
              <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=600&q=80"
                alt="" />
              <span class="prev-badge">For Sale</span>
            </div>
            <div class="prev-body">
              <span class="prev-loc">Palmeraie · Marrakech</span>
              <strong class="prev-title">Villa Tazri</strong>
              <div class="prev-specs"><span>5 bd</span><span>6 ba</span><span>620 m²</span></div>
              <div class="prev-price">12,400,000 <small>MAD</small></div>
            </div>
          </div>
          <div class="tips">
            <h4>Listing tips</h4>
            <ul>
              <li>✓ Use natural light photographs</li>
              <li>✓ Mention the closest landmark</li>
              <li>✓ Add at least 8 photos for 2× more views</li>
              <li>✓ Specify language of contract</li>
              <li>✓ Disclose any pending notarial work</li>
            </ul>
          </div>
        </aside>
      </form>
    </main>
  </div>

  <!-- Success toast -->
  <div class="toast" id="toast" role="status" aria-live="polite">
    <span class="toast-ico">✓</span>
    <div><strong>Property added</strong><em>Listing is now live on SAMSAR.</em></div>
  </div>

  <script src="scripts/samsar-transitions.js"></script>
  <script src="scripts/18-agency-add-property.js"></script>
</body>

</html>
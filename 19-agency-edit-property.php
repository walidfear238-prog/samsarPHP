<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SAMSAR · Edit property</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles/19-agency-edit-property.css" />
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
          <h1>Edit listing <span class="status-pill" id="status-pill">Published</span></h1>
          <p>Reference <strong>ARE-2026-0142</strong> · <strong>284</strong> views · <strong>14</strong> leads · listed
            Oct 28, 2026.</p>
        </div>
        <div class="head-actions">
          <a href="03-property-details.php" class="btn btn-ghost">View public page</a>
          <button class="btn btn-ghost" type="button" id="unpublish">Unpublish</button>
          <a href="17-agency-properties.php" class="btn btn-outline-danger">Cancel</a>
          <button class="btn btn-primary" form="prop-form">Save changes <span class="arrow">→</span></button>
        </div>
      </header>

      <div class="steps">
        <span class="step active">1 · Details</span>
        <span class="step active">2 · Location</span>
        <span class="step active">3 · Images</span>
        <span class="step active">4 · Review</span>
      </div>

      <form id="prop-form" class="layout">
        <section class="form-col">
          <div class="block">
            <h2>Property details</h2>
            <div class="row">
              <div class="field"><label>Title</label><input type="text" required value="Villa Tazri — Palmeraie" />
              </div>
              <div class="field"><label>Reference</label><input type="text" value="ARE-2026-0142" readonly /></div>
            </div>
            <div class="row">
              <div class="field"><label>Type</label>
                <select>
                  <option selected>Villa</option>
                  <option>Riad</option>
                  <option>Apartment</option>
                  <option>Penthouse</option>
                  <option>Land</option>
                  <option>Commercial</option>
                </select>
              </div>
              <div class="field"><label>Status</label>
                <select>
                  <option selected>For sale</option>
                  <option>For rent</option>
                  <option>Off-market</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="field"><label>Price (MAD)</label><input type="text" required value="12,400,000" /></div>
              <div class="field"><label>Price visibility</label>
                <select>
                  <option selected>Public</option>
                  <option>Price on request</option>
                </select>
              </div>
            </div>
            <div class="field">
              <label>Description</label>
              <textarea
                rows="6">Set on 2,400 m² of olive-grove land in the heart of Marrakech's Palmeraie, Villa Tazri pairs the tactile warmth of traditional tadelakt with the discipline of contemporary architecture. Five en-suite bedrooms open onto shaded courtyards. A 22-metre saltwater pool stretches toward an unbroken view of the Atlas Mountains.</textarea>
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
                  <option selected>B</option>
                  <option>C</option>
                  <option>D</option>
                </select>
              </div>
            </div>

            <label class="mini-lbl">Features</label>
            <div class="tags">
              <span class="tag-pill active">Pool</span>
              <span class="tag-pill active">Garden</span>
              <span class="tag-pill active">Hammam</span>
              <span class="tag-pill active">Garage</span>
              <span class="tag-pill">Sea view</span>
              <span class="tag-pill active">Mountain view</span>
              <span class="tag-pill active">Gym</span>
              <span class="tag-pill active">Smart home</span>
              <span class="tag-pill active">Cinema</span>
              <span class="tag-pill active">Wine cellar</span>
              <span class="tag-pill active">Solar heating</span>
              <span class="tag-pill active">Borehole well</span>
            </div>
          </div>

          <div class="block">
            <h2>Location</h2>
            <div class="row">
              <div class="field"><label>City</label>
                <select>
                  <option selected>Marrakech</option>
                  <option>Casablanca</option>
                  <option>Rabat</option>
                  <option>Tangier</option>
                  <option>Fès</option>
                  <option>Essaouira</option>
                </select>
              </div>
              <div class="field"><label>District</label><input type="text" value="Palmeraie" /></div>
            </div>
            <div class="field"><label>Street address</label><input type="text" value="Route de la Palmeraie" /></div>
            <div class="row">
              <div class="field"><label>Postal code</label><input type="text" value="40000" /></div>
              <div class="field"><label>GPS (lat, lng)</label><input type="text" value="31.6700, -7.9700" /></div>
            </div>

          </div>

          <div class="block">
            <h2>Photos & media</h2>
            <p class="block-sub">Drag to reorder. The first photo is your cover. <strong>5 photos uploaded.</strong></p>
            <div class="thumbs" id="thumbs">
              <div class="thumb cover"><img
                  src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=600&q=80"
                  alt="" /><button type="button" class="thumb-rm">✕</button></div>
              <div class="thumb"><img
                  src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=600&q=80"
                  alt="" /><button type="button" class="thumb-rm">✕</button></div>
              <div class="thumb"><img
                  src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=600&q=80"
                  alt="" /><button type="button" class="thumb-rm">✕</button></div>
              <div class="thumb"><img
                  src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=600&q=80"
                  alt="" /><button type="button" class="thumb-rm">✕</button></div>
              <div class="thumb"><img
                  src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=600&q=80"
                  alt="" /><button type="button" class="thumb-rm">✕</button></div>
            </div>
            <div class="upload" id="upload">
              <span class="up-icon">⬆</span>
              <strong>Drop more images or click to browse</strong>
              <span>JPG, PNG, WebP — up to 25 MB each</span>
              <input type="file" multiple accept="image/*" hidden id="file" />
            </div>
          </div>

          <div class="block danger-zone">
            <h2>Danger zone</h2>
            <div class="dz-row">
              <div>
                <strong>Delete this listing permanently</strong>
                <span>This cannot be undone after 30 days.</span>
              </div>
              <button type="button" class="btn btn-danger" id="delete-btn">Delete listing</button>
            </div>
          </div>

          <!-- Bottom action bar -->
          <div class="form-foot">
            <a href="17-agency-properties.php" class="btn btn-outline-danger">Cancel</a>
            <div class="foot-right">
              <span class="autosave">All changes saved · 2 min ago</span>
              <button class="btn btn-primary" type="submit">Save changes <span class="arrow">→</span></button>
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
              <strong class="prev-title">Villa Tazri — Palmeraie</strong>
              <div class="prev-specs"><span>5 bd</span><span>6 ba</span><span>620 m²</span></div>
              <div class="prev-price">12,400,000 <small>MAD</small></div>
              <a href="03-property-details.php" class="prev-cta">View details <span class="arrow">→</span></a>
            </div>
          </div>

          <div class="stat-mini">
            <h4>Performance · last 30 days</h4>
            <div class="sm-row"><span>Views</span><strong>284</strong></div>
            <div class="sm-row"><span>Saves</span><strong>38</strong></div>
            <div class="sm-row"><span>Leads</span><strong>14</strong></div>
            <div class="sm-row"><span>Viewings</span><strong>6</strong></div>
          </div>

          <div class="tips">
            <h4>Edit tips</h4>
            <ul>
              <li>✓ A price change notifies users who favourited this listing</li>
              <li>✓ Re-ordering photos refreshes the public carousel</li>
              <li>✓ Status changes go live within 60 seconds</li>
            </ul>
          </div>
        </aside>
      </form>
    </main>
  </div>

  <!-- Delete confirm modal -->
  <div class="confirm" id="confirm" aria-hidden="true" role="dialog">
    <div class="cf-backdrop" data-close></div>
    <div class="cf-panel">
      <h3>Delete Villa Tazri?</h3>
      <p>This will remove the listing from SAMSAR. You can restore it within 30 days from the Trash folder.</p>
      <div class="cf-actions">
        <button class="btn btn-ghost" type="button" data-close>Cancel</button>
        <button class="btn btn-danger" type="button" id="cf-confirm">Delete permanently</button>
      </div>
    </div>
  </div>

  <!-- Save toast -->
  <div class="toast" id="toast" role="status" aria-live="polite">
    <span class="toast-ico">✓</span>
    <div><strong>Changes saved</strong><em>Updates are live on SAMSAR.</em></div>
  </div>

  <script src="scripts/samsar-transitions.js"></script>
  <script src="scripts/19-agency-edit-property.js"></script>
</body>

</html>
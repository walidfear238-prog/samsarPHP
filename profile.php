<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Profile</title>
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
                <a class="dashboard-link" href="add-property.php"><span class="ico">+</span>Add Property</a>
                <div class="dashboard-group">SOCIAL</div>
                <a class="dashboard-link" href="messages.php"><span class="ico">✉</span>Messages <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span>Favorites <em
                        class="dashboard-badge red" id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span>Following</a>
                <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span>Notifications <em
                        class="dashboard-badge red" id="bdg-notif-2">0</em></a>
            </nav>
            <div class="dashboard-side-foot">
                <div class="dashboard-user">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80"
                        alt="Avatar" />
                    <div><strong>Yassine A.</strong><span>User</span></div>
                </div>
                <a class="dashboard-signout" href="logout.php" data-logout>Sign out →</a>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-head">
                <div>
                    <h1>Profile</h1>
                    <p>Manage your public information and account preferences.</p>
                </div>
            </header>

            <form id="profile-form" class="ap-form" style="max-width:760px">
                <div class="content-card" style="display:flex;gap:20px;align-items:center">
                    <img id="prof-avatar"
                        src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80"
                        style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,.06)" />
                    <div style="flex:1">
                        <input type="file" id="avatar-input" accept="image/*" hidden />
                        <button type="button" class="btn btn-ghost" id="change-avatar">Change photo</button>
                        <p style="margin:8px 0 0;font-size:12px;color:#888">JPG, PNG. Max 2MB.</p>
                    </div>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title">Personal information</h3>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span>Full name</span>
                            <input name="name" type="text" value="Yassine A." required />
                        </label>
                        <label class="ap-field">
                            <span>Email</span>
                            <input name="email" type="email" value="yassine@samsar.ma" required />
                        </label>
                        <label class="ap-field">
                            <span>Phone</span>
                            <input name="phone" type="tel" value="+212 6 12 34 56 78" />
                        </label>
                        <label class="ap-field">
                            <span>City</span>
                            <select name="city">
                                <option>Casablanca</option>
                                <option selected>Marrakech</option>
                                <option>Rabat</option>
                                <option>Tangier</option>
                                <option>Fès</option>
                                <option>Essaouira</option>
                            </select>
                        </label>
                    </div>
                    <label class="ap-field" style="margin-top:14px;display:block">
                        <span>Bio</span>
                        <textarea name="bio" rows="3"
                            placeholder="Tell the community a little about yourself…">Looking for a family villa in Marrakech or Rabat. 4+ bedrooms, pool, quiet neighbourhood.</textarea>
                    </label>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title">Notification preferences</h3>
                    <label class="ap-check"><input type="checkbox" checked /><span>Email me when a matching property is
                            listed</span></label>
                    <label class="ap-check"><input type="checkbox" checked /><span>SMS me about scheduled
                            viewings</span></label>
                    <label class="ap-check"><input type="checkbox" /><span>Weekly market digest
                            (Tuesdays)</span></label>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px">
                    <button type="button" class="btn btn-ghost" id="cancel-profile">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </main>
    </div>

    <style>
    .ap-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
        max-width: 760px
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
        min-height: 80px;
        grid-column: 1 / -1
    }

    .ap-check {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 14px;
        border: 1px solid #ececec;
        border-radius: 10px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all .2s
    }

    .ap-check:hover {
        border-color: #1A1A1A
    }

    .ap-check input {
        accent-color: #C72C41;
        width: 18px;
        height: 18px
    }

    .ap-check span {
        font-size: 14px
    }

    @media(max-width:780px) {
        .ap-grid {
            grid-template-columns: 1fr
        }
    }
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>
    <script>
    (function() {
        const Store = window.SamsarStore;

        // Pre-fill from saved data
        const saved = Store.get('profile', null);
        if (saved) {
            const form = document.getElementById('profile-form');
            Object.keys(saved).forEach(k => {
                const el = form.querySelector(`[name="${k}"]`);
                if (el) el.value = saved[k];
            });
            if (saved.avatar) {
                document.getElementById('prof-avatar').src = saved.avatar;
            }
        }

        // Avatar upload
        document.getElementById('change-avatar').addEventListener('click', () => {
            document.getElementById('avatar-input').click();
        });
        document.getElementById('avatar-input').addEventListener('change', e => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => {
                document.getElementById('prof-avatar').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });

        // Cancel
        document.getElementById('cancel-profile').addEventListener('click', () => {
            document.getElementById('profile-form').reset();
        });

        // Save
        document.getElementById('profile-form').addEventListener('submit', e => {
            e.preventDefault();
            const data = new FormData(e.target);
            const profile = {};
            data.forEach((v, k) => profile[k] = v);
            profile.avatar = document.getElementById('prof-avatar').src;
            Store.set('profile', profile);

            // Update sidebar name
            const nameEl = document.querySelector('.dashboard-user strong');
            if (nameEl) nameEl.textContent = profile.name || 'User';

            const btn = e.target.querySelector('button[type="submit"]');
            const orig = btn.textContent;
            btn.textContent = 'Saved ✓';
            btn.style.background = '#2D7D5A';
            btn.disabled = true;
            setTimeout(() => {
                btn.textContent = orig;
                btn.style.background = '';
                btn.disabled = false;
            }, 1500);
        });
    })();
    </script>
</body>

</html>
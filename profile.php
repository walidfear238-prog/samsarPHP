<?php

session_start();
require "db/connect.php";
if (!isset($_SESSION['user_id'])) {
    header('location: 08-login.php');
    exit;
}

$id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT firstname, lastname, email, phone, city, role, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$defaultAvatar = 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=120&q=80';
$fullName = trim($user['firstname'] . ' ' . $user['lastname']);
$avatarSrc = $user['profile_image'] ?: $defaultAvatar;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-i18n-doctitle="profile.title">SAMSAR · Profile</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/dashboard-shell.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
    <link rel="stylesheet" href="css/rtl.css" />
    <script src="js/translations.js"></script>
    <script src="js/language-switcher.js"></script>
</head>

<body>
    <div class="cursor"></div>
    <div class="cursor-dot"></div>

    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            <a class="dashboard-brand" href="index.php">
                <svg width="25" height="25
                " class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">
                    <path
                        d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
                </svg>
                <span class="dashboard-brand-word">SAMSAR</span>
            </a>
            <nav class="dashboard-nav">
                <div class="dashboard-group"><span data-i18n="dash.group.main">MAIN</span></div>
                <a class="dashboard-link" href="dashboard.php"><span class="ico">⌂</span><span data-i18n="dash.overview">Overview</span></a>
                <a class="dashboard-link active" href="profile.php"><span class="ico">☺</span><span data-i18n="dash.profile">Profile</span></a>
                <a class="dashboard-link" href="my-properties.php"><span class="ico">▤</span><span data-i18n="dash.myproperties">My Properties</span></a>
                <a class="dashboard-link" href="add-property.php"><span class="ico">+</span><span data-i18n="dash.addproperty">Add Property</span></a>
                <div class="dashboard-group"><span data-i18n="dash.group.social">SOCIAL</span></div>
                <a class="dashboard-link" href="messages.php"><span class="ico">✉</span><span data-i18n="dash.messages">Messages</span> <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span><span data-i18n="dash.favorites">Favorites</span> <em
                        class="dashboard-badge red" id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span><span data-i18n="dash.following">Following</span></a>
                <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span><span data-i18n="dash.notifications">Notifications</span> <em
                        class="dashboard-badge red" id="bdg-notif-2">0</em></a>
            </nav>
            <div class="dashboard-side-foot">
                <div class="dashboard-user">
                    <img id="sidebar-avatar" src="<?php echo htmlspecialchars($avatarSrc); ?>"
                        alt="Avatar" />
                    <div><strong id="sidebar-name"><?php echo htmlspecialchars($fullName); ?></strong><span><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span></div>
                </div>
                <a class="dashboard-signout" href="logout.php" data-logout><span data-i18n="dash.signout">Sign out</span> →</a>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-head">
                <div>
                    <h1 data-i18n="dash.profile">Profile</h1>
                    <p data-i18n="profile.subtitle">Manage your public information and account preferences.</p>
                </div>
            </header>

            <div id="profile-status" class="ap-status" role="status" aria-live="polite"></div>

            <form id="profile-form" class="ap-form" style="max-width:760px" enctype="multipart/form-data">
                <div class="content-card" style="display:flex;gap:20px;align-items:center">
                    <img id="prof-avatar"
                        src="<?php echo htmlspecialchars($avatarSrc); ?>"
                        style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,.06)" />
                    <div style="flex:1">
                        <input type="file" name="avatar" id="avatar-input" accept="image/png,image/jpeg" hidden />
                        <button type="button" class="btn btn-ghost" id="change-avatar" data-i18n="profile.changephoto">Change photo</button>
                        <p style="margin:8px 0 0;font-size:12px;color:#888" data-i18n="profile.filehint">JPG, PNG. Max 2MB.</p>
                    </div>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title" data-i18n="profile.personalinfo">Personal information</h3>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span data-i18n="modal.fullname">Full name</span>
                            <input name="name" type="text" value="<?php echo htmlspecialchars($fullName); ?>" required />
                        </label>
                        <label class="ap-field">
                            <span data-i18n="modal.email">Email</span>
                            <input name="email" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" required />
                        </label>
                        <label class="ap-field">
                            <span data-i18n="agencyprofile.phone">Phone</span>
                            <input name="phone" type="tel" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" />
                        </label>
                        <label class="ap-field">
                            <span data-i18n="modal.city">City</span>
                            <?php
                            $cityOptions = [
                                'casablanca' => 'Casablanca',
                                'marrakech'  => 'Marrakech',
                                'rabat'      => 'Rabat',
                                'tangier'    => 'Tangier',
                                'fès'        => 'Fès',
                                'essaouira'  => 'Essaouira',
                            ];
                            $currentCity = $user['city'] ?? '';
                            ?>
                            <select name="city">
                                <option value="" <?php echo $currentCity === '' ? 'selected' : ''; ?>></option>
                                <?php foreach ($cityOptions as $val => $label): ?>
                                <option value="<?php echo htmlspecialchars($val); ?>" <?php echo $currentCity === $val ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <label class="ap-field" style="margin-top:14px;display:block">
                        <span data-i18n="profile.bio">Bio</span>
                        <textarea name="bio" rows="3"
                            placeholder="Tell the community a little about yourself…" data-i18n-placeholder="profile.bio.placeholder"></textarea>
                    </label>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title" data-i18n="profile.changepassword">Change password</h3>
                    <p style="margin:0 0 14px;font-size:12px;color:#888" data-i18n="profile.passwordhint">Leave blank to keep your current password. Minimum 6 characters.</p>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span data-i18n="profile.newpassword">New password</span>
                            <input name="new_password" type="password" autocomplete="new-password" minlength="6" />
                        </label>
                        <label class="ap-field">
                            <span data-i18n="profile.confirmpassword">Confirm new password</span>
                            <input name="confirm_password" type="password" autocomplete="new-password" minlength="6" />
                        </label>
                    </div>
                </div>

                <div class="content-card">
                    <h3 class="ap-section-title" data-i18n="profile.notifprefs">Notification preferences</h3>
                    <label class="ap-check"><input type="checkbox" checked /><span data-i18n="profile.notif1">Email me when a matching property is
                            listed</span></label>
                    <label class="ap-check"><input type="checkbox" checked /><span data-i18n="profile.notif2">SMS me about scheduled
                            viewings</span></label>
                    <label class="ap-check"><input type="checkbox" /><span data-i18n="profile.notif3">Weekly market digest
                            (Tuesdays)</span></label>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px">
                    <button type="button" class="btn btn-ghost" id="cancel-profile" data-i18n="myproperties.cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-i18n="profile.savechanges">Save changes</button>
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

    .ap-status {
        display: none;
        max-width: 760px;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
        margin-bottom: 18px
    }

    .ap-status.is-visible {
        display: block
    }

    .ap-status.is-success {
        background: #E9F6EF;
        color: #1E6B47;
        border: 1px solid #BFE6D2
    }

    .ap-status.is-error {
        background: #FBEAEC;
        color: #A50034;
        border: 1px solid #F1C3CB
    }
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>
    <script>
    (function() {
        const form = document.getElementById('profile-form');
        const statusEl = document.getElementById('profile-status');
        const originalAvatarSrc = document.getElementById('prof-avatar').src;

        function showStatus(text, isError) {
            statusEl.textContent = text;
            statusEl.classList.remove('is-error', 'is-success');
            statusEl.classList.add(isError ? 'is-error' : 'is-success', 'is-visible');
        }

        // Avatar upload (local preview only — the real file is sent on save)
        document.getElementById('change-avatar').addEventListener('click', () => {
            document.getElementById('avatar-input').click();
        });
        document.getElementById('avatar-input').addEventListener('change', e => {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                showStatus(window.t ? window.t('profile.err.file_too_large') : 'Image must be smaller than 2MB.', true);
                e.target.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = ev => {
                document.getElementById('prof-avatar').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });

        // Cancel — revert every field (including the avatar preview) to the saved DB values
        document.getElementById('cancel-profile').addEventListener('click', () => {
            form.reset();
            document.getElementById('prof-avatar').src = originalAvatarSrc;
            statusEl.classList.remove('is-visible');
        });

        // Save
        form.addEventListener('submit', async e => {
            e.preventDefault();
            statusEl.classList.remove('is-visible');

            const newPassword = form.querySelector('[name="new_password"]').value;
            const confirmPassword = form.querySelector('[name="confirm_password"]').value;

            if (newPassword && newPassword.length < 6) {
                showStatus(window.t ? window.t('profile.err.password_length') : 'Password must be at least 6 characters.', true);
                return;
            }
            if (newPassword !== confirmPassword) {
                showStatus(window.t ? window.t('profile.err.password_mismatch') : 'Password confirmation does not match.', true);
                return;
            }

            const formData = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            const orig = btn.textContent;
            btn.disabled = true;
            btn.textContent = window.t ? window.t('editproperty.saving', 'Saving…') : 'Saving…';

            try {
                const res = await fetch('api/update-profile.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                const result = await res.json().catch(() => ({}));

                if (result.success) {
                    showStatus(result.message || (window.t ? window.t('profile.success.updated') : 'Profile updated successfully ✓'), false);

                    // Reflect the change immediately without a reload
                    if (result.user) {
                        const nameEl = document.getElementById('sidebar-name');
                        if (nameEl && result.user.name) nameEl.textContent = result.user.name;
                        const avatarEl = document.getElementById('prof-avatar');
                        const sidebarAvatarEl = document.getElementById('sidebar-avatar');
                        if (result.user.avatar) {
                            if (avatarEl) avatarEl.src = result.user.avatar;
                            if (sidebarAvatarEl) sidebarAvatarEl.src = result.user.avatar;
                        }
                    }

                    // Clear password fields — never re-populate them
                    form.querySelector('[name="new_password"]').value = '';
                    form.querySelector('[name="confirm_password"]').value = '';

                    setTimeout(() => statusEl.classList.remove('is-visible'), 5000);
                } else {
                    showStatus(result.message || (window.t ? window.t('profile.err.update_failed') : 'Failed to update your profile. Please try again.'), true);
                }
            } catch (err) {
                showStatus('Network error — please check your connection and try again.', true);
            } finally {
                btn.disabled = false;
                btn.textContent = orig;
            }
        });
    })();
    </script>
</body>

</html>
<?php
session_start();
require "db/connect.php";
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}

// Used by the inline script below so fetch paths resolve correctly whether
// SAMSAR lives at the web root or in a subdirectory like /samsar/.
$samsar_base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-i18n-doctitle="notifications.title">SAMSAR · Notifications</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/dashboard-shell.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
    <script>
    // Resolves API paths correctly regardless of subdirectory.
    window.SAMSAR_BASE = <?php echo json_encode($samsar_base); ?>;
    </script>
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
                <a class="dashboard-link" href="my-properties.php"><span class="ico">▤</span><span data-i18n="dash.myproperties">My Properties</span></a>
                <a class="dashboard-link" href="add-property.php"><span class="ico">+</span><span data-i18n="dash.addproperty">Add Property</span></a>
                <div class="dashboard-group"><span data-i18n="dash.group.social">SOCIAL</span></div>
                <a class="dashboard-link" href="messages.php"><span class="ico">✉</span><span data-i18n="dash.messages">Messages</span> <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span><span data-i18n="dash.favorites">Favorites</span> <em
                        class="dashboard-badge red" id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span><span data-i18n="dash.following">Following</span></a>
                <a class="dashboard-link active" href="notifications.php"><span class="ico">⌖</span><span data-i18n="dash.notifications">Notifications</span> <em
                        class="dashboard-badge red" id="bdg-notif-2">0</em></a>
            </nav>
            <!-- profile name and role and profile image -->
            <?php
            $id = $_SESSION['user_id'];

            $stmt = $conn->prepare("SELECT firstname , role , profile_image FROM users where id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();


            ?>
            <div class="dashboard-side-foot">
                <div class="dashboard-user">
                    <?php
                    echo "<img src='" . htmlspecialchars($user['profile_image']) . "'" .
                        "alt='profile picture'/>";


                    echo " <div><strong>" . htmlspecialchars($user['firstname']) . "</strong><span>" .
                        htmlspecialchars($user['role']) . "</span></div>";
                    ?>
                </div>
                <a class="dashboard-signout" href="logout.php" data-logout><span data-i18n="dash.signout">Sign out</span> →</a>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-head">
                <div>
                    <h1 data-i18n="dash.notifications">Notifications</h1>
                    <p data-i18n="notifications.subtitle">Updates from your properties and social activity.</p>
                </div>
                <button class="btn btn-ghost" id="mark-read" data-i18n="notifications.markallread">Mark all as read</button>
            </header>
            <div class="content-card" style="padding:0;overflow:hidden">
                <ul class="notif-list" id="notif-list-full"></ul>
            </div>
        </main>
    </div>

    <style>
    .notif-list {
        list-style: none;
        padding: 0;
        margin: 0
    }

    .notif-item {
        display: flex;
        gap: 14px;
        padding: 18px 22px;
        border-bottom: 1px solid #f4f4f2;
        transition: background .2s;
        cursor: pointer
    }

    .notif-item:last-child {
        border: none
    }

    .notif-item:hover {
        background: #fafafa
    }

    .notif-item.unread {
        background: #fff8f8
    }

    .notif-item.unread:hover {
        background: #fff0f0
    }

    .notif-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #ddd;
        margin-top: 6px;
        flex-shrink: 0
    }

    .notif-item.unread .notif-dot {
        background: #C72C41
    }

    .notif-item p {
        margin: 0;
        font-size: 14px;
        line-height: 1.45;
        color: #1A1A1A
    }

    .notif-time {
        font-size: 12px;
        color: #888;
        display: block;
        margin-top: 4px
    }

    .notif-empty {
        padding: 60px 20px;
        text-align: center;
        color: #888
    }

    .notif-hint {
        font-size: 11px;
        color: #C72C41;
        display: block;
        margin-top: 2px
    }
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>
    <script>
    (function() {
        const BASE = (window.SAMSAR_BASE || '').replace(/\/+$/, '');
        const list = document.getElementById('notif-list-full');
        const markAllBtn = document.getElementById('mark-read');

        function timeAgo(dateStr) {
            if (!dateStr) return '';
            const then = new Date(dateStr.replace(' ', 'T'));
            const diff = Math.max(0, Math.floor((Date.now() - then.getTime()) / 1000));
            if (diff < 60) return window.t ? window.t('notifications.just_now') : 'just now';
            if (diff < 3600) return Math.floor(diff / 60) + (window.t ? window.t('notifications.unit_min') : 'm');
            if (diff < 86400) return Math.floor(diff / 3600) + (window.t ? window.t('notifications.unit_hour') : 'h');
            return Math.floor(diff / 86400) + (window.t ? window.t('notifications.unit_day') : 'd');
        }

        function esc(s) {
            return String(s ?? '')
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Build the URL a notification should jump to when clicked.
        // Returns null for notifications with no clickable target.
        function targetUrl(n) {
            if (!n.link) return null;
            // Message notifications link to "otherUserId_propertyId"
            if (n.type === 'message' && /^[0-9]+_[0-9]+$/.test(n.link)) {
                return BASE + '/messages.php?open=' + encodeURIComponent(n.link);
            }
            // Favorite notifications link to the favorited property.
            if (n.type === 'favorite' && /^property:\d+$/.test(n.link)) {
                return BASE + '/03-property-details.php?id=' + n.link.split(':')[1];
            }
            // Follow notifications link to the follower's agency profile.
            if (n.type === 'follow' && /^user:\d+$/.test(n.link)) {
                return BASE + '/05-agency-profile.php?id=' + n.link.split(':')[1];
            }
            return null;
        }

        function render(notifs) {
            if (!notifs.length) {
                list.innerHTML = '<li class="notif-empty">' + (window.t ? window.t('notifications.empty') : 'No notifications yet.') + '</li>';
                return;
            }
            list.innerHTML = notifs.map(n => {
                const url = targetUrl(n);
                const hint = url ? '<span class="notif-hint">' + (window.t ? window.t('notifications.click_to_open') : 'Click to open →') + '</span>' : '';
                return `
      <li class="notif-item ${n.read ? '' : 'unread'}" data-id="${n.id}" data-url="${esc(url || '')}">
        <span class="notif-dot"></span>
        <div style="flex:1">
          <p>${n.title ? '<strong>' + esc(n.title) + '</strong> ' : ''}${esc(n.text)}</p>
          <span class="notif-time">${timeAgo(n.created_at)} ${window.t ? window.t('notifications.ago') : 'ago'}</span>
          ${hint}
        </div>
      </li>
    `;
            }).join('');

            list.querySelectorAll('.notif-item').forEach(item => {
                item.addEventListener('click', () => {
                    const id = parseInt(item.dataset.id, 10);
                    const url = item.dataset.url;
                    markRead(id, url);
                });
            });
        }

        // Loads this user's notifications from the real backend (session-scoped, auth-checked server-side)
        function load() {
            fetch(BASE + '/api/get-notifications.php?limit=100')
                .then(r => {
                    if (r.status === 401) {
                        list.innerHTML = '<li class="notif-empty">' + (window.t ? window.t('notifications.please_signin') : 'Please sign in to view notifications.') + '</li>';
                        return [];
                    }
                    return r.ok ? r.json() : [];
                })
                .then(notifs => {
                    if (Array.isArray(notifs)) render(notifs);
                })
                .catch(() => {
                    list.innerHTML = '<li class="notif-empty">' + (window.t ? window.t('notifications.load_error') : "Couldn't load notifications.") + '</li>';
                });
        }

        // Keeps the sidebar badge / unread count in sync with the database after any change
        function syncBadges() {
            if (window.SamsarApp && typeof window.SamsarApp.refreshMessagingData === 'function') {
                window.SamsarApp.refreshMessagingData();
            }
        }

        // Persists read state to the database. If `url` is provided,
        // the user is navigated there AFTER the read-state is recorded.
        function markRead(id, url) {
            fetch(BASE + '/api/mark-notification-read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(id ? {
                        id
                    } : {})
                })
                .then(r => r.ok ? r.json() : null)
                .then(() => {
                    syncBadges();
                    if (url) {
                        // Small delay so the badge update has time to flush
                        setTimeout(() => {
                            window.location.href = url;
                        }, 120);
                    } else {
                        load();
                    }
                })
                .catch(() => {});
        }

        markAllBtn.addEventListener('click', () => markRead(null, null));

        load();
    })();
    </script>
    <script src="scripts/dashboard-mobile-nav.js"></script>
</body>

</html>
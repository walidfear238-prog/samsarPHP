<?php
session_start();
require "db/connect.php";
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Notifications</title>
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
                <svg class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">
                    <path
                        d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
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
                <a class="dashboard-link active" href="notifications.php"><span class="ico">⌖</span>Notifications <em
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
                <a class="dashboard-signout" href="logout.php" data-logout>Sign out →</a>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-head">
                <div>
                    <h1>Notifications</h1>
                    <p>Updates from your properties and social activity.</p>
                </div>
                <button class="btn btn-ghost" id="mark-read">Mark all as read</button>
            </header>
            <div class="content-card" style="padding:0;overflow:hidden">
                <ul class="notif-list" id="notif-list"></ul>
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
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>
    <script>
    (function() {
        const Store = window.SamsarStore;
        const list = document.getElementById('notif-list');

        function render() {
            const notifs = Store.get('notifications', []);
            if (!notifs.length) {
                list.innerHTML = '<li class="notif-empty">No notifications yet.</li>';
                return;
            }
            list.innerHTML = notifs.map(n => `
      <li class="notif-item ${n.read ? '' : 'unread'}" data-id="${n.id}">
        <span class="notif-dot"></span>
        <div style="flex:1">
          <p>${n.text}</p>
          <span class="notif-time">${n.time} ago</span>
        </div>
      </li>
    `).join('');

            list.querySelectorAll('.notif-item').forEach(item => {
                item.addEventListener('click', () => {
                    const id = parseInt(item.dataset.id);
                    const notifs = Store.get('notifications', []);
                    const n = notifs.find(x => x.id === id);
                    if (n) n.read = true;
                    Store.set('notifications', notifs);
                    render();
                    if (window.SamsarApp) SamsarApp.paintOverview();
                });
            });
        }

        document.getElementById('mark-read').addEventListener('click', () => {
            const notifs = Store.get('notifications', []).map(n => ({
                ...n,
                read: true
            }));
            Store.set('notifications', notifs);
            render();
            if (window.SamsarApp) SamsarApp.paintOverview();
        });

        render();
    })();
    </script>
</body>

</html>
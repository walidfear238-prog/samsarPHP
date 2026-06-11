<?php

session_start();
require "db/connect.php";
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}








?>





<!DOCTYPE html>
<html lang="en">



<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Dashboard</title>
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
                <a class="dashboard-link active" href="dashboard.php"><span class="ico">⌂</span>Overview</a>
                <a class="dashboard-link" href="my-properties.php"><span class="ico">▤</span>My Properties</a>
                <a class="dashboard-link" href="add-property.php"><span class="ico">+</span>Add Property</a>
                <div class="dashboard-group">SOCIAL</div>
                <a class="dashboard-link" href="messages.php"><span class="ico">✉</span>Messages <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span>Favorites <em
                        class="dashboard-badge red" id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span>Following</a>
                <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span>Notifications <em
                        class="dashboard-badge red" id="bdg-notif">0</em></a>
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
                    <h1>Overview</h1>
                    <p>Manage your listings, conversations and social activity.</p>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <a class="btn btn-ghost" href="02-properties.php">Browse Market</a>
                    <a class="btn btn-primary" href="add-property.php">+ Add Property</a>
                </div>
            </header>

            <div
                style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:26px">
                <div class="content-card">
                    <div style="font-size:12px;letter-spacing:.12em;color:#666;text-transform:uppercase">Active
                        Listings
                    </div>
                    <div id="stat-listings" style="font-family:Fraunces,serif;font-size:34px;margin-top:8px">0</div>
                    <a href="my-properties.php"
                        style="font-size:12px;color:#C72C41;margin-top:8px;display:inline-block">Manage
                        →</a>
                </div>
                <div class="content-card">
                    <div style="font-size:12px;letter-spacing:.12em;color:#666;text-transform:uppercase">Favorites
                    </div>
                    <div id="stat-favorites" style="font-family:Fraunces,serif;font-size:34px;margin-top:8px">0
                    </div>
                    <a href="favorites.php"
                        style="font-size:12px;color:#C72C41;margin-top:8px;display:inline-block">View
                        →</a>
                </div>
                <div class="content-card">
                    <div style="font-size:12px;letter-spacing:.12em;color:#666;text-transform:uppercase">Messages
                    </div>
                    <div id="stat-notifications" style="font-family:Fraunces,serif;font-size:34px;margin-top:8px">0
                    </div>
                    <a href="messages.php" style="font-size:12px;color:#C72C41;margin-top:8px;display:inline-block">Open
                        →</a>
                </div>
                <div class="content-card">
                    <div style="font-size:12px;letter-spacing:.12em;color:#666;text-transform:uppercase">Followers
                    </div>
                    <div id="stat-followers" style="font-family:Fraunces,serif;font-size:34px;margin-top:8px">0
                    </div>
                    <a href="following.php"
                        style="font-size:12px;color:#C72C41;margin-top:8px;display:inline-block">View
                        →</a>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:24px">
                <section>
                    <div class="content-card" style="margin-bottom:18px">
                        <h3 style="font-family:Fraunces,serif;font-size:24px;margin:0 0 6px">Quick Actions</h3>
                        <p style="margin:0 0 18px;color:#666">Jump back into the most common tasks.</p>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
                            <a class="btn btn-primary" href="add-property.php" style="text-align:center">+ Add
                                Property</a>
                            <a class="btn btn-ghost" href="messages.php" style="text-align:center">Open Messages</a>
                            <a class="btn btn-ghost" href="favorites.php" style="text-align:center">View
                                Favorites</a>
                        </div>
                    </div>

                    <div class="content-card">
                        <h3 style="font-family:Fraunces,serif;font-size:22px;margin:0 0 14px">Latest Notifications
                        </h3>
                        <ul id="notif-list" class="notif-mini-list"></ul>
                        <a href="notifications.php"
                            style="display:inline-block;margin-top:14px;color:#C72C41;font-size:13px;font-weight:600">View
                            all →</a>
                    </div>
                </section>

                <aside>
                    <div class="content-card" style="margin-bottom:18px">
                        <h3 style="font-family:Fraunces,serif;font-size:20px;margin:0 0 12px">Quick Messages</h3>
                        <a href="messages.php" class="msg-mini">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80"
                                alt="" />
                            <div><strong>Karim B.</strong><span>Is the price negotiable on Riad Souira?</span></div>
                        </a>
                        <a href="messages.php" class="msg-mini">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=100&q=80"
                                alt="" />
                            <div><strong>Élise M.</strong><span>I would like to book a visit for Saturday.</span>
                            </div>
                        </a>
                    </div>

                    <div class="content-card">
                        <h3 style="font-family:Fraunces,serif;font-size:20px;margin:0 0 12px">Following</h3>
                        <a href="following.php" class="follow-mini">
                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=100&q=80"
                                alt="" />
                            <div><strong>Atlas Real Estate</strong><span>Marrakech · 86 listings</span></div>
                        </a>
                        <a href="following.php" class="follow-mini">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=100&q=80"
                                alt="" />
                            <div><strong>Élise M.</strong><span>Casablanca · 2 listings</span></div>
                        </a>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <style>
    .notif-mini-list {
        list-style: none;
        padding: 0;
        margin: 0
    }

    .notif-mini-list li {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0
    }

    .notif-mini-list li:last-child {
        border: none
    }

    .notif-mini-list .notif-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #C72C41;
        margin-top: 8px;
        flex-shrink: 0
    }

    .notif-mini-list .notif-dot.red {
        background: #C72C41
    }

    .notif-mini-list .notif-dot.empty {
        background: transparent;
        border: 1px solid #ddd
    }

    .notif-mini-list p {
        margin: 0;
        font-size: 14px;
        line-height: 1.4
    }

    .notif-mini-list .notif-time {
        font-size: 11px;
        color: #888
    }

    .msg-mini,
    .follow-mini {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 12px;
        border-radius: 10px;
        background: #f8f8f6;
        margin-bottom: 10px;
        transition: background .2s
    }

    .msg-mini:hover,
    .follow-mini:hover {
        background: #f0f0ee
    }

    .msg-mini img,
    .follow-mini img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover
    }

    .msg-mini strong,
    .follow-mini strong {
        display: block;
        font-size: 13px
    }

    .msg-mini span,
    .follow-mini span {
        display: block;
        font-size: 12px;
        color: #777
    }
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>
</body>

</html>
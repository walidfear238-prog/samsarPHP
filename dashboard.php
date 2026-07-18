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
    <title data-i18n-doctitle="dashboard.title">SAMSAR · Dashboard</title>
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
                <a class="dashboard-link active" href="dashboard.php"><span class="ico">⌂</span><span
                        data-i18n="dash.overview">Overview</span></a>
                <a class="dashboard-link" href="profile.php"><span class="ico">☺</span><span
                        data-i18n="dash.profile">Profile</span></a>
                <a class="dashboard-link" href="my-properties.php"><span class="ico">▤</span><span
                        data-i18n="dash.myproperties">My Properties</span></a>
                <a class="dashboard-link" href="add-property.php"><span class="ico">+</span><span
                        data-i18n="dash.addproperty">Add Property</span></a>
                <div class="dashboard-group"><span data-i18n="dash.group.social">SOCIAL</span></div>
                <a class="dashboard-link" href="messages.php"><span class="ico">✉</span><span
                        data-i18n="dash.messages">Messages</span> <em class="dashboard-badge red"
                        id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span><span
                        data-i18n="dash.favorites">Favorites</span> <em class="dashboard-badge red"
                        id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span><span
                        data-i18n="dash.following">Following</span></a>
                <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span><span
                        data-i18n="dash.notifications">Notifications</span> <em class="dashboard-badge red"
                        id="bdg-notif">0</em></a>
            </nav>


            <!-- profile name and role and profile image -->
            <?php
            $id = $_SESSION['user_id'];

            $stmt = $conn->prepare("SELECT firstname , role , profile_image FROM users where id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // ---- Overview page live stats (replaces hardcoded/demo values) ----
            $defaultAvatar = 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80';

            // Active Listings: this user's properties that aren't sold/rented/draft
            $listingsStmt = $conn->prepare("SELECT COUNT(*) AS count FROM properties WHERE user_id = ? AND status NOT IN ('sold','rented','draft')");
            $listingsStmt->bind_param("i", $id);
            $listingsStmt->execute();
            $activeListingsCount = (int) $listingsStmt->get_result()->fetch_assoc()['count'];
            $listingsStmt->close();

            // Followers: users who follow this account
            $followersStmt = $conn->prepare("SELECT COUNT(*) AS count FROM following WHERE following_id = ?");
            $followersStmt->bind_param("i", $id);
            $followersStmt->execute();
            $followersCount = (int) $followersStmt->get_result()->fetch_assoc()['count'];
            $followersStmt->close();

            // Quick Messages: latest message with each of the 2 most recent conversation partners.
            // NOTE: your database has no `conversations` table — messages are stored directly
            // with sender_id/receiver_id, so "conversations" are derived by grouping on the
            // other participant's id and taking their most recent message (highest id).
            $msgStmt = $conn->prepare("
                SELECT m.id, m.message, m.created_at, m.sender_id, m.receiver_id,
                    IF(m.sender_id = ?, m.receiver_id, m.sender_id) AS other_user_id
                FROM messages m
                WHERE m.id IN (
                    SELECT MAX(id) FROM messages
                    WHERE sender_id = ? OR receiver_id = ?
                    GROUP BY IF(sender_id = ?, receiver_id, sender_id)
                )
                AND (m.sender_id = ? OR m.receiver_id = ?)
                ORDER BY m.created_at DESC
                LIMIT 2
            ");
            $msgStmt->bind_param("iiiiii", $id, $id, $id, $id, $id, $id);
            $msgStmt->execute();
            $latestMessages = $msgStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $msgStmt->close();

            // Attach the other participant's display info for each preview row
            $quickMessages = [];
            foreach ($latestMessages as $lm) {
                $partnerStmt = $conn->prepare("SELECT firstname, lastname, agencyName, profile_image, role FROM users WHERE id = ?");
                $partnerStmt->bind_param("i", $lm['other_user_id']);
                $partnerStmt->execute();
                $partner = $partnerStmt->get_result()->fetch_assoc();
                $partnerStmt->close();
                if ($partner) {
                    $quickMessages[] = [
                        'last_message' => $lm['message'],
                        'firstname'    => $partner['firstname'],
                        'lastname'     => $partner['lastname'],
                        'agencyName'   => $partner['agencyName'],
                        'profile_image'=> $partner['profile_image'],
                        'role'         => $partner['role'],
                    ];
                }
            }

            // Following: latest 2 accounts this user follows
            $cityLabels = [
                'marrakech'  => 'Marrakech',
                'casablanca' => 'Casablanca',
                'tangier'    => 'Tangier',
                'rabat'      => 'Rabat',
                'fès'        => 'Fès',
                'essaouira'  => 'Essaouira',
            ];
            $followingStmt = $conn->prepare("
                SELECT u.id, u.firstname, u.lastname, u.agencyName, u.city, u.profile_image,
                    (SELECT COUNT(*) FROM properties p WHERE p.user_id = u.id) AS listings_count
                FROM following f
                JOIN users u ON u.id = f.following_id
                WHERE f.follower_id = ?
                ORDER BY f.created_at DESC
                LIMIT 2
            ");
            $followingStmt->bind_param("i", $id);
            $followingStmt->execute();
            $followingPreview = $followingStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $followingStmt->close();
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
                <a class="dashboard-signout" href="logout.php" data-logout><span data-i18n="dash.signout">Sign
                        out</span> →</a>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-head">
                <div>
                    <h1 data-i18n="dash.overview">Overview</h1>
                    <p data-i18n="dash.overview.subtitle">Manage your listings, conversations and social activity.</p>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <a class="btn btn-ghost" href="02-properties.php" data-i18n="dash.browsemarket">Browse Market</a>
                    <a class="btn btn-primary" href="add-property.php" data-i18n="dash.addproperty.plus">+ Add
                        Property</a>
                </div>
            </header>

            <div
                style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:26px">
                <div class="content-card">
                    <div style="font-size:12px;letter-spacing:.12em;color:#666;text-transform:uppercase"
                        data-i18n="dash.stats.active_listings">Active
                        Listings
                    </div>
                    <div id="stat-listings" style="font-family:Fraunces,serif;font-size:34px;margin-top:8px">
                        <?php echo $activeListingsCount; ?></div>
                    <a href="my-properties.php"
                        style="font-size:12px;color:#C72C41;margin-top:8px;display:inline-block"><span
                            data-i18n="dash.manage">Manage</span>
                        →</a>
                </div>
                <div class="content-card">
                    <div style="font-size:12px;letter-spacing:.12em;color:#666;text-transform:uppercase"
                        data-i18n="dash.favorites">Favorites
                    </div>
                    <div id="stat-favorites" style="font-family:Fraunces,serif;font-size:34px;margin-top:8px">0
                    </div>
                    <a href="favorites.php"
                        style="font-size:12px;color:#C72C41;margin-top:8px;display:inline-block"><span
                            data-i18n="dash.view">View</span>
                        →</a>
                </div>
                <div class="content-card">
                    <div style="font-size:12px;letter-spacing:.12em;color:#666;text-transform:uppercase"
                        data-i18n="dash.messages">Messages
                    </div>
                    <div id="stat-notifications" style="font-family:Fraunces,serif;font-size:34px;margin-top:8px">0
                    </div>
                    <a href="messages.php"
                        style="font-size:12px;color:#C72C41;margin-top:8px;display:inline-block"><span
                            data-i18n="dash.open">Open</span>
                        →</a>
                </div>
                <div class="content-card">
                    <div style="font-size:12px;letter-spacing:.12em;color:#666;text-transform:uppercase"
                        data-i18n="dash.followers">Followers
                    </div>
                    <div id="stat-followers" style="font-family:Fraunces,serif;font-size:34px;margin-top:8px">
                        <?php echo $followersCount; ?>
                    </div>
                    <a href="following.php"
                        style="font-size:12px;color:#C72C41;margin-top:8px;display:inline-block"><span
                            data-i18n="dash.view">View</span>
                        →</a>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:24px">
                <section>
                    <div class="content-card" style="margin-bottom:18px">
                        <h3 style="font-family:Fraunces,serif;font-size:24px;margin:0 0 6px"
                            data-i18n="dash.quickactions">Quick Actions</h3>
                        <p style="margin:0 0 18px;color:#666" data-i18n="dash.quickactions.subtitle">Jump back into the
                            most common tasks.</p>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
                            <a class="btn btn-primary" href="add-property.php" style="text-align:center"
                                data-i18n="dash.addproperty.plus2">+ Add
                                Property</a>
                            <a class="btn btn-ghost" href="messages.php" style="text-align:center"
                                data-i18n="dash.openmessages">Open Messages</a>
                            <a class="btn btn-ghost" href="favorites.php" style="text-align:center"
                                data-i18n="dash.viewfavorites">View
                                Favorites</a>
                        </div>
                    </div>

                    <div class="content-card">
                        <h3 style="font-family:Fraunces,serif;font-size:22px;margin:0 0 14px"
                            data-i18n="dash.latestnotifications">Latest Notifications
                        </h3>
                        <ul id="notif-list" class="notif-mini-list"></ul>
                        <a href="notifications.php"
                            style="display:inline-block;margin-top:14px;color:#C72C41;font-size:13px;font-weight:600"><span
                                data-i18n="dash.viewall">View
                                all</span> →</a>
                    </div>
                </section>

                <aside>
                    <div class="content-card" style="margin-bottom:18px">
                        <h3 style="font-family:Fraunces,serif;font-size:20px;margin:0 0 12px"
                            data-i18n="dash.quickmessages">Quick Messages</h3>
                        <?php if (empty($quickMessages)): ?>
                        <p style="color:#888;font-size:13px;margin:0">No messages yet.</p>
                        <?php else: foreach ($quickMessages as $m):
                            $mName = ($m['role'] === 'agency' && $m['agencyName']) ? $m['agencyName'] : trim($m['firstname'] . ' ' . $m['lastname']);
                            $mAvatar = $m['profile_image'] ?: $defaultAvatar;
                            $mPreview = $m['last_message'] ? mb_strimwidth($m['last_message'], 0, 60, '…') : '';
                        ?>
                        <a href="messages.php" class="msg-mini">
                            <img src="<?php echo htmlspecialchars($mAvatar); ?>" alt="" />
                            <div>
                                <strong><?php echo htmlspecialchars($mName); ?></strong><span><?php echo htmlspecialchars($mPreview); ?></span>
                            </div>
                        </a>
                        <?php endforeach; endif; ?>
                    </div>

                    <div class="content-card">
                        <h3 style="font-family:Fraunces,serif;font-size:20px;margin:0 0 12px"
                            data-i18n="dash.following">Following</h3>
                        <?php if (empty($followingPreview)): ?>
                        <p style="color:#888;font-size:13px;margin:0">Not following anyone yet.</p>
                        <?php else: foreach ($followingPreview as $f):
                            $fName = trim((string) $f['agencyName']);
                            if ($fName === '') { $fName = trim($f['firstname'] . ' ' . $f['lastname']); }
                            $fCityRaw = (string) ($f['city'] ?? '');
                            $fCity = $cityLabels[$fCityRaw] ?? ($fCityRaw !== '' ? ucfirst($fCityRaw) : '');
                            $fAvatar = $f['profile_image'] ?: $defaultAvatar;
                        ?>
                        <a href="following.php" class="follow-mini">
                            <img src="<?php echo htmlspecialchars($fAvatar); ?>" alt="" />
                            <div><strong><?php echo htmlspecialchars($fName); ?></strong><span><?php echo htmlspecialchars($fCity); ?>
                                    · <?php echo (int) $f['listings_count']; ?> <span
                                        data-i18n="unit.listings">listings</span></span></div>
                        </a>
                        <?php endforeach; endif; ?>
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
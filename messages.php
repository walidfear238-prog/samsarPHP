<?php
session_start();
require "db/connect.php";
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}


$id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT firstname, role, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Messages</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/dashboard-shell.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
    <script>
    window.currentUserId = <?php echo (int) $id; ?>;
    window.SAMSAR_BASE =
        <?php echo json_encode(rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/')); ?>;
    </script>
</head>

<body>
    <div class="cursor"></div>
    <div class="cursor-dot"></div>

    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            <a class="dashboard-brand" href="index.php">
                <svg class="dashboard-brand-mark" viewBox="0 0 24 24" fill="currentColor">
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
                <a class="dashboard-link active" href="messages.php"><span class="ico">✉</span>Messages <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span>Favorites <em
                        class="dashboard-badge grey" id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span>Following</a>
                <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span>Notifications <em
                        class="dashboard-badge red" id="bdg-notif-2">0</em></a>
            </nav>
            <div class="dashboard-side-foot">
                <div class="dashboard-user">
                    <?php
                    echo "<img src='" . htmlspecialchars($user['profile_image']) . "' alt='profile picture'/>";
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
                    <h1>Messages</h1>
                    <p>Your conversations with buyers, sellers and agencies.</p>
                </div>
            </header>

            <div class="chat-layout">
                <aside class="chat-list" id="chat-list"></aside>
                <section class="chat-window" id="chat-window">
                    <div class="chat-empty">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                        </svg>
                        <h3>Select a conversation</h3>
                        <p>Choose a conversation from the left to start chatting.</p>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <style>
    .chat-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 18px;
        height: calc(100vh - 220px);
        min-height: 520px
    }

    .chat-list {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 16px;
        overflow-y: auto;
        padding: 10px
    }

    .chat-item {
        display: flex;
        gap: 12px;
        padding: 14px;
        border-radius: 12px;
        cursor: pointer;
        transition: background .2s
    }

    .chat-item:hover {
        background: #fafafa
    }

    .chat-item.active {
        background: #f4f4f2
    }

    .chat-item img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0
    }

    .chat-item-info {
        flex: 1;
        min-width: 0
    }

    .chat-item-info strong {
        display: block;
        font-size: 14px;
        margin-bottom: 2px
    }

    .chat-item-info p {
        margin: 0;
        font-size: 13px;
        color: #888;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }

    .chat-item.unread strong::after {
        content: "•";
        color: #C72C41;
        margin-left: 6px
    }

    .chat-item-meta {
        text-align: right;
        font-size: 11px;
        color: #888
    }

    .chat-window {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        overflow: hidden
    }

    .chat-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #999;
        text-align: center;
        padding: 20px
    }

    .chat-empty h3 {
        font-family: Fraunces, serif;
        margin: 14px 0 6px;
        color: #1A1A1A
    }

    .chat-empty p {
        margin: 0;
        font-size: 14px
    }

    .chat-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        background: #fff
    }

    .chat-head img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover
    }

    .chat-head strong {
        display: block;
        font-size: 15px
    }

    .chat-head span {
        font-size: 12px;
        color: #888
    }

    .chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #fafafa;
        display: flex;
        flex-direction: column;
        gap: 8px
    }

    .bubble {
        max-width: 70%;
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 14px;
        line-height: 1.4;
        animation: pop .3s var(--ease)
    }

    .bubble.them {
        background: #fff;
        color: #1A1A1A;
        border-bottom-left-radius: 4px;
        align-self: flex-start
    }

    .bubble.me {
        background: #1A1A1A;
        color: #fff;
        border-bottom-right-radius: 4px;
        align-self: flex-end
    }

    .bubble small {
        display: block;
        font-size: 10px;
        opacity: .6;
        margin-top: 4px
    }

    @keyframes pop {
        from {
            opacity: 0;
            transform: translateY(6px) scale(.97)
        }
    }

    .chat-form {
        display: flex;
        gap: 10px;
        padding: 14px;
        border-top: 1px solid #f0f0f0;
        background: #fff
    }

    .chat-form input {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 999px;
        font-size: 14px;
        background: #fff
    }

    .chat-form input:focus {
        outline: none;
        border-color: #C72C41
    }

    .chat-form button {
        padding: 12px 22px;
        background: #C72C41;
        color: #fff;
        border: none;
        border-radius: 999px;
        font-weight: 600;
        cursor: pointer
    }

    .chat-form button:hover {
        background: #A50034
    }
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>
    <script src="scripts/chat.js"></script>
</body>

</html>
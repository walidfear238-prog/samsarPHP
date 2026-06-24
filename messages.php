<?php
session_start();
require "db/connect.php";
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}

// Get current user ID
$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $conn->prepare("SELECT firstname, role, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
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
                <a class="dashboard-link active" href="messages.php"><span class="ico">✉</span>Messages <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span>Favorites <em
                        class="dashboard-badge red" id="bdg-fav">0</em></a>
                <a class="dashboard-link" href="following.php"><span class="ico">࿄</span>Following</a>
                <a class="dashboard-link" href="notifications.php"><span class="ico">⌖</span>Notifications <em
                        class="dashboard-badge red" id="bdg-notif-2">0</em></a>
            </nav>

            <div class="dashboard-side-foot">
                <div class="dashboard-user">
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="profile picture" />
                    <div>
                        <strong><?php echo htmlspecialchars($user['firstname']); ?></strong>
                        <span><?php echo htmlspecialchars($user['role']); ?></span>
                    </div>
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
                <aside class="chat-list" id="chat-list">
                    <div class="loading">Loading conversations...</div>
                </aside>
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
        transition: background .2s;
        position: relative;
        align-items: center;
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

    .chat-item .unread-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #C72C41;
        color: #fff;
        border-radius: 50%;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: bold;
        min-width: 20px;
        text-align: center;
    }

    .chat-item-meta {
        text-align: right;
        font-size: 11px;
        color: #888;
        margin-left: auto;
        min-width: 40px;
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
        animation: pop .3s ease
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

        to {
            opacity: 1;
            transform: translateY(0) scale(1)
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
        background: #fff;
        outline: none;
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

    .chat-form button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .loading {
        text-align: center;
        padding: 20px;
        color: #999;
    }
    </style>

    <!-- Load external scripts FIRST (these control the cursor) -->
    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>

    <!-- Pass user data to JavaScript -->
    <script>
    window.currentUserId = <?php echo json_encode($user_id); ?>;
    window.userName = <?php echo json_encode($user['firstname']); ?>;
    </script>

    <!-- Chat functionality - wrapped in try/catch to prevent errors from breaking other JS -->
    <script>
    (function() {
        'use strict';

        try {
            // Main chat functionality
            const list = document.getElementById('chat-list');
            const win = document.getElementById('chat-window');
            let activeId = null;
            let pollingInterval = null;
            const currentUserId = window.currentUserId || 0;
            const API_BASE = '/api/chat/';

            // Format timestamp
            function formatTime(timestamp) {
                if (!timestamp) return '';
                try {
                    const date = new Date(timestamp);
                    return date.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });
                } catch (e) {
                    return '';
                }
            }

            // Get user initials for avatar fallback
            function getInitials(name) {
                if (!name) return '?';
                const parts = name.split(' ');
                if (parts.length >= 2) {
                    return (parts[0][0] || '') + (parts[1][0] || '');
                }
                return name.substring(0, 2).toUpperCase();
            }

            // Fetch all users for chat list
            async function fetchUsers() {
                try {
                    const response = await fetch(API_BASE + 'get_users.php');
                    const data = await response.json();

                    if (data.success) {
                        renderUserList(data.data);
                        // Update badge count
                        const totalUnread = data.data.reduce((sum, user) => sum + (user.unread_count || 0), 0);
                        const badge = document.getElementById('bdg-msg');
                        if (badge) badge.textContent = totalUnread;
                        return data.data;
                    } else {
                        console.error('Failed to fetch users:', data.message);
                        if (list) list.innerHTML = '<div class="loading">Error loading users</div>';
                        return [];
                    }
                } catch (error) {
                    console.error('Error fetching users:', error);
                    if (list) list.innerHTML = '<div class="loading">Error connecting to server</div>';
                    return [];
                }
            }

            // Render user list
            function renderUserList(users) {
                if (!list) return;

                if (!users || users.length === 0) {
                    list.innerHTML = `
                        <div style="text-align: center; padding: 40px 20px; color: #999;">
                            <p>No users found to chat with</p>
                        </div>
                    `;
                    return;
                }

                list.innerHTML = users.map(function(user) {
                    const hasUnread = user.unread_count > 0;
                    const lastMsg = user.last_message || 'No messages yet';
                    const lastTime = user.last_message_time ? formatTime(user.last_message_time) : '';
                    const avatar = user.profile_image || '';
                    const initials = getInitials((user.firstname || '') + ' ' + (user.lastname || ''));
                    const fullName = (user.firstname || '') + ' ' + (user.lastname || '');

                    return `
                        <div class="chat-item ${hasUnread ? 'unread' : ''} ${activeId === user.id ? 'active' : ''}" 
                             data-id="${user.id}"
                             data-name="${fullName}"
                             data-avatar="${avatar}">
                            ${avatar ? `<img src="${avatar}" alt="${fullName}" />` : `<div style="width:44px;height:44px;border-radius:50%;background:#C72C41;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:16px;flex-shrink:0;">${initials}</div>`}
                            <div class="chat-item-info">
                                <strong>${fullName}</strong>
                                <p>${lastMsg}</p>
                            </div>
                            <div class="chat-item-meta">
                                ${lastTime}
                                ${hasUnread ? `<div class="unread-badge">${user.unread_count}</div>` : ''}
                            </div>
                        </div>
                    `;
                }).join('');

                // Add click event listeners
                list.querySelectorAll('.chat-item').forEach(function(el) {
                    el.addEventListener('click', function() {
                        const userId = parseInt(this.dataset.id);
                        const userName = this.dataset.name;
                        const userAvatar = this.dataset.avatar;
                        activeId = userId;
                        openChat(userId, userName, userAvatar);
                        // Update active state
                        list.querySelectorAll('.chat-item').forEach(function(item) {
                            item.classList.remove('active');
                        });
                        this.classList.add('active');
                        // Remove unread badge
                        const badge = this.querySelector('.unread-badge');
                        if (badge) badge.remove();
                        // Update badge count
                        const totalUnread = document.querySelectorAll('.unread-badge').length;
                        const badgeEl = document.getElementById('bdg-msg');
                        if (badgeEl) badgeEl.textContent = totalUnread;
                    });
                });
            }

            // Fetch conversation messages
            async function fetchMessages(userId, limit, offset) {
                limit = limit || 50;
                offset = offset || 0;
                try {
                    const response = await fetch(API_BASE + 'get_conversation.php?user_id=' + userId +
                        '&limit=' + limit + '&offset=' + offset);
                    const data = await response.json();

                    if (data.success) {
                        // Mark messages as read automatically
                        markMessagesAsRead(userId);
                        return data.data.map(function(msg) {
                            return {
                                id: msg.id,
                                text: msg.message,
                                ts: formatTime(msg.created_at),
                                me: msg.sender_id == currentUserId,
                                sender_id: msg.sender_id,
                                is_read: msg.is_read
                            };
                        });
                    }
                    return [];
                } catch (error) {
                    console.error('Error fetching messages:', error);
                    return [];
                }
            }

            // Send a message
            async function sendMessage(receiverId, message) {
                try {
                    const response = await fetch(API_BASE + 'send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            receiver_id: receiverId,
                            message: message
                        })
                    });

                    const data = await response.json();
                    return data.success;
                } catch (error) {
                    console.error('Error sending message:', error);
                    return false;
                }
            }

            // Mark messages as read
            async function markMessagesAsRead(senderId) {
                try {
                    const response = await fetch(API_BASE + 'mark_messages_read.php', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            sender_id: senderId
                        })
                    });

                    const data = await response.json();
                    return data.success;
                } catch (error) {
                    console.error('Error marking messages as read:', error);
                    return false;
                }
            }

            // Open chat window
            function openChat(userId, userName, userAvatar) {
                if (!win) return;

                win.innerHTML = `
                    <div style="display: flex; flex-direction: column; height: 100%;">
                        <div class="chat-head">
                            ${userAvatar ? `<img src="${userAvatar}" alt="${userName}" />` : `<div style="width:42px;height:42px;border-radius:50%;background:#C72C41;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:16px;flex-shrink:0;">${getInitials(userName)}</div>`}
                            <div>
                                <strong>${userName}</strong>
                                <span>Online</span>
                            </div>
                        </div>
                        <div class="chat-body" id="chat-body">
                            <div class="loading">Loading messages...</div>
                        </div>
                        <form class="chat-form" id="chat-form">
                            <input type="text" id="chat-input" placeholder="Type a message…" autocomplete="off" required />
                            <button type="submit" id="send-btn">Send</button>
                        </form>
                    </div>
                `;

                // Load messages
                loadMessages(userId);

                // Handle form submission
                const form = document.getElementById('chat-form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const input = document.getElementById('chat-input');
                        const btn = document.getElementById('send-btn');
                        const text = input.value.trim();
                        if (!text || !activeId) return;

                        // Disable input while sending
                        input.disabled = true;
                        btn.disabled = true;
                        btn.textContent = 'Sending...';

                        sendMessage(activeId, text).then(function(sent) {
                            if (sent) {
                                input.value = '';
                                // Reload messages
                                loadMessages(activeId);
                            } else {
                                alert('Failed to send message. Please try again.');
                            }

                            input.disabled = false;
                            btn.disabled = false;
                            btn.textContent = 'Send';
                            input.focus();
                        });
                    });
                }

                // Enter key to send
                const input = document.getElementById('chat-input');
                if (input) {
                    input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            const form = document.getElementById('chat-form');
                            if (form) form.dispatchEvent(new Event('submit'));
                        }
                    });
                }
            }

            // Load messages into chat window
            async function loadMessages(userId) {
                const body = document.getElementById('chat-body');
                if (!body) return;

                body.innerHTML = '<div class="loading">Loading messages...</div>';

                const messages = await fetchMessages(userId);

                if (messages.length === 0) {
                    body.innerHTML = `
                        <div style="text-align: center; padding: 40px 0; color: #999;">
                            <p>No messages yet. Say hello! 👋</p>
                        </div>
                    `;
                } else {
                    body.innerHTML = messages.map(function(m) {
                        return `
                            <div class="bubble ${m.me ? 'me' : 'them'}">
                                ${m.text}
                                <small>${m.ts || ''}</small>
                            </div>
                        `;
                    }).join('');
                }

                // Scroll to bottom
                body.scrollTop = body.scrollHeight;
            }

            // Poll for new messages
            function startPolling() {
                if (pollingInterval) clearInterval(pollingInterval);

                pollingInterval = setInterval(function() {
                    // Refresh user list
                    fetchUsers().then(function(users) {
                        // If a chat is open, refresh messages
                        if (activeId) {
                            // Get user info from the list
                            var activeItem = list ? list.querySelector('.chat-item[data-id="' +
                                activeId + '"]') : null;
                            if (activeItem) {
                                var userName = activeItem.dataset.name;
                                var userAvatar = activeItem.dataset.avatar;
                                // Check if there are new messages
                                var userData = null;
                                if (users) {
                                    for (var i = 0; i < users.length; i++) {
                                        if (users[i].id === activeId) {
                                            userData = users[i];
                                            break;
                                        }
                                    }
                                }
                                if (userData && userData.unread_count > 0) {
                                    openChat(activeId, userName, userAvatar);
                                }
                            }
                        }
                    });
                }, 5000); // Poll every 5 seconds
            }

            // Stop polling
            function stopPolling() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            }

            // Initialize chat
            function init() {
                fetchUsers().then(function() {
                    startPolling();
                });

                // Clean up on page unload
                window.addEventListener('beforeunload', function() {
                    stopPolling();
                });
            }

            // Start the chat when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

        } catch (error) {
            console.error('Chat initialization error:', error);
        }

    })();
    </script>
</body>

</html>
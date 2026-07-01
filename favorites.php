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
    <title>SAMSAR · Favorites</title>
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
                <a class="dashboard-link active" href="favorites.php"><span class="ico">♡</span>Favorites <em
                        class="dashboard-badge red" id="bdg-fav">0</em></a>
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
                    <h1>Favorites</h1>
                    <p>Your saved properties.</p>
                </div>
            </header>
            <div id="fav-grid" class="fav-grid"></div>
        </main>
    </div>

    <style>
    .fav-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
    }

    .fav-card {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .fav-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.15);
    }

    .fav-card-media {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: #f5f5f5;
    }

    .fav-card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .fav-card:hover .fav-card-media img {
        transform: scale(1.05);
    }

    .fav-remove {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.6);
        border: none;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: white;
        font-size: 18px;
        transition: all 0.2s;
        z-index: 2;
    }

    .fav-remove:hover {
        background: #C72C41;
        transform: scale(1.1);
    }

    .fav-body {
        padding: 18px;
    }

    .fav-loc {
        font-size: 12px;
        color: #888;
        margin-bottom: 8px;
    }

    .fav-title {
        font-family: Fraunces, serif;
        font-size: 18px;
        margin: 0 0 12px 0;
    }

    .fav-title a {
        color: #1A1A1A;
        text-decoration: none;
    }

    .fav-title a:hover {
        color: #C72C41;
    }

    .fav-specs {
        display: flex;
        gap: 16px;
        font-size: 13px;
        color: #666;
        margin-bottom: 16px;
    }

    .fav-price {
        font-size: 20px;
        font-weight: 600;
        color: #C72C41;
        margin-bottom: 16px;
    }

    .fav-price small {
        font-size: 11px;
        font-weight: normal;
    }

    .fav-actions {
        display: flex;
        gap: 12px;
    }

    .fav-btn {
        flex: 1;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid #e5e5e5;
        background: #fff;
    }

    .fav-btn.view {
        color: #1A1A1A;
    }

    .fav-btn.view:hover {
        border-color: #C72C41;
        background: #C72C41;
        color: white;
    }

    .fav-btn.unfav {
        color: #C72C41;
        border-color: rgba(199, 44, 65, 0.3);
    }

    .fav-btn.unfav:hover {
        background: #C72C41;
        color: white;
        border-color: #C72C41;
    }

    .fav-empty {
        grid-column: 1/-1;
        text-align: center;
        padding: 60px 20px;
    }

    .loading-spinner {
        text-align: center;
        padding: 60px;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #C72C41;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>

    <script>
    (function() {
        const grid = document.getElementById('fav-grid');

        function formatPrice(price) {
            if (!price) return '0';
            return parseFloat(price).toLocaleString();
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        function showToast(message, isError = false) {
            const existing = document.querySelector('.toast-message');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = 'toast-message';
            toast.textContent = message;
            toast.style.cssText = `
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: ${isError ? '#dc3545' : '#28a745'};
                color: white;
                padding: 12px 24px;
                border-radius: 8px;
                font-size: 14px;
                z-index: 10000;
                animation: slideIn 0.3s ease;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }

        function loadFavorites() {
            grid.innerHTML =
                '<div class="loading-spinner"><div class="spinner"></div><p>Loading favorites...</p></div>';

            fetch('api/favorits/get-all-favorits.php')
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(properties => {
                    console.log('Fetched properties:', properties);

                    if (properties.error) {
                        grid.innerHTML =
                            `<div class="fav-empty"><h3>Error</h3><p>${properties.error}</p></div>`;
                        return;
                    }

                    if (properties && properties.length > 0) {
                        displayFavorites(properties);
                        document.getElementById('bdg-fav').textContent = properties.length;
                    } else {
                        showEmptyState();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    grid.innerHTML =
                        `<div class="fav-empty"><h3>Error Loading Favorites</h3><p>${error.message}</p></div>`;
                });
        }

        function displayFavorites(properties) {
            grid.innerHTML = properties.map((p, index) => {
                const imagePath = p.img ? `uploads/property_images/${p.img}` : '';
                return `
                    <div class="fav-card" style="animation: fadeInUp 0.4s ease ${index * 0.05}s forwards; opacity:0;">
                        <div class="fav-card-media">
                            <img src="${imagePath}" 
                                 alt="${escapeHtml(p.title)}"
                                 onerror="this.src='https://placehold.co/600x400/eef2f5/8ba3b0?text=No+Image'">
                            <button class="fav-remove" data-id="${p.id}">✕</button>
                        </div>
                        <div class="fav-body">
                            <div class="fav-loc">📍 ${escapeHtml(p.city)} · ${escapeHtml(p.property_type)}</div>
                            <h3 class="fav-title"><a href="03-property-details.php?id=${p.id}">${escapeHtml(p.title)}</a></h3>
                            <div class="fav-specs">
                                <span>${p.bedrooms || 0} bd</span>
                                <span>${p.bathrooms || 0} ba</span>
                                <span>${p.area || 0} m²</span>
                            </div>
                            <div class="fav-price">${formatPrice(p.price)} <small>MAD</small></div>
                            <div class="fav-actions">
                                <a href="03-property-details.php?id=${p.id}" class="fav-btn view">View Details →</a>
                                <button class="fav-btn unfav" data-id="${p.id}">Remove</button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            document.querySelectorAll('.fav-remove, .fav-btn.unfav').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const propertyId = this.getAttribute('data-id');
                    if (confirm('Remove this property from favorites?')) {
                        removeFromFavorites(propertyId, this.closest('.fav-card'));
                    }
                });
            });
        }

        function removeFromFavorites(propertyId, cardElement) {
            const formData = new FormData();
            formData.append('property_id', propertyId);

            fetch('api/favorits/remove-favorite.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cardElement.style.transition = 'all 0.3s ease';
                        cardElement.style.transform = 'translateX(100%)';
                        cardElement.style.opacity = '0';
                        setTimeout(() => {
                            cardElement.remove();
                            showToast('✓ Removed from favorites');
                            const remaining = document.querySelectorAll('.fav-card').length;
                            document.getElementById('bdg-fav').textContent = remaining;
                            if (remaining === 0) showEmptyState();
                        }, 300);
                    } else {
                        showToast(data.message || 'Error removing from favorites', true);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error removing from favorites', true);
                });
        }

        function showEmptyState() {
            grid.innerHTML = `
                <div class="fav-empty">
                    <h3>No favorites yet</h3>
                    <p>Start saving properties you love by clicking the heart icon.</p>
                    <a href="02-properties.php" class="fav-btn view" style="display:inline-block;width:auto;padding:12px 24px;">Browse Properties →</a>
                </div>
            `;
            document.getElementById('bdg-fav').textContent = '0';
        }

        loadFavorites();
    })();
    </script>
</body>

</html>
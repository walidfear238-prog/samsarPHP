<?php
session_start();
require "db/connect.php";
if (!isset($_SESSION['user_id'])) {
    header('location: index.php');
    exit;
}





//delete property function
function delete_property($conn, $user_id, $property_id, $property_images)
{
    $stmt = $conn->prepare("DELETE from properties where id=? and user_id=?");
    $stmt->bind_param("ii", $property_id, $user_id);
    return $stmt->exicute;
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · My Properties</title>
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
                <a class="dashboard-link active" href="my-properties.php"><span class="ico">▤</span>My Properties</a>
                <a class="dashboard-link" href="add-property.php"><span class="ico">+</span>Add Property</a>
                <div class="dashboard-group">SOCIAL</div>
                <a class="dashboard-link" href="messages.php"><span class="ico">✉</span>Messages <em
                        class="dashboard-badge red" id="bdg-msg">0</em></a>
                <a class="dashboard-link" href="favorites.php"><span class="ico">♡</span>Favorites <em
                        class="dashboard-badge grey" id="bdg-fav">0</em></a>
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
                    <h1>My Properties</h1>
                    <p>Manage your active and draft listings.</p>
                </div>
                <a class="btn btn-primary" href="add-property.php">+ Add Property</a>
            </header>

            <div class="content-card" style="padding:0;overflow:hidden">
                <table class="mp-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Beds / Baths</th>
                            <th>Area</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="mp-body"></tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Delete Modal -->
    <div class="modal-overlay" id="delete-modal">
        <div class="modal-box">
            <h3>Delete Property?</h3>
            <p>This listing will be permanently removed.</p>
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:24px">
                <button class="btn btn-ghost" id="cancel-delete">Cancel</button>
                <button class="btn btn-danger" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>

    <style>
    .mp-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px
    }

    .mp-table th {
        text-align: left;
        padding: 16px 20px;
        font-size: 11px;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #666;
        background: #fafafa;
        border-bottom: 1px solid #ececec
    }

    .mp-table td {
        padding: 18px 20px;
        border-bottom: 1px solid #f4f4f2;
        vertical-align: middle
    }

    .mp-table tr:last-child td {
        border: none
    }

    .mp-table tr:hover td {
        background: #fafafa
    }

    .mp-cell {
        display: flex;
        gap: 14px;
        align-items: center
    }

    .mp-cell img {
        width: 64px;
        height: 64px;
        border-radius: 8px;
        object-fit: cover
    }

    .mp-cell strong {
        display: block;
        font-family: Fraunces, serif;
        font-size: 16px
    }

    .mp-cell span {
        font-size: 11px;
        letter-spacing: .08em;
        color: #888;
        text-transform: uppercase
    }

    .mp-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        background: rgba(45, 125, 90, .1);
        color: #2D7D5A
    }

    .mp-status::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor
    }

    .mp-status.rented {
        background: rgba(217, 119, 6, .1);
        color: #D97706
    }

    .mp-status.sold {
        background: rgba(199, 44, 65, .1);
        color: #C72C41
    }

    .mp-actions {
        display: flex;
        gap: 6px;
        justify-content: flex-end
    }

    .mp-btn {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #ddd;
        background: #fff;
        color: #1A1A1A;
        transition: all .2s;
        cursor: pointer
    }

    .mp-btn:hover {
        border-color: #1A1A1A
    }

    .mp-btn.edit {
        border-color: #C72C41;
        color: #C72C41
    }

    .mp-btn.edit:hover {
        background: #C72C41;
        color: #fff
    }

    .mp-btn.del {
        border-color: transparent;
        color: #C72C41
    }

    .mp-btn.del:hover {
        background: rgba(199, 44, 65, .1)
    }

    .mp-empty {
        padding: 60px 20px;
        text-align: center;
        color: #888
    }

    .mp-empty h3 {
        font-family: Fraunces, serif;
        margin: 0 0 6px;
        color: #1A1A1A
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(20, 20, 20, .55);
        backdrop-filter: blur(6px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all .3s ease;
        z-index: 1000
    }

    .modal-overlay.is-open {
        opacity: 1;
        visibility: visible
    }

    .modal-box {
        background: #fff;
        padding: 32px;
        border-radius: 16px;
        width: min(420px, 90vw);
        transform: scale(.95);
        transition: transform .3s var(--ease)
    }

    .modal-overlay.is-open .modal-box {
        transform: scale(1)
    }

    .modal-box h3 {
        font-family: Fraunces, serif;
        font-size: 24px;
        margin: 0 0 8px
    }

    .modal-box p {
        margin: 0;
        color: #666
    }

    .btn-danger {
        background: #C72C41;
        color: #fff
    }

    .btn-danger:hover {
        background: #A50034
    }
    </style>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/dashboard-shell.js"></script>
    <script src="scripts/dashboard.js"></script>
    <script>
    (function() {
        const Store = window.SamsarStore;
        const body = document.getElementById('mp-body');
        const modal = document.getElementById('delete-modal');
        let pendingId = null;

        function statusClass(s) {
            if (s === 'rented') return 'rented';
            if (s === 'sold') return 'sold';
            return '';
        }

        function render() {
            const props = Store.get('properties', []);
            if (!props.length) {
                body.innerHTML =
                    '<tr><td colspan="6" class="mp-empty"><h3>No properties yet</h3><p>Start by adding your first listing.</p></td></tr>';
                return;
            }
            body.innerHTML = props.map(p => `
      <tr>
        <td>
          <div class="mp-cell">
            <img src="${p.img}" alt="${p.title}"/>
            <div>
              <strong>${p.title}</strong>
              <span>${p.city} · ${p.type}</span>
            </div>
          </div>
        </td>
        <td><span class="mp-status ${statusClass(p.status)}">${p.status}</span></td>
        <td><strong>${p.price}</strong></td>
        <td>${p.beds} bd / ${p.baths} ba</td>
        <td>${p.area} m²</td>
        <td>
          <div class="mp-actions">
            <a href="03-property-details.php" class="mp-btn">View</a>
            <a href="edit-property.php?id=${p.id}" class="mp-btn edit">Edit</a>
            <button class="mp-btn del" data-del="${p.id}">Delete</button>
          </div>
        </td>
      </tr>
    `).join('');

            body.querySelectorAll('[data-del]').forEach(btn => {
                btn.addEventListener('click', () => {
                    pendingId = parseInt(btn.dataset.del);
                    modal.classList.add('is-open');
                });
            });
        }

        document.getElementById('cancel-delete').addEventListener('click', () => modal.classList.remove('is-open'));
        modal.addEventListener('click', e => {
            if (e.target === modal) modal.classList.remove('is-open');
        });
        document.getElementById('confirm-delete').addEventListener('click', () => {
            if (pendingId) {
                const props = Store.get('properties', []).filter(p => p.id !== pendingId);
                Store.set('properties', props);
                render();
                if (window.SamsarApp) SamsarApp.paintOverview();
            }
            modal.classList.remove('is-open');
        });

        render();
    })();
    </script>
</body>

</html>
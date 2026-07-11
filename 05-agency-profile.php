<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Agency · SAMSAR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/05-agency-profile.css" />
</head>

<body>
    <div class="page-trans"><span></span><span></span><span></span></div>
    <div class="cursor"></div>
    <div class="cursor-dot"></div>
    <header class="nav">
        <div class="container nav-inner">
            <a href="index.php" class="brand"> <svg class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor"
                    aria-hidden="true">
                    <path
                        d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
                </svg><span class="brand-word">SAMSAR</span></a>
            <nav class="nav-links"><a href="02-properties.php">Properties</a><a href="04-agencies.php"
                    class="active">Agencies</a><a href="06-about.php">About</a><a href="07-contact.php">Contact</a>
            </nav>
            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<div class="nav-right">';
                echo '<a href="dashboard.php" style="font-size:14px;font-weight:500">Dashboard</a>';


                echo '<a href="logout.php" class="btn btn-secondary">Logout</a>';
                echo '
        </div>';
            } else {
                echo '            <div class="nav-right"><a href="08-login.php" class="nav-text">Sign in</a><a href="10-register-choose.php"
                    class="btn btn-primary">Join SAMSAR <span class="arrow">→</span></a></div>
        </div>';
            }


            ?>







    </header>

    <main>
        <div class="cover">
            <img src="https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=2000&q=85"
                alt="Marrakech rooftops" />
            <div class="cover-fade"></div>
        </div>

        <div class="container">
            <section class="profile-head reveal">
                <img class="prof-logo" id="prof-logo"
                    src=""
                    alt="Agency logo" />
                <div class="prof-info">
                    <span class="eyebrow" id="prof-eyebrow">Loading…</span>
                    <h1 id="prof-name">Loading…</h1>
                    <p id="prof-location">Loading…</p>
                </div>
                <div class="prof-actions">
                    <button class="btn btn-ghost follow-btn" data-follow>Follow</button>
                    <button class="btn btn-primary">Contact agency <span class="arrow">→</span></button>
                </div>
            </section>

            <section class="stat-row">
                <div class="stat reveal"><strong id="stat-listings">–</strong><span>Active listings</span></div>
                <div class="stat reveal" data-delay="60"><strong id="stat-years">–</strong><span>In the market</span></div>
                <div class="stat reveal" data-delay="120"><strong id="stat-rating">–</strong><span id="stat-reviews">0 reviews</span></div>
                <div class="stat reveal" data-delay="180"><strong id="stat-languages">–</strong><span>Languages</span></div>
            </section>

            <div class="layout">
                <section class="content">
                    <div class="tabs reveal">
                        <button class="tab active" data-tab="listings">Listings (<span id="tab-listings-count">0</span>)</button>
                        <button class="tab" data-tab="about">About</button>
                        <button class="tab" data-tab="reviews">Reviews (<span id="tab-reviews-count">0</span>)</button>
                    </div>

                    <div class="tab-panel active" data-panel="listings">
                        <div class="prop-grid" id="prop-grid"></div>
                    </div>
                    <div class="tab-panel" data-panel="about">
                        <h2 id="about-heading">About</h2>
                        <p id="about-text">Loading…</p>
                        <h3>Specialties</h3>
                        <ul class="spec" id="about-specialties"></ul>
                    </div>
                    <div class="tab-panel" data-panel="reviews">
                        <div id="reviews-list"></div>
                    </div>
                </section>

                <aside class="sidebar">
                    <div class="contact-card reveal">
                        <h3>Get in touch</h3>
                        <ul class="contact-info">
                            <li><span>Phone</span><a id="contact-phone" href="#">–</a></li>
                            <li><span>Email</span><a id="contact-email" href="#">–</a></li>
                            <li><span>Office</span><span id="contact-office">–</span></li>
                            <li><span>Hours</span><span id="contact-hours">–</span></li>
                        </ul>

                        <button class="btn btn-primary" style="width:100%;justify-content:center;margin-top:14px">Send a
                            message
                            <span class="arrow">→</span></button>
                    </div>
                </aside>
            </div>
        </div>
    </main>
    <footer class="footer">
        <div class="container footer-inner"><span>© 2026 SAMSAR · Casablanca, Morocco</span></div>
    </footer>
    <script src="scripts/05-agency-profile.js"></script>
</body>

</html>
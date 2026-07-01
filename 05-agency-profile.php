<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Atlas Real Estate · SAMSAR</title>
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
                <img class="prof-logo"
                    src="https://images.unsplash.com/photo-1572021335469-31706a17aaef?auto=format&fit=crop&w=200&q=80"
                    alt="Atlas Real Estate logo" />
                <div class="prof-info">
                    <span class="eyebrow">Verified samsar · Since 2008</span>
                    <h1>Atlas Real Estate</h1>
                    <p>Marrakech · Palmeraie · Ourika · Amizmiz</p>
                </div>
                <div class="prof-actions">
                    <button class="btn btn-ghost follow-btn" data-follow>Follow</button>
                    <button class="btn btn-primary">Contact agency <span class="arrow">→</span></button>
                </div>
            </section>

            <section class="stat-row">
                <div class="stat reveal"><strong>124</strong><span>Active listings</span></div>
                <div class="stat reveal" data-delay="60"><strong>18 yrs</strong><span>In the market</span></div>
                <div class="stat reveal" data-delay="120"><strong>4.9★</strong><span>312 reviews</span></div>
                <div class="stat reveal" data-delay="180"><strong>FR · EN · AR</strong><span>Languages</span></div>
            </section>

            <div class="layout">
                <section class="content">
                    <div class="tabs reveal">
                        <button class="tab active" data-tab="listings">Listings (124)</button>
                        <button class="tab" data-tab="about">About</button>
                        <button class="tab" data-tab="reviews">Reviews (312)</button>
                    </div>

                    <div class="tab-panel active" data-panel="listings">
                        <div class="prop-grid" id="prop-grid"></div>
                    </div>
                    <div class="tab-panel" data-panel="about">
                        <h2>About Atlas Real Estate</h2>
                        <p>Founded in 2008 by Karim El Idrissi, Atlas Real Estate is Marrakech's reference for luxury
                            riads and
                            villas in the Palmeraie, the Ourika Valley and the foothills of the Atlas. Our team of nine
                            bilingual
                            brokers has closed over 480 transactions, working with private buyers, hospitality investors
                            and a growing
                            number of remote-working families relocating to Marrakech.</p>
                        <p>We work exclusively on a transparent flat-fee basis and provide every client with a Moroccan
                            notary of
                            their choice — never a referral commission.</p>
                        <h3>Specialties</h3>
                        <ul class="spec">
                            <li>Luxury riads (medina)</li>
                            <li>Palmeraie villas</li>
                            <li>Atlas mountain residences</li>
                            <li>Hospitality / boutique hotel acquisitions</li>
                        </ul>
                    </div>
                    <div class="tab-panel" data-panel="reviews">
                        <div class="review">
                            <div class="rev-head"><strong>Élise M.</strong><span>★★★★★ · Bought a riad in 2025</span>
                            </div>
                            <p>"Karim and his team handled every step of our riad purchase — from the first viewing in
                                March to the
                                keys in November. Transparent, patient, and deeply knowledgeable about the medina."</p>
                        </div>
                        <div class="review">
                            <div class="rev-head"><strong>Yassine B.</strong><span>★★★★★ · Sold villa in 2024</span>
                            </div>
                            <p>"Listed Friday, six offers by Tuesday, sold at asking price within three weeks.
                                Professional
                                photography and exceptional buyer screening."</p>
                        </div>
                        <div class="review">
                            <div class="rev-head"><strong>Carla R.</strong><span>★★★★☆ · Long-term rental</span></div>
                            <p>"Helpful, responsive, and made the lease bilingual without us asking. Only small note:
                                viewings were
                                sometimes hard to schedule on short notice."</p>
                        </div>
                    </div>
                </section>

                <aside class="sidebar">
                    <div class="contact-card reveal">
                        <h3>Get in touch</h3>
                        <ul class="contact-info">
                            <li><span>Phone</span><a href="tel:+212524000000">+212 5 24 00 00 00</a></li>
                            <li><span>Email</span><a
                                    href="mailto:hello@atlas-realestate.ma">hello@atlas-realestate.ma</a></li>
                            <li><span>Office</span>15 Rue de la Liberté, Guéliz, Marrakech</li>
                            <li><span>Hours</span>Mon–Sat · 9:00–19:00</li>
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
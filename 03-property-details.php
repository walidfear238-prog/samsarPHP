<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-i18n-doctitle="propdetails.title">Property Details · SAMSAR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/03-property-details.css" />
    <link rel="stylesheet" href="css/rtl.css" />
    <script src="js/translations.js"></script>
    <script src="js/language-switcher.js"></script>
</head>

<body>
    <div class="cursor"></div>
    <div class="cursor-dot"></div>

    <header class="nav">
        <div class="container nav-inner">
            <a href="index.php" class="brand">
                <svg class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">
                    <path
                        d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
                </svg>
                <span class="brand-word">SAMSAR</span>
            </a>
            <nav class="nav-links">
                <a href="02-properties.php" data-i18n="nav.properties" class="active">Properties</a>
                <a href="04-agencies.php" data-i18n="nav.agencies">Agencies</a>
                <a href="06-about.php" data-i18n="nav.about">About</a>
                <a href="07-contact.php" data-i18n="nav.contact">Contact</a>
            </nav>

            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<div class="nav-right">';
                echo '<a href="dashboard.php" style="font-size:14px;font-weight:500"><span data-i18n="nav.dashboard">Dashboard</span></a>';
                echo '<a href="logout.php" class="btn btn-secondary"><span data-i18n="nav.logout">Logout</span></a>';
                echo '</div>';
            } else {
                echo '<div class="nav-right">';
                echo '<a href="08-login.php" class="nav-text"><span data-i18n="nav.signin">Sign in</span></a>';
                echo '<a href="10-register-choose.php" class="btn btn-primary"><span data-i18n="nav.join">Join SAMSAR</span> <span class="arrow">→</span></a>';
                echo '</div>';
            }
            ?>
        </div>
    </header>

    <main>
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="crumbs">
                <a href="index.php" data-i18n="breadcrumb.home">Home</a> /
                <a href="02-properties.php" data-i18n="nav.properties">Properties</a> /
                <span id="breadcrumb-title" data-i18n="common.loading">Loading...</span>
            </nav>

            <!-- Hero Section -->
            <section class="hero-prop">
                <div class="title-block">
                    <span class="eyebrow" id="property-location" data-i18n="propdetails.loading_location">Loading location...</span>
                    <h1 id="property-title" data-i18n="common.loading">Loading...</h1>
                    <p class="meta-line" id="property-meta" data-i18n="propdetails.loading_details">Loading property details...</p>
                </div>
                <div class="price-block">
                    <span class="price" id="property-price" data-i18n="common.loading">Loading...</span>
                    <div class="actions">
                        <button class="btn btn-ghost" id="detail-save-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                            </svg>
                            <span data-i18n="propdetails.save">Save</span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- Gallery -->
            <section class="gallery">
                <div class="g-main">
                    <img id="g-main-img" src="" alt="Property main image" />
                </div>
                <div class="g-thumbs" id="g-thumbs">
                    <!-- Thumbnails will be populated by JavaScript -->
                </div>
            </section>

            <!-- Layout -->
            <div class="layout">
                <section class="content">
                    <!-- Key Facts -->
                    <div class="key-facts">
                        <div><span data-i18n="propdetails.bedrooms">Bedrooms</span><strong id="fact-bedrooms">-</strong></div>
                        <div><span data-i18n="propdetails.bathrooms">Bathrooms</span><strong id="fact-bathrooms">-</strong></div>
                        <div><span data-i18n="propdetails.livingarea">Living area</span><strong id="fact-area">-</strong></div>
                        <div><span data-i18n="propdetails.propertytype">Property Type</span><strong id="fact-type">-</strong></div>
                        <div><span data-i18n="propdetails.status">Status</span><strong id="fact-status">-</strong></div>
                        <div><span data-i18n="propdetails.listed">Listed</span><strong id="fact-date">-</strong></div>
                    </div>

                    <!-- Description -->
                    <section class="block">
                        <h2 data-i18n="propdetails.description">Description</h2>
                        <div id="property-description">
                            <p data-i18n="propdetails.loading_description">Loading description...</p>
                        </div>
                    </section>

                    <!-- Features -->
                    <section class="block">
                        <h2 data-i18n="propdetails.features">Features</h2>
                        <ul class="features" id="property-features">
                            <li data-i18n="propdetails.loading_features">Loading features...</li>
                        </ul>
                    </section>
                </section>

                <aside class="sidebar">
                    <div class="agency-card">
                        <div class="agency-head">
                            <img id="agent-avatar" src="" alt="Agent" />
                            <div>
                                <strong id="agent-name" data-i18n="common.loading">Loading...</strong>
                                <span id="agent-location" data-i18n="propdetails.listing_agency">Listing agency</span>
                            </div>
                            <button data-follow class="btn-follow" data-i18n="propdetails.follow">Follow</button>
                        </div>
                        <p class="agency-bio" id="agent-bio" data-i18n="propdetails.loading_agent">Loading agent information...</p>
                        <form class="agency-form" id="contact-form">
                            <div class="field">
                                <label data-i18n="propdetails.fullname">Full Name</label>
                                <input type="text" required placeholder="Your name" data-i18n-placeholder="propdetails.fullname.placeholder" />
                            </div>
                            <div class="field">
                                <label data-i18n="propdetails.message">Message</label>
                                <textarea rows="4" placeholder="I am interested in this property." data-i18n-placeholder="propdetails.message.placeholder"></textarea>
                            </div>
                            <div class="contact-buttons">
                                <button class="btn btn-primary" type="submit" data-i18n="propdetails.sendmessage">Send Message</button>
                                <a href="#" class="btn btn-whatsapp" id="whatsapp-link" data-i18n="propdetails.whatsapp">WhatsApp</a>
                            </div>
                            <div class="booking-section">
                                <button type="button" class="btn btn-ghost full" id="book-visit-btn" data-i18n="propdetails.bookvisit">Book a
                                    Visit</button>
                            </div>
                        </form>
                        <a href="#" class="view-agency" id="agency-profile-link"><span data-i18n="propdetails.viewagencyprofile">View agency profile</span> →</a>
                    </div>
                </aside>
            </div>

            <!-- Similar Properties -->
            <section class="similar">
                <h2 data-i18n="propdetails.similar">Similar properties</h2>
                <div class="sim-grid" id="similar-properties">
                    <!-- Similar properties will be populated by JavaScript -->
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <span>© 2026 SAMSAR · Casablanca, Morocco</span>
        </div>
    </footer>


    <script src="scripts/03-property-details.js"></script>
    <script>
    console.log('Page loaded, checking for external JS...');
    var scripts = document.querySelectorAll('script');
    console.log('Number of script tags:', scripts.length);
    for (var i = 0; i < scripts.length; i++) {
        console.log('Script ' + i + ':', scripts[i].src || 'inline');
    }
    </script>
</body>

</html>
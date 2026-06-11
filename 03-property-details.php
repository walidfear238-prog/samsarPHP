<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Villa Tazri · SAMSAR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/03-property-details.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>
    <div class="page-trans"><span></span><span></span><span></span></div>
    <div class="cursor"></div>
    <div class="cursor-dot"></div>

    <header class="nav">
        <div class="container nav-inner">
            <a href="index.php" class="brand"><svg class="brand-mark" viewBox="0 0 100 100">
                    <path
                        d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z M38 38 L62 38 L62 50 L38 50 Z M38 60 L62 60 L62 72 L38 72 Z"
                        fill-rule="evenodd" />
                </svg><span class="brand-word">SAMSAR</span></a>
            <nav class="nav-links"><a href="02-properties.php" class="active">Properties</a><a
                    href="04-agencies.php">Agencies</a><a href="06-about.php">About</a><a
                    href="07-contact.php">Contact</a>
            </nav>

            <?php
            if(isset($_SESSION['user_id'])){
                     echo '<div class="nav-right">';
        echo '<a href="dashboard.php" style="font-size:14px;font-weight:500">Dashboard</a>';


        echo '<a href="logout.php" class="btn btn-secondary">Logout</a>';
        echo '
        </div>';
            }else{
              echo'            <div class="nav-right"><a href="08-login.php" class="nav-text">Sign in</a><a href="10-register-choose.php"
                    class="btn btn-primary">Join SAMSAR <span class="arrow">→</span></a>
            </div>';
            }
            
            
            
            ?>



        </div>
    </header>

    <main>
        <div class="container">
            <nav class="crumbs reveal"><a href="index.php">Home</a> / <a href="02-properties.php">Properties</a> /
                <span>Villa Tazri</span>
            </nav>

            <section class="hero-prop">
                <div class="title-block reveal">
                    <span class="eyebrow">Marrakech · Palmeraie</span>
                    <h1>Villa Tazri</h1>
                    <p class="meta-line">5 bedrooms · 6 bathrooms · 620 m² · Private pool · Atlas views</p>
                </div>
                <div class="price-block reveal" data-delay="80">
                    <span class="price">12,400,000 <small>MAD</small></span>
                    <div class="actions">
                        <button class="btn btn-ghost"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8">
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                            </svg> Save</button>

                    </div>
                </div>
            </section>

            <section class="gallery reveal" data-delay="120">
                <div class="g-main"><img id="g-main-img"
                        src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=1600&q=85"
                        alt="Villa Tazri main" /></div>
                <div class="g-thumbs">
                    <button class="g-thumb active"><img
                            src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=600&q=80"
                            alt="" /></button>
                    <button class="g-thumb"><img
                            src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=600&q=80"
                            alt="" /></button>
                    <button class="g-thumb"><img
                            src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=600&q=80"
                            alt="" /></button>
                    <button class="g-thumb"><img
                            src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=600&q=80"
                            alt="" /></button>
                    <button class="g-thumb"><img
                            src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=600&q=80"
                            alt="" /></button>
                </div>
            </section>

            <div class="layout">
                <section class="content">
                    <div class="key-facts reveal">
                        <div><span>Bedrooms</span><strong>5</strong></div>
                        <div><span>Bathrooms</span><strong>6</strong></div>
                        <div><span>Living area</span><strong>620 m²</strong></div>
                        <div><span>Land</span><strong>2,400 m²</strong></div>
                        <div><span>Year built</span><strong>2019</strong></div>
                        <div><span>Parking</span><strong>4 cars</strong></div>
                    </div>

                    <section class="block reveal">
                        <h2>Description</h2>
                        <p>Set on 2,400 m² of olive-grove land in the heart of Marrakech's Palmeraie, Villa Tazri pairs
                            the tactile
                            warmth of traditional tadelakt with the discipline of contemporary architecture. Five
                            en-suite bedrooms
                            open onto shaded courtyards. A 22-metre saltwater pool stretches toward an unbroken view of
                            the Atlas
                            Mountains.</p>
                        <p>The villa was completed in 2019 by Casablanca-based studio Tazri Architectes and has been
                            meticulously
                            maintained. It is sold fully furnished, with bespoke pieces from Moroccan ateliers in Fès
                            and Marrakech.
                        </p>
                    </section>

                    <section class="block reveal">
                        <h2>Features</h2>
                        <ul class="features">
                            <li>22m saltwater pool</li>
                            <li>Hammam & spa</li>
                            <li>Staff quarters</li>
                            <li>Solar heating</li>
                            <li>Underfloor heating</li>
                            <li>Smart home system</li>
                            <li>Olive grove (180 trees)</li>
                            <li>Outdoor kitchen</li>
                            <li>Cinema room</li>
                            <li>Wine cellar</li>
                            <li>Gym</li>
                            <li>Borehole well</li>
                        </ul>
                    </section>


                </section>

                <aside class="sidebar">
                    <div class="agency-card reveal">
                        <div class="agency-head">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=200&q=80"
                                alt="Agent" />
                            <div>
                                <strong>Atlas Real Estate</strong>
                                <span>Listing agency · Marrakech</span>
                            </div>
                            <button class="follow-btn" data-follow>Follow</button>
                        </div>
                        <p class="agency-bio">Specialist in luxury riads & villas across the Palmeraie and the Ourika
                            valley since
                            2008.</p>
                        <form class="agency-form" id="contact-form">
                            <div class="field"><label>Full Name</label><input type="text" required value=""
                                    placeholder="Your name" />
                            </div>
                            <div class="field"><label>Message</label><textarea
                                    rows="4">I am interested in this property.</textarea>
                            </div>

                            <div class="contact-buttons">
                                <button class="btn btn-primary" type="submit">Send Message</button>
                                <a href="https://wa.me/212600000000" class="btn btn-whatsapp">WhatsApp</a>
                            </div>

                            <div class="booking-section">
                                <button type="button" class="btn btn-ghost full" id="book-visit-btn">Book a
                                    Visit</button>
                            </div>
                        </form>
                        <a href="05-agency-profile.php" class="view-agency">View agency profile →</a>
                    </div>
                </aside>
            </div>

            <section class="similar">
                <h2 class="reveal">Similar properties</h2>
                <div class="sim-grid">
                    <a class="sim-card reveal" href="03-property-details.php">
                        <div class="sim-img"><img
                                src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=600&q=80"
                                alt="" /></div>
                        <h3>Villa Atlas</h3><span>Souissi · Rabat</span><strong>18,500,000 MAD</strong>
                    </a>
                    <a class="sim-card reveal" data-delay="80" href="03-property-details.php">
                        <div class="sim-img"><img
                                src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=600&q=80"
                                alt="" /></div>
                        <h3>Villa Ocean</h3><span>Essaouira Coast</span><strong>22,000,000 MAD</strong>
                    </a>
                    <a class="sim-card reveal" data-delay="160" href="03-property-details.php">
                        <div class="sim-img"><img
                                src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=600&q=80"
                                alt="" /></div>
                        <h3>Riad Yasmine</h3><span>Medina · Marrakech</span><strong>5,200,000 MAD</strong>
                    </a>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-inner"><span>© 2026 SAMSAR · Casablanca, Morocco</span></div>
    </footer>
    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/03-property-details.js"></script>
    <script>
    (function() {
        if (window.SamsarTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            var old = document.querySelector('.page-trans');
            if (old) old.remove();
            setTimeout(function() {
                SamsarTransition.play('liquid-wipe-right', 'slow')
            }, 50);
        }
    })();
    </script>
</body>

</html>
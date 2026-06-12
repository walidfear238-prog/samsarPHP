<?php
session_start();

require_once "db/connect.php";



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description"
        content="SAMSAR — Morocco's premium real estate platform. Riads, villas, and homes from Marrakech to Tangier, brokered with transparency." />
    <title>SAMSAR · Find your place in Morocco</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="styles/01-home.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
</head>

<body>

    <!-- Custom cursor -->
    <div class="cursor" aria-hidden="true"></div>
    <div class="cursor-dot" aria-hidden="true"></div>

    <!-- Navigation -->
    <header class="nav" role="banner">
        <div class="container nav-inner">
            <a href="#" class="brand" aria-label="SAMSAR home">
                <svg class="brand-mark" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true">
                    <!-- Stylised house-S mark (matches uploaded logo silhouette) -->
                    <path d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z
                   M38 38 L62 38 L62 50 L38 50 Z
                   M38 60 L62 60 L62 72 L38 72 Z" fill-rule="evenodd" />
                    <path d="M50 26 L66 40 L34 40 Z" fill="#fff" />
                </svg>
                <span class="brand-word">SAMSAR</span>
            </a>

            <nav class="nav-links" aria-label="Primary">
                <a href="02-properties.php">Properties</a>
                <a href="04-agencies.php">Agencies</a>
                <a href="06-about.php">About</a>
                <a href="07-contact.php">Contact</a>
            </nav>

            <?php
            if (isset($_SESSION["user_id"])) {
                // User is logged in, show dashboard link and logout option
                echo '<div class="nav-right">';
                echo '<a href="dashboard.php" style="font-size:14px;font-weight:500">Dashboard</a>';


                echo '<a href="logout.php" class="btn btn-secondary">Logout</a>';
                echo '
        </div>';
            } else {
                // User is not logged in, show sign in and register options
                echo ' <div class="nav-right">
            <a href="08-login.php" style="font-size:14px;font-weight:500">Sign in</a>
            <a href="09-register.php" class="btn btn-primary">
                Join SAMSAR
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <button class="nav-toggle" aria-label="Open menu"><span></span></button>
        </div>';
            }
            ?>



        </div>
    </header>

    <main>

        <!-- HERO — Cinematic Full Screen -->
        <section class="hero-cinematic">
            <div class="hero-bg" data-parallax="0.2">
                <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=2400&q=85"
                    alt="Luxury Moroccan villa" loading="eager" />
            </div>



            <?php


            if (isset($_SESSION["user_id"])) {
                echo '    <div class="container hero-content">
                <h1 class="hero-title" data-scroll-hide>One click away from your <em>home.</em></h1>
                 </div> ';
            } else {

                echo '            <div class="container hero-content">
                <h1 class="hero-title" data-scroll-hide>One click away from your <em>dream.</em></h1>
                <a href="09-register.php" class="btn btn-primary hero-cta" data-scroll-hide>Join us</a>
            </div>';

            }
            ?>






        </section>

        <!-- ACTION LIST -->
        <section class="actions" id="buy">
            <div class="container">
                <div class="actions-header reveal">
                    <h2>Three ways<br />to move with SAMSAR.</h2>
                    <p>Whether you're settling in, cashing out, or just exploring — start with a single tap. Every
                        path
                        is
                        brokered by a verified local samsar.</p>
                </div>

                <a class="action-row reveal" href="02-properties.php" data-cursor="hover">
                    <span class="arrow-in" aria-hidden="true">→</span>
                    <span class="label">Buy</span>
                    <span class="meta"><strong>1,284 homes</strong>Marrakech · Casablanca · Tangier</span>
                </a>
                <a class="action-row reveal" href="02-properties.php" data-cursor="hover" data-delay="80">
                    <span class="arrow-in" aria-hidden="true">→</span>
                    <span class="label">Rent</span>
                    <span class="meta"><strong>540 listings</strong>Long-term & seasonal riads</span>
                </a>
                <a class="action-row reveal" href="10-register-choose.php" data-cursor="hover" data-delay="160">
                    <span class="arrow-in" aria-hidden="true">→</span>
                    <span class="label">Sell</span>
                    <span class="meta"><strong>Free valuation</strong>Listed in under 48 hours</span>
                </a>
            </div>
        </section>

        <!-- FEED & LATEST -->
        <section class="featured" id="developments">
            <div class="container">
                <div class="featured-header reveal">
                    <h2>Latest in <em>your feed.</em></h2>
                    <p>New listings from agencies you follow.</p>
                </div>
                <div class="property-grid" id="feed-grid">
                    <!-- Statically filled for demo -->
                </div>

                <div class="featured-header reveal" style="margin-top: 80px;">
                    <h2>Featured <em>listings.</em></h2>
                    <a class="view-all" href="02-properties.php" data-cursor="hover">
                        View all 1,284 →
                    </a>
                </div>

                <div class="property-grid">

                    <article class="card reveal">
                        <div class="card-media">
                            <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=900&q=80"
                                alt="Villa with private pool, palm garden in Marrakech Palmeraie" loading="lazy" />
                            <span class="card-badge crimson">For Sale</span>
                            <button class="card-fav" aria-label="Save property">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8">
                                    <path
                                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                </svg>
                            </button>
                        </div>
                        <div class="card-body">
                            <span class="card-loc">Palmeraie · Marrakech</span>
                            <h3 class="card-title">Villa Tazri — Pool & Atlas views</h3>
                            <div class="card-specs">
                                <span>5 bd</span><span>6 ba</span><span>620 m²</span>
                            </div>
                            <div class="card-foot">
                                <span class="card-price">12,400,000 <small>MAD</small></span>
                                <a class="card-cta" href="03-property-details.php" data-cursor="hover">View Details
                                    <span class="arrow">→</span></a>
                            </div>
                        </div>
                    </article>

                    <article class="card reveal" data-delay="120">
                        <div class="card-media">
                            <img src="https://images.unsplash.com/photo-1542718610-a1d656d1884c?auto=format&fit=crop&w=900&q=80"
                                alt="Restored riad with zellige courtyard in Essaouira medina" loading="lazy" />
                            <span class="card-badge">For Rent</span>
                            <button class="card-fav" aria-label="Save property">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8">
                                    <path
                                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                </svg>
                            </button>
                        </div>
                        <div class="card-body">
                            <span class="card-loc">Medina · Essaouira</span>
                            <h3 class="card-title">Riad Souira — Restored 18th-century</h3>
                            <div class="card-specs">
                                <span>4 bd</span><span>4 ba</span><span>310 m²</span>
                            </div>
                            <div class="card-foot">
                                <span class="card-price">38,000 <small>MAD / mo</small></span>
                                <a class="card-cta" href="03-property-details.php" data-cursor="hover">View Details
                                    <span class="arrow">→</span></a>
                            </div>
                        </div>
                    </article>

                    <article class="card reveal" data-delay="240">
                        <div class="card-media">
                            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=80"
                                alt="Bright penthouse with terrace in Casablanca Anfa" loading="lazy" />
                            <span class="card-badge crimson">New</span>
                            <button class="card-fav" aria-label="Save property">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8">
                                    <path
                                        d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                </svg>
                            </button>
                        </div>
                        <div class="card-body">
                            <span class="card-loc">Anfa · Casablanca</span>
                            <h3 class="card-title">Penthouse Lumière — Ocean terrace</h3>
                            <div class="card-specs">
                                <span>3 bd</span><span>3 ba</span><span>240 m²</span>
                            </div>
                            <div class="card-foot">
                                <span class="card-price">7,950,000 <small>MAD</small></span>
                                <a class="card-cta" href="03-property-details.php" data-cursor="hover">View Details
                                    <span class="arrow">→</span></a>
                            </div>
                        </div>
                    </article>

                </div>
            </div>
        </section>

        <!-- WHY SAMSAR -->
        <section class="why" id="journal">
            <div class="container">
                <div class="why-grid">
                    <div class="why-intro reveal">
                        <span class="eyebrow">Why SAMSAR</span>
                        <h2>A modern broker,<br />rooted in <em>tradition.</em></h2>
                        <p>The samsar has always been the soul of Moroccan property — the one who knows the door
                            behind
                            the door.
                            We've kept the trust and added the technology.</p>
                    </div>
                    <div class="why-stats">
                        <div class="stat reveal">
                            <div class="num">100<em>%</em></div>
                            <h3>Verified listings</h3>
                            <p>Every title deed cross-checked with the Conservation Foncière before publication.</p>
                        </div>
                        <div class="stat reveal" data-delay="100">
                            <div class="num">14<em>+</em></div>
                            <h3>Local samsars</h3>
                            <p>A network of vetted brokers across Marrakech, Casablanca, Tangier, Fès and the coast.
                            </p>
                        </div>
                        <div class="stat reveal" data-delay="200">
                            <div class="num">0<em>%</em></div>
                            <h3>Hidden fees</h3>
                            <p>Flat 2.5% commission. Itemised in writing before you ever sign a thing.</p>
                        </div>
                        <div class="stat reveal" data-delay="300">
                            <div class="num">3<em>×</em></div>
                            <h3>Bilingual contracts</h3>
                            <p>Arabic, French and English — drafted by a Moroccan notary you choose.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CITIES -->
        <section class="cities">
            <div class="container">
                <div class="cities-head reveal">
                    <h2>Explore by city.</h2>
                    <div class="cities-controls" role="group" aria-label="Scroll cities">
                        <button data-cities-prev aria-label="Previous cities">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                        </button>
                        <button data-cities-next aria-label="Next cities">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <path d="M9 6l6 6-6 6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="cities-track">
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1597211833712-5e41faa202ea?auto=format&fit=crop&w=700&q=80"
                            alt="Blue alleys of Chefchaouen" loading="lazy" />
                        <div class="city-label">
                            <h3>Chefchaouen</h3><span>86 homes</span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=700&q=80"
                            alt="Marrakech medina rooftops" loading="lazy" />
                        <div class="city-label">
                            <h3>Marrakech</h3><span>412 homes</span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1538230575309-59fe2ecf5b59?auto=format&fit=crop&w=700&q=80"
                            alt="Fès tanneries and medina" loading="lazy" />
                        <div class="city-label">
                            <h3>Fès</h3><span>164 homes</span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1577147443647-81d0e4bfe4cc?auto=format&fit=crop&w=700&q=80"
                            alt="Tangier waterfront" loading="lazy" />
                        <div class="city-label">
                            <h3>Tangier</h3><span>238 homes</span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1570214476695-19bd467e6f7a?auto=format&fit=crop&w=700&q=80"
                            alt="Hassan Tower Rabat" loading="lazy" />
                        <div class="city-label">
                            <h3>Rabat</h3><span>197 homes</span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1528657249085-893be9ffd04f?auto=format&fit=crop&w=700&q=80"
                            alt="Essaouira fishing port and ramparts" loading="lazy" />
                        <div class="city-label">
                            <h3>Essaouira</h3><span>94 homes</span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1518730518541-d0843268c287?auto=format&fit=crop&w=700&q=80"
                            alt="Ouarzazate desert kasbah" loading="lazy" />
                        <div class="city-label">
                            <h3>Ouarzazate</h3><span>52 homes</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA BAND -->
        <section class="cta-band" id="list">
            <div class="container cta-inner">
                <h2>Selling your home?<br /><em>Let's get started.</em></h2>
                <a href="#" class="btn" data-open-modal data-cursor="hover">
                    Let's get started <span class="arrow" aria-hidden="true">→</span>
                </a>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="footer" role="contentinfo">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="brand">
                        <svg class="brand-mark" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true">
                            <path d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z
                       M38 38 L62 38 L62 50 L38 50 Z
                       M38 60 L62 60 L62 72 L38 72 Z" fill-rule="evenodd" />
                            <path d="M50 26 L66 40 L34 40 Z" fill="#1A1A1A" />
                        </svg>
                        <span class="brand-word">SAMSAR</span>
                    </a>
                    <p>The trusted Moroccan broker, reimagined. Properties brokered with transparency, from the
                        Atlas to
                        the
                        Atlantic.</p>
                </div>

                <div class="footer-col">
                    <h4>Explore</h4>
                    <ul>
                        <li><a href="#">Buy</a></li>
                        <li><a href="#">Rent</a></li>
                        <li><a href="#">Sell</a></li>
                        <li><a href="#">New developments</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Our samsars</a></li>
                        <li><a href="#">Journal</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Buyer guide</a></li>
                        <li><a href="#">Seller guide</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Terms</a></li>
                        <li><a href="#">Privacy</a></li>
                        <li><a href="#">Cookies</a></li>
                        <li><a href="#">Mentions légales</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>© <span data-year>2026</span> SAMSAR Real Estate · Casablanca, Morocco</span>
                <div class="socials">
                    <a href="#" aria-label="Instagram">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8">
                            <rect x="3" y="3" width="18" height="18" rx="5" />
                            <circle cx="12" cy="12" r="4" />
                            <circle cx="17.5" cy="6.5" r="0.8" fill="currentColor" />
                        </svg>
                    </a>
                    <a href="#" aria-label="LinkedIn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <line x1="8" y1="10" x2="8" y2="17" />
                            <circle cx="8" cy="7" r="0.8" fill="currentColor" />
                            <path d="M12 17v-4a2 2 0 0 1 4 0v4M12 10v7" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Twitter">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M18.244 3H21l-6.52 7.45L22 21h-6.81l-4.5-5.83L5.4 21H2.64l6.98-7.98L2 3h6.94l4.06 5.4L18.244 3Zm-2.39 16.2h1.5L7.27 4.7H5.66l10.193 14.5Z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- MODAL -->
    <div class="modal" id="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-backdrop" data-close-modal></div>
        <div class="modal-panel" role="document">
            <button class="modal-close" data-close-modal aria-label="Close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
            <h3 id="modal-title">Let's <em>get started.</em></h3>
            <p>Tell us about your property. A SAMSAR broker will reach out within 24 hours with a free
                valuation.
            </p>
            <form>
                <div class="field-row">
                    <div class="field">
                        <label for="m-name">Full name</label>
                        <input id="m-name" type="text" required placeholder="Yassine El Amrani" />
                    </div>
                    <div class="field">
                        <label for="m-phone">Phone</label>
                        <input id="m-phone" type="tel" required placeholder="+212 …" />
                    </div>
                </div>
                <div class="field">
                    <label for="m-email">Email</label>
                    <input id="m-email" type="email" required placeholder="you@samsar.ma" />
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="m-city">City</label>
                        <select id="m-city">
                            <option>Marrakech</option>
                            <option>Casablanca</option>
                            <option>Tangier</option>
                            <option>Rabat</option>
                            <option>Fès</option>
                            <option>Essaouira</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="m-type">Property type</label>
                        <select id="m-type">
                            <option>Riad</option>
                            <option>Villa</option>
                            <option>Apartment</option>
                            <option>Land</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">
                    Request valuation <span class="arrow" aria-hidden="true">→</span>
                </button>
            </form>
        </div>
    </div>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/01-home.js"></script>
</body>

</html>
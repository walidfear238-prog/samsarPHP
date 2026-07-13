<?php
session_start();



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-i18n-doctitle="properties.title">SAMSAR · Properties</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/02-properties.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
    <link rel="stylesheet" href="css/rtl.css" />
    <script src="js/translations.js"></script>
    <script src="js/language-switcher.js"></script>
</head>

<body>
    <div class="page-trans"><span></span><span></span><span></span></div>
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
            if (isset($_SESSION["user_id"])) {
                // User is logged in, show dashboard link and logout option
                echo '<div class="nav-right">';
                echo '<a href="dashboard.php" style="font-size:14px;font-weight:500"><span data-i18n="nav.dashboard">Dashboard</span></a>';


                echo '<a href="logout.php" class="btn btn-secondary"><span data-i18n="nav.logout">Logout</span></a>';
                echo '
        </div>';
            } else {
                // User is not logged in, show sign in and register options
                echo '            <div class="nav-right">
                <a href="08-login.php" class="nav-text"><span data-i18n="nav.signin">Sign in</span></a>
                <a href="09-register.php" class="btn btn-primary"><span data-i18n="nav.join">Join SAMSAR</span> <span class="arrow">→</span></a>
            </div>';
            }
            ?>






        </div>
    </header>

    <main>

        <section class="page-head">
            <div> <span data-i18n="properties.marketplace">Marketplace</span>
                <h1><span data-i18n="properties.hero.title1">Every Moroccan home,</span><br /><em data-i18n="properties.hero.title2">in one place.</em></h1>
                <p data-i18n="properties.hero.subtitle">Browse 1,284 verified listings from Marrakech to Tangier — riads,
                    villas,
                    apartments and land — brokered by the SAMSAR network.</p>
            </div>
        </section>
        <div class="container layout">
            <!-- LEFT FILTER SIDEBAR -->
            <aside class="filter-side">
                <button class="filter-toggle" id="ft-toggle" aria-label="Toggle filters" data-i18n-aria-label="properties.filters.toggle">
                    <span data-i18n="properties.filters">Filters</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" y1="6" x2="20" y2="6" />
                        <line x1="7" y1="12" x2="17" y2="12" />
                        <line x1="10" y1="18" x2="14" y2="18" />
                    </svg>
                </button>

                <div class="filter-panel" id="filter-panel">
                    <div class="fp-head">
                        <h2 data-i18n="properties.filters">Filters</h2>
                        <button class="reset-btn" id="reset-filters" data-i18n="properties.filters.reset">Reset</button>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label" data-i18n="properties.filters.search">Search</label>
                        <input type="search" placeholder="Marrakech, Anfa…" id="f-search" data-i18n-placeholder="properties.filters.search.placeholder" />
                    </div>

                    <div class="fp-group">
                        <label class="fp-label" data-i18n="properties.filters.purpose">Purpose</label>
                        <div class="seg-control" data-group="status">
                            <button class="seg-btn active" data-v="all" data-i18n="properties.filters.all">All</button>
                            <button class="seg-btn" data-v="sale" data-i18n="card.forsale">For Sale</button>
                            <button class="seg-btn" data-v="rent" data-i18n="card.forrent">For Rent</button>
                        </div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label" data-i18n="properties.filters.type">Property type</label>
                        <div class="chip-stack" data-group="type">
                            <button class="filter-chip active" data-v="all"><span data-i18n="properties.filters.all">All</span> <em>9</em></button>
                            <button class="filter-chip" data-v="villa"><span data-i18n="proptype.villas">Villas</span> <em>4</em></button>
                            <button class="filter-chip" data-v="riad"><span data-i18n="proptype.riads">Riads</span> <em>3</em></button>
                            <button class="filter-chip" data-v="apt"><span data-i18n="proptype.apartments">Apartments</span> <em>2</em></button>
                        </div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label" data-i18n="properties.filters.pricerange">Price range (MAD)</label>
                        <div class="price-inputs">
                            <input type="text" placeholder="Min" id="f-min-price" data-i18n-placeholder="properties.filters.min" />
                            <span>—</span>
                            <input type="text" placeholder="Max" id="f-max-price" data-i18n-placeholder="properties.filters.max" />
                        </div>
                        <div class="price-bar"><span class="track"></span><span class="fill"></span></div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label" data-i18n="properties.filters.bedrooms">Bedrooms</label>
                        <div class="pill-row" data-group="bd">
                            <button class="pill active" data-v="0" data-i18n="properties.filters.any">Any</button>
                            <button class="pill" data-v="1">1+</button>
                            <button class="pill" data-v="2">2+</button>
                            <button class="pill" data-v="3">3+</button>
                            <button class="pill" data-v="4">4+</button>
                            <button class="pill" data-v="5">5+</button>
                        </div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label" data-i18n="properties.filters.bathrooms">Bathrooms</label>
                        <div class="pill-row" data-group="ba">
                            <button class="pill active" data-v="0" data-i18n="properties.filters.any">Any</button>
                            <button class="pill" data-v="1">1+</button>
                            <button class="pill" data-v="2">2+</button>
                            <button class="pill" data-v="3">3+</button>
                            <button class="pill" data-v="4">4+</button>
                        </div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label" data-i18n="properties.filters.city">City</label>
                        <div class="check-list">
                            <label class="ck"><input type="checkbox" data-city="marrakech" />
                                <span>Marrakech</span>
                                <em>412</em></label>
                            <label class="ck"><input type="checkbox" data-city="casablanca" />
                                <span>Casablanca</span>
                                <em>238</em></label>
                            <label class="ck"><input type="checkbox" data-city="tangier" /> <span>Tangier</span>
                                <em>197</em></label>
                            <label class="ck"><input type="checkbox" data-city="rabat" /> <span>Rabat</span>
                                <em>164</em></label>
                            <label class="ck"><input type="checkbox" data-city="fes" /> <span>Fès</span>
                                <em>120</em></label>
                            <label class="ck"><input type="checkbox" data-city="essaouira" />
                                <span>Essaouira</span>
                                <em>94</em></label>
                        </div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label" data-i18n="properties.filters.features">Features</label>
                        <div class="check-list">
                            <label class="ck"><input type="checkbox" data-feature="pool" />
                                <span data-i18n="feature.pool">Pool</span></label>
                            <label class="ck"><input type="checkbox" data-feature="garden" />
                                <span data-i18n="feature.garden">Garden</span></label>
                            <label class="ck"><input type="checkbox" data-feature="hammam" />
                                <span data-i18n="feature.hammam">Hammam</span></label>
                            <label class="ck"><input type="checkbox" data-feature="sea" /> <span data-i18n="feature.seaview">Sea
                                    view</span></label>
                            <label class="ck"><input type="checkbox" data-feature="mountain" /> <span data-i18n="feature.mountainview">Mountain
                                    view</span></label>
                            <label class="ck"><input type="checkbox" data-feature="parking" />
                                <span data-i18n="feature.parking">Parking</span></label>
                        </div>
                    </div>

                    <button class="btn btn-primary apply-btn" type="button" id="apply-filters"><span data-i18n="properties.filters.apply">Apply filters</span>
                        <span class="arrow">→</span></button>
                </div>
            </aside>

            <!-- RESULTS -->
            <section class="results">
                <div class="results-head reveal">
                    <div><span><strong id="result-count">9</strong> <span data-i18n="properties.results.found">homes found</span></span><em class="active-loc"><span data-i18n="properties.results.in">in</span>
                            Marrakech</em>
                    </div>
                    <div class="r-tools">
                        <div class="sort">
                            <label data-i18n="properties.sort">Sort</label>
                            <select>
                                <option data-i18n="properties.sort.newest">Newest</option>
                                <option data-i18n="properties.sort.priceup">Price ↑</option>
                                <option data-i18n="properties.sort.pricedown">Price ↓</option>
                                <option data-i18n="properties.sort.mostviewed">Most viewed</option>
                            </select>
                        </div>
                        <div class="view-toggle">
                            <button class="vt-btn active" data-view="grid" aria-label="Grid view" data-i18n-aria-label="properties.view.grid"><svg width="14"
                                    height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <rect x="3" y="3" width="8" height="8" />
                                    <rect x="13" y="3" width="8" height="8" />
                                    <rect x="3" y="13" width="8" height="8" />
                                    <rect x="13" y="13" width="8" height="8" />
                                </svg></button>
                            <button class="vt-btn" data-view="list" aria-label="List view" data-i18n-aria-label="properties.view.list"><svg width="14" height="14"
                                    viewBox="0 0 24 24" fill="currentColor">
                                    <rect x="3" y="4" width="18" height="3" />
                                    <rect x="3" y="10" width="18" height="3" />
                                    <rect x="3" y="16" width="18" height="3" />
                                </svg></button>
                        </div>
                    </div>
                </div>

                <div class="property-grid" id="grid"></div>

                <div class="pagination reveal">
                    <button class="page-btn"><span data-i18n="properties.pagination.prev">← Prev</span></button>
                    <span class="page-num active">1</span>
                    <span class="page-num">2</span>
                    <span class="page-num">3</span>
                    <span class="page-num">…</span>
                    <span class="page-num">54</span>
                    <button class="page-btn"><span data-i18n="properties.pagination.next">Next →</span></button>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <a href="index.php" class="brand"><svg class="brand-mark" viewBox="0 0 100 100" style="color:#C72C41">
                    <path
                        d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z M38 38 L62 38 L62 50 L38 50 Z M38 60 L62 60 L62 72 L38 72 Z"
                        fill-rule="evenodd" />
                </svg><span class="brand-word">SAMSAR</span></a>
            <span>© 2026 SAMSAR · Casablanca, Morocco</span>
        </div>
    </footer>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/02-properties.js"></script>
</body>

</html>
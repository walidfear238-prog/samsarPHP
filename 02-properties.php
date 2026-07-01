<?php
session_start();



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Properties</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/02-properties.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
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
                <a href="02-properties.php" class="active">Properties</a>
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
                echo '            <div class="nav-right">
                <a href="08-login.php" class="nav-text">Sign in</a>
                <a href="09-register.php" class="btn btn-primary">Join SAMSAR <span class="arrow">→</span></a>
            </div>';
            }
            ?>






        </div>
    </header>

    <main>

        <section class="page-head">
            <div> <span>Marketplace</span>
                <h1>Every Moroccan home,<br /><em>in one place.</em></h1>
                <p>Browse 1,284 verified listings from Marrakech to Tangier — riads,
                    villas,
                    apartments and land — brokered by the SAMSAR network.</p>
            </div>
        </section>
        <div class="container layout">
            <!-- LEFT FILTER SIDEBAR -->
            <aside class="filter-side">
                <button class="filter-toggle" id="ft-toggle" aria-label="Toggle filters">
                    <span>Filters</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" y1="6" x2="20" y2="6" />
                        <line x1="7" y1="12" x2="17" y2="12" />
                        <line x1="10" y1="18" x2="14" y2="18" />
                    </svg>
                </button>

                <div class="filter-panel" id="filter-panel">
                    <div class="fp-head">
                        <h2>Filters</h2>
                        <button class="reset-btn" id="reset-filters">Reset</button>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label">Search</label>
                        <input type="search" placeholder="Marrakech, Anfa…" id="f-search" />
                    </div>

                    <div class="fp-group">
                        <label class="fp-label">Purpose</label>
                        <div class="seg-control" data-group="status">
                            <button class="seg-btn active" data-v="all">All</button>
                            <button class="seg-btn" data-v="sale">For Sale</button>
                            <button class="seg-btn" data-v="rent">For Rent</button>
                        </div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label">Property type</label>
                        <div class="chip-stack" data-group="type">
                            <button class="filter-chip active" data-v="all">All <em>9</em></button>
                            <button class="filter-chip" data-v="villa">Villas <em>4</em></button>
                            <button class="filter-chip" data-v="riad">Riads <em>3</em></button>
                            <button class="filter-chip" data-v="apt">Apartments <em>2</em></button>
                        </div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label">Price range (MAD)</label>
                        <div class="price-inputs">
                            <input type="text" placeholder="Min" id="f-min-price" />
                            <span>—</span>
                            <input type="text" placeholder="Max" id="f-max-price" />
                        </div>
                        <div class="price-bar"><span class="track"></span><span class="fill"></span></div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label">Bedrooms</label>
                        <div class="pill-row" data-group="bd">
                            <button class="pill active" data-v="0">Any</button>
                            <button class="pill" data-v="1">1+</button>
                            <button class="pill" data-v="2">2+</button>
                            <button class="pill" data-v="3">3+</button>
                            <button class="pill" data-v="4">4+</button>
                            <button class="pill" data-v="5">5+</button>
                        </div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label">Bathrooms</label>
                        <div class="pill-row" data-group="ba">
                            <button class="pill active" data-v="0">Any</button>
                            <button class="pill" data-v="1">1+</button>
                            <button class="pill" data-v="2">2+</button>
                            <button class="pill" data-v="3">3+</button>
                            <button class="pill" data-v="4">4+</button>
                        </div>
                    </div>

                    <div class="fp-group">
                        <label class="fp-label">City</label>
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
                        <label class="fp-label">Features</label>
                        <div class="check-list">
                            <label class="ck"><input type="checkbox" data-feature="pool" />
                                <span>Pool</span></label>
                            <label class="ck"><input type="checkbox" data-feature="garden" />
                                <span>Garden</span></label>
                            <label class="ck"><input type="checkbox" data-feature="hammam" />
                                <span>Hammam</span></label>
                            <label class="ck"><input type="checkbox" data-feature="sea" /> <span>Sea
                                    view</span></label>
                            <label class="ck"><input type="checkbox" data-feature="mountain" /> <span>Mountain
                                    view</span></label>
                            <label class="ck"><input type="checkbox" data-feature="parking" />
                                <span>Parking</span></label>
                        </div>
                    </div>

                    <button class="btn btn-primary apply-btn" type="button" id="apply-filters">Apply filters
                        <span class="arrow">→</span></button>
                </div>
            </aside>

            <!-- RESULTS -->
            <section class="results">
                <div class="results-head reveal">
                    <div><span><strong id="result-count">9</strong> homes found</span><em class="active-loc">in
                            Marrakech</em>
                    </div>
                    <div class="r-tools">
                        <div class="sort">
                            <label>Sort</label>
                            <select>
                                <option>Newest</option>
                                <option>Price ↑</option>
                                <option>Price ↓</option>
                                <option>Most viewed</option>
                            </select>
                        </div>
                        <div class="view-toggle">
                            <button class="vt-btn active" data-view="grid" aria-label="Grid view"><svg width="14"
                                    height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <rect x="3" y="3" width="8" height="8" />
                                    <rect x="13" y="3" width="8" height="8" />
                                    <rect x="3" y="13" width="8" height="8" />
                                    <rect x="13" y="13" width="8" height="8" />
                                </svg></button>
                            <button class="vt-btn" data-view="list" aria-label="List view"><svg width="14" height="14"
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
                    <button class="page-btn">← Prev</button>
                    <span class="page-num active">1</span>
                    <span class="page-num">2</span>
                    <span class="page-num">3</span>
                    <span class="page-num">…</span>
                    <span class="page-num">54</span>
                    <button class="page-btn">Next →</button>
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
<?php
session_start();

require_once "db/connect.php";

/**
 * ============================================================================
 * SAMSAR homepage data layer
 * ----------------------------------------------------------------------------
 * Fetches Featured Listings + the logged-in-only "Latest in your feed" rail.
 * All queries use mysqli prepared statements (no raw user input is ever
 * concatenated into SQL). Every DB call is wrapped in try/catch so a query
 * failure degrades gracefully (empty section) instead of crashing the page.
 * ============================================================================
 */

const FEATURED_LIMIT = 6; // cards shown in "Featured listings"
const FEED_LIMIT = 10;    // cards shown in "Latest in your feed" (logged-in only)

/**
 * Checks whether a column exists on a given table.
 * Used so this page still works today (schema has no `is_featured` yet)
 * and automatically switches to it once you add the column — see the
 * migration note further down.
 */
function samsar_column_exists(mysqli $conn, string $table, string $column): bool
{
    try {
        $stmt = $conn->prepare(
            "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
             LIMIT 1"
        );
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ss", $table, $column);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    } catch (Throwable $e) {
        error_log("[SAMSAR][index.php] samsar_column_exists failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Featured Listings — pulled from `properties`.
 *
 * NOTE: this database does not currently have an `is_featured` column
 * (see migration note below the code). Until it's added, this falls back
 * to the newest published properties so the section is never empty.
 */
function get_featured_properties(mysqli $conn, int $limit): array
{
    $properties = [];
    try {
        $has_featured_col = samsar_column_exists($conn, 'properties', 'is_featured');
        // Built from a boolean, never from user input — safe to inline.
        $featured_clause = $has_featured_col ? "p.is_featured = 1 AND " : "";

        $sql = "SELECT p.id, p.title, p.price, p.city, p.district, p.status,
                       p.bedrooms, p.bathrooms, p.area, p.created_at,
                       (SELECT pi.image_path FROM property_images pi
                        WHERE pi.property_id = p.id
                        ORDER BY pi.is_primary DESC, pi.sort_order ASC
                        LIMIT 1) AS img
                FROM properties p
                WHERE {$featured_clause}p.status != 'draft'
                ORDER BY p.created_at DESC
                LIMIT ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $properties[] = $row;
        }
        $stmt->close();
    } catch (Throwable $e) {
        error_log("[SAMSAR][index.php] get_featured_properties failed: " . $e->getMessage());
    }
    return $properties;
}

/**
 * "Latest in your feed" — only ever called when a user is logged in.
 * Shows ALL recently published listings (not filtered by featured status).
 */
function get_latest_properties(mysqli $conn, int $limit): array
{
    $properties = [];
    try {
        $sql = "SELECT p.id, p.title, p.price, p.city, p.district, p.status,
                       p.bedrooms, p.bathrooms, p.area, p.created_at,
                       (SELECT pi.image_path FROM property_images pi
                        WHERE pi.property_id = p.id
                        ORDER BY pi.is_primary DESC, pi.sort_order ASC
                        LIMIT 1) AS img
                FROM properties p
                WHERE p.status != 'draft'
                ORDER BY p.created_at DESC
                LIMIT ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $properties[] = $row;
        }
        $stmt->close();
    } catch (Throwable $e) {
        error_log("[SAMSAR][index.php] get_latest_properties failed: " . $e->getMessage());
    }
    return $properties;
}

/** Total published (non-draft) listings, used for the "View all" count. */
function get_public_properties_count(mysqli $conn): int
{
    try {
        $result = $conn->query("SELECT COUNT(*) AS cnt FROM properties WHERE status != 'draft'");
        if ($result && ($row = $result->fetch_assoc())) {
            return (int) $row['cnt'];
        }
    } catch (Throwable $e) {
        error_log("[SAMSAR][index.php] get_public_properties_count failed: " . $e->getMessage());
    }
    return 0;
}

/**
 * Picks the badge shown on a card: "New" if published in the last 7 days,
 * otherwise mapped from the property's status.
 */
function get_property_badge(array $property): array
{
    $created = strtotime((string) $property['created_at']);
    if ($created !== false && $created >= strtotime('-7 days')) {
        return ['key' => 'card.new', 'text' => 'New', 'class' => 'crimson'];
    }
    switch ($property['status']) {
        case 'rented':
            return ['key' => 'card.forrent', 'text' => 'For Rent', 'class' => ''];
        case 'sold':
            return ['key' => 'propstatus.sold', 'text' => 'Sold', 'class' => 'crimson'];
        case 'pending':
            return ['key' => 'propstatus.pending', 'text' => 'Pending', 'class' => ''];
        case 'available':
        default:
            return ['key' => 'card.forsale', 'text' => 'For Sale', 'class' => 'crimson'];
    }
}

/** Renders one property card. Shared by both the feed and featured grids. */
function render_property_card(array $p): void
{
    $badge    = get_property_badge($p);
    $img      = !empty($p['img'])
        ? 'uploads/property_images/' . htmlspecialchars($p['img'], ENT_QUOTES, 'UTF-8')
        : 'https://placehold.co/600x400/eef2f5/8ba3b0?text=No+Image';
    $title    = htmlspecialchars((string) $p['title'], ENT_QUOTES, 'UTF-8');
    $city     = htmlspecialchars((string) ($p['city'] ?? ''), ENT_QUOTES, 'UTF-8');
    $district = htmlspecialchars((string) ($p['district'] ?? ''), ENT_QUOTES, 'UTF-8');
    $location = $district !== '' ? "{$district} · {$city}" : $city;
    $price    = number_format((float) $p['price'], 0);
    $isRent   = $p['status'] === 'rented';
    $unitKey  = $isRent ? 'unit.mad_mo' : 'unit.mad';
    $unitText = $isRent ? 'MAD / mo' : 'MAD';
    ?>
<article class="card reveal">
    <div class="card-media">
        <img src="<?= $img ?>" alt="<?= $title ?>" loading="lazy"
            onerror="this.onerror=null;this.src='https://placehold.co/600x400/eef2f5/8ba3b0?text=No+Image';" />
        <span class="card-badge <?= $badge['class'] ?>" data-i18n="<?= $badge['key'] ?>"><?= $badge['text'] ?></span>
        <button class="card-fav" aria-label="Save property" data-i18n-aria-label="card.save_property">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path
                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
        </button>
    </div>
    <div class="card-body">
        <span class="card-loc"><?= $location ?></span>
        <h3 class="card-title"><?= $title ?></h3>
        <div class="card-specs">
            <span><?= (int) $p['bedrooms'] ?> <span
                    data-i18n="unit.bd">bd</span></span><span><?= (int) $p['bathrooms'] ?> <span
                    data-i18n="unit.ba">ba</span></span><span><?= rtrim(rtrim(number_format((float) $p['area'], 1), '0'), '.') ?>
                m²</span>
        </div>
        <div class="card-foot">
            <span class="card-price"><?= $price ?> <small data-i18n="<?= $unitKey ?>"><?= $unitText ?></small></span>
            <a class="card-cta" href="03-property-details.php?id=<?= (int) $p['id'] ?>" data-cursor="hover"><span
                    data-i18n="card.viewdetails">View Details</span>
                <span class="arrow">→</span></a>
        </div>
    </div>
</article>
<?php
}

$is_logged_in = isset($_SESSION['user_id']);

$featured_properties     = get_featured_properties($conn, FEATURED_LIMIT);
$latest_properties       = $is_logged_in ? get_latest_properties($conn, FEED_LIMIT) : [];
$public_properties_count = get_public_properties_count($conn);

/**
 * ----------------------------------------------------------------------------
 * MIGRATION NOTE (does not run automatically — nothing here alters your schema):
 * Your current `properties` table has no `is_featured` column, so Featured
 * Listings above falls back to "newest published" until you add one. To turn
 * on manual curation, run this whenever you're ready:
 *
 *   ALTER TABLE properties ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0;
 *   UPDATE properties SET is_featured = 1 WHERE id IN (42, 43); -- pick your picks
 *
 * The code above already checks for the column and will start using it the
 * moment it exists — no further changes needed.
 * ----------------------------------------------------------------------------
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" data-i18n-content="home.meta.description"
        content="SAMSAR — Morocco's premium real estate platform. Riads, villas, and homes from Marrakech to Tangier, brokered with transparency." />
    <title data-i18n-doctitle="home.title">SAMSAR · Find your place in Morocco</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="styles/01-home.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
    <link rel="stylesheet" href="css/rtl.css" />
    <script src="js/translations.js"></script>
    <script src="js/language-switcher.js"></script>
</head>

<body>

    <!-- Custom cursor -->
    <div class="cursor" aria-hidden="true"></div>
    <div class="cursor-dot" aria-hidden="true"></div>

    <!-- Navigation -->
    <header class="nav" role="banner">
        <div class="container nav-inner">
            <a href="#" class="brand" aria-label="SAMSAR home">
                <svg class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">
                    <path
                        d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
                </svg>
                <span class="brand-word">SAMSAR</span>

            </a>

            <nav class="nav-links" aria-label="Primary">
                <a href="02-properties.php" data-i18n="nav.properties">Properties</a>
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
                echo ' <div class="nav-right">
            <a href="08-login.php" style="font-size:14px;font-weight:500"><span data-i18n="nav.signin">Sign in</span></a>
            <a href="09-register.php" class="btn btn-primary">
                <span data-i18n="nav.join">Join SAMSAR</span>
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
                <h1 class="hero-title" data-scroll-hide><span data-i18n="home.hero.title_loggedin">One click away from your </span><em data-i18n="home.hero.title_loggedin_em">home.</em></h1>
                 </div> ';
            } else {

                echo '            <div class="container hero-content">
                <h1 class="hero-title" data-scroll-hide><span data-i18n="home.hero.title">One click away from your </span><em data-i18n="home.hero.title_em">dream.</em></h1>
                <a href="09-register.php" class="btn btn-primary hero-cta" data-scroll-hide><span data-i18n="home.hero.joinus">Join us</span></a>
            </div>';

            }
            ?>






        </section>

        <!-- ACTION LIST -->
        <section class="actions" id="buy">
            <div class="container">
                <div class="actions-header reveal">
                    <h2><span data-i18n="home.actions.title1">Three ways</span><br /><span
                            data-i18n="home.actions.title2">to move with SAMSAR.</span></h2>
                    <p data-i18n="home.actions.subtitle">Whether you're settling in, cashing out, or just exploring —
                        start with a single tap. Every
                        path
                        is
                        brokered by a verified local samsar.</p>
                </div>

                <a class="action-row reveal" href="02-properties.php" data-cursor="hover">
                    <span class="arrow-in" aria-hidden="true">→</span>
                    <span class="label" data-i18n="home.actions.buy">Buy</span>
                    <span class="meta"><strong>1,284 <span
                                data-i18n="home.actions.homes_count">homes</span></strong>Marrakech · Casablanca ·
                        Tangier</span>
                </a>
                <a class="action-row reveal" href="02-properties.php" data-cursor="hover" data-delay="80">
                    <span class="arrow-in" aria-hidden="true">→</span>
                    <span class="label" data-i18n="home.actions.rent">Rent</span>
                    <span class="meta"><strong>540 <span
                                data-i18n="home.actions.listings_count">listings</span></strong><span
                            data-i18n="home.actions.rent_meta">Long-term & seasonal riads</span></span>
                </a>
                <a class="action-row reveal" href="10-register-choose.php" data-cursor="hover" data-delay="160">
                    <span class="arrow-in" aria-hidden="true">→</span>
                    <span class="label" data-i18n="home.actions.sell">Sell</span>
                    <span class="meta"><strong data-i18n="home.actions.free_valuation">Free valuation</strong><span
                            data-i18n="home.actions.sell_meta">Listed in under 48 hours</span></span>
                </a>
            </div>
        </section>

        <!-- FEED & LATEST -->
        <section class="featured" id="developments">
            <div class="container">

                <?php if ($is_logged_in): ?>
                <!-- "Latest in your feed" — only rendered at all when logged in.
                         Logged-out visitors get no trace of this markup in the DOM. -->
                <div class="featured-header reveal">
                    <h2><span data-i18n="home.feed.title">Latest in </span><em data-i18n="home.feed.title_em">your
                            feed.</em></h2>
                    <p data-i18n="home.feed.subtitle">New listings from agencies you follow.</p>
                </div>
                <div class="property-grid" id="feed-grid">
                    <?php if (empty($latest_properties)): ?>
                    <p class="reveal" data-i18n="home.feed.empty">No new listings yet — check back soon.</p>
                    <?php else: ?>
                    <?php foreach ($latest_properties as $property): ?>
                    <?php render_property_card($property); ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="featured-header reveal" style="margin-top: 80px;">
                    <h2><span data-i18n="home.featured.title">Featured </span><em
                            data-i18n="home.featured.title_em">listings.</em></h2>
                    <a class="view-all" href="02-properties.php" data-cursor="hover">
                        <span data-i18n="home.featured.viewall">View all</span>
                        <?= number_format($public_properties_count) ?> →
                    </a>
                </div>

                <div class="property-grid">
                    <?php if (empty($featured_properties)): ?>
                    <p class="reveal" data-i18n="home.featured.empty">No featured listings yet — check back soon.</p>
                    <?php else: ?>
                    <?php foreach ($featured_properties as $property): ?>
                    <?php render_property_card($property); ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- WHY SAMSAR -->
        <section class="why" id="journal">
            <div class="container">
                <div class="why-grid">
                    <div class="why-intro reveal">
                        <span class="eyebrow" data-i18n="home.why.eyebrow">Why SAMSAR</span>
                        <h2><span data-i18n="home.why.title1">A modern broker,</span><br /><span
                                data-i18n="home.why.title2">rooted in </span><em
                                data-i18n="home.why.title2_em">tradition.</em></h2>
                        <p data-i18n="home.why.subtitle">The samsar has always been the soul of Moroccan property — the
                            one who knows the door
                            behind
                            the door.
                            We've kept the trust and added the technology.</p>
                    </div>
                    <div class="why-stats">
                        <div class="stat reveal">
                            <div class="num">100<em>%</em></div>
                            <h3 data-i18n="home.why.stat1.title">Verified listings</h3>
                            <p data-i18n="home.why.stat1.text">Every title deed cross-checked with the Conservation
                                Foncière before publication.</p>
                        </div>
                        <div class="stat reveal" data-delay="100">
                            <div class="num">14<em>+</em></div>
                            <h3 data-i18n="home.why.stat2.title">Local samsars</h3>
                            <p data-i18n="home.why.stat2.text">A network of vetted brokers across Marrakech, Casablanca,
                                Tangier, Fès and the coast.
                            </p>
                        </div>
                        <div class="stat reveal" data-delay="200">
                            <div class="num">0<em>%</em></div>
                            <h3 data-i18n="home.why.stat3.title">Hidden fees</h3>
                            <p data-i18n="home.why.stat3.text">Flat 2.5% commission. Itemised in writing before you ever
                                sign a thing.</p>
                        </div>
                        <div class="stat reveal" data-delay="300">
                            <div class="num">3<em>×</em></div>
                            <h3 data-i18n="home.why.stat4.title">Bilingual contracts</h3>
                            <p data-i18n="home.why.stat4.text">Arabic, French and English — drafted by a Moroccan notary
                                you choose.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CITIES -->
        <section class="cities">
            <div class="container">
                <div class="cities-head reveal">
                    <h2 data-i18n="home.cities.title">Explore by city.</h2>
                    <div class="cities-controls" role="group" aria-label="Scroll cities">
                        <button data-cities-prev aria-label="Previous cities" data-i18n-aria-label="home.cities.prev">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                        </button>
                        <button data-cities-next aria-label="Next cities" data-i18n-aria-label="home.cities.next">
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
                            <h3>Chefchaouen</h3><span>86 <span data-i18n="unit.homes">homes</span></span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=700&q=80"
                            alt="Marrakech medina rooftops" loading="lazy" />
                        <div class="city-label">
                            <h3>Marrakech</h3><span>412 <span data-i18n="unit.homes">homes</span></span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1538230575309-59fe2ecf5b59?auto=format&fit=crop&w=700&q=80"
                            alt="Fès tanneries and medina" loading="lazy" />
                        <div class="city-label">
                            <h3>Fès</h3><span>164 <span data-i18n="unit.homes">homes</span></span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1577147443647-81d0e4bfe4cc?auto=format&fit=crop&w=700&q=80"
                            alt="Tangier waterfront" loading="lazy" />
                        <div class="city-label">
                            <h3>Tangier</h3><span>238 <span data-i18n="unit.homes">homes</span></span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1570214476695-19bd467e6f7a?auto=format&fit=crop&w=700&q=80"
                            alt="Hassan Tower Rabat" loading="lazy" />
                        <div class="city-label">
                            <h3>Rabat</h3><span>197 <span data-i18n="unit.homes">homes</span></span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1528657249085-893be9ffd04f?auto=format&fit=crop&w=700&q=80"
                            alt="Essaouira fishing port and ramparts" loading="lazy" />
                        <div class="city-label">
                            <h3>Essaouira</h3><span>94 <span data-i18n="unit.homes">homes</span></span>
                        </div>
                    </div>
                    <div class="city" data-cursor="hover">
                        <img src="https://images.unsplash.com/photo-1518730518541-d0843268c287?auto=format&fit=crop&w=700&q=80"
                            alt="Ouarzazate desert kasbah" loading="lazy" />
                        <div class="city-label">
                            <h3>Ouarzazate</h3><span>52 <span data-i18n="unit.homes">homes</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA BAND -->
        <section class="cta-band" id="list">
            <div class="container cta-inner">
                <?php if ($is_logged_in): ?>
                <!-- Logged-in users don't need the registration pitch — send them to their dashboard instead. -->
                <h2><span data-i18n="home.cta.title1_loggedin">Ready for your next move?</span><br /><em
                        data-i18n="home.cta.title2_loggedin">Go to your dashboard.</em></h2>
                <a href="dashboard.php" class="btn" data-cursor="hover">
                    <span data-i18n="home.cta.button_loggedin">Go to Dashboard</span> <span class="arrow"
                        aria-hidden="true">→</span>
                </a>
                <?php else: ?>
                <h2><span data-i18n="home.cta.title1">Selling your home?</span><br /><em
                        data-i18n="home.cta.title2">Let's get started.</em></h2>
                <a href="09-register.php" class="btn" data-cursor="hover">
                    <span data-i18n="home.cta.button">Let's get started</span> <span class="arrow"
                        aria-hidden="true">→</span>
                </a>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="footer" role="contentinfo">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="brand">
                        <svg class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor" aria-hidden="true">
                            <path
                                d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
                        </svg>
                        <span class="brand-word">SAMSAR</span>
                    </a>
                    <p data-i18n="footer.tagline">The trusted Moroccan broker, reimagined. Properties brokered with
                        transparency, from the
                        Atlas to
                        the
                        Atlantic.</p>
                </div>

                <div class="footer-col">
                    <h4 data-i18n="footer.explore">Explore</h4>
                    <ul>
                        <li><a href="#" data-i18n="footer.explore.buy">Buy</a></li>
                        <li><a href="#" data-i18n="footer.explore.rent">Rent</a></li>
                        <li><a href="#" data-i18n="footer.explore.sell">Sell</a></li>
                        <li><a href="#" data-i18n="footer.explore.newdev">New developments</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 data-i18n="footer.company">Company</h4>
                    <ul>
                        <li><a href="#" data-i18n="footer.company.about">About</a></li>
                        <li><a href="#" data-i18n="footer.company.samsars">Our samsars</a></li>
                        <li><a href="#" data-i18n="footer.company.journal">Journal</a></li>
                        <li><a href="#" data-i18n="footer.company.careers">Careers</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 data-i18n="footer.support">Support</h4>
                    <ul>
                        <li><a href="#" data-i18n="footer.support.contact">Contact</a></li>
                        <li><a href="#" data-i18n="footer.support.faq">FAQ</a></li>
                        <li><a href="#" data-i18n="footer.support.buyerguide">Buyer guide</a></li>
                        <li><a href="#" data-i18n="footer.support.sellerguide">Seller guide</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 data-i18n="footer.legal">Legal</h4>
                    <ul>
                        <li><a href="#" data-i18n="footer.legal.terms">Terms</a></li>
                        <li><a href="#" data-i18n="footer.legal.privacy">Privacy</a></li>
                        <li><a href="#" data-i18n="footer.legal.cookies">Cookies</a></li>
                        <li><a href="#" data-i18n="footer.legal.mentions">Mentions légales</a></li>
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
            <button class="modal-close" data-close-modal aria-label="Close" data-i18n-aria-label="modal.close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
            <h3 id="modal-title"><span data-i18n="modal.title1">Let's </span><em data-i18n="modal.title1_em">get
                    started.</em></h3>
            <p data-i18n="modal.subtitle">Tell us about your property. A SAMSAR broker will reach out within 24 hours
                with a free
                valuation.
            </p>
            <form>
                <div class="field-row">
                    <div class="field">
                        <label for="m-name" data-i18n="modal.fullname">Full name</label>
                        <input id="m-name" type="text" required placeholder="Yassine El Amrani"
                            data-i18n-placeholder="modal.fullname.placeholder" />
                    </div>
                    <div class="field">
                        <label for="m-phone" data-i18n="modal.phone">Phone</label>
                        <input id="m-phone" type="tel" required placeholder="+212 …"
                            data-i18n-placeholder="modal.phone.placeholder" />
                    </div>
                </div>
                <div class="field">
                    <label for="m-email" data-i18n="modal.email">Email</label>
                    <input id="m-email" type="email" required placeholder="you@samsar.ma"
                        data-i18n-placeholder="modal.email.placeholder" />
                </div>
                <div class="field-row">
                    <div class="field">
                        <label for="m-city" data-i18n="modal.city">City</label>
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
                        <label for="m-type" data-i18n="modal.propertytype">Property type</label>
                        <select id="m-type">
                            <option data-i18n="proptype.riad">Riad</option>
                            <option data-i18n="proptype.villa">Villa</option>
                            <option data-i18n="proptype.apartment">Apartment</option>
                            <option data-i18n="proptype.land">Land</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">
                    <span data-i18n="modal.requestvaluation">Request valuation</span> <span class="arrow"
                        aria-hidden="true">→</span>
                </button>
            </form>
        </div>
    </div>

    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/01-home.js"></script>
</body>

</html>
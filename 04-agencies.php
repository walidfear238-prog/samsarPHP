<?php
session_start();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMSAR · Agencies</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/04-agencies.css" />
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
        <section class="page-head">
            <div class="container">
                <span class="eyebrow reveal">Network</span>
                <h1 class="reveal" data-delay="60">The samsars<br /><em>of Morocco.</em></h1>
                <p class="reveal" data-delay="120">14 vetted agencies, hundreds of brokers — every one of them
                    verified by
                    SAMSAR. Find the right partner for your city, your style, your timeline.</p>
                <div class="head-search reveal" data-delay="180">
                    <input type="text" id="agency-search" placeholder="Search agencies by name or city…" />
                    <button class="btn btn-primary">Search <span class="arrow">→</span></button>
                </div>
            </div>
        </section>

        <section class="ag-list">
            <div class="container">
                <div class="ag-tabs reveal">
                    <button class="ag-tab active">All cities</button>
                    <button class="ag-tab">Marrakech</button>
                    <button class="ag-tab">Casablanca</button>
                    <button class="ag-tab">Tangier</button>
                    <button class="ag-tab">Rabat</button>
                    <button class="ag-tab">Fès</button>
                    <button class="ag-tab">Essaouira</button>
                </div>

                <div class="ag-grid" id="ag-grid"></div>
            </div>
        </section>
    </main>
    <footer class="footer">
        <div class="container footer-inner"><span>© 2026 SAMSAR · Casablanca, Morocco</span></div>
    </footer>
    <script src="scripts/04-agencies.js"></script>
</body>

</html>
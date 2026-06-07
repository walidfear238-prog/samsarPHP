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
      <a href="index.php" class="brand"><svg class="brand-mark" viewBox="0 0 100 100">
          <path
            d="M22 44 L50 18 L78 44 L78 86 Q78 90 74 90 L26 90 Q22 90 22 86 Z M38 38 L62 38 L62 50 L38 50 Z M38 60 L62 60 L62 72 L38 72 Z"
            fill-rule="evenodd" />
        </svg><span class="brand-word">SAMSAR</span></a>
      <nav class="nav-links"><a href="02-properties.php">Properties</a><a href="04-agencies.php"
          class="active">Agencies</a><a href="06-about.php">About</a><a href="07-contact.php">Contact</a></nav>
      <div class="nav-right"><a href="08-login.php" class="nav-text">Sign in</a><a href="10-register-choose.php"
          class="btn btn-primary">Join SAMSAR <span class="arrow">→</span></a></div>
    </div>
  </header>
  <main>
    <section class="page-head">
      <div class="container">
        <span class="eyebrow reveal">Network</span>
        <h1 class="reveal" data-delay="60">The samsars<br /><em>of Morocco.</em></h1>
        <p class="reveal" data-delay="120">14 vetted agencies, hundreds of brokers — every one of them verified by
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
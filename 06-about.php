<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-i18n-doctitle="about.title">SAMSAR · About</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/06-about.css" />
    <link rel="stylesheet" href="styles/samsar-transitions.css" />
    <link rel="stylesheet" href="css/rtl.css" />
    <link rel="stylesheet" href="styles/responsive-nav.css" />
    <script src="js/translations.js"></script>
    <script src="js/language-switcher.js"></script>
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
            <nav class="nav-links"><a href="02-properties.php" data-i18n="nav.properties">Properties</a><a href="04-agencies.php" data-i18n="nav.agencies">Agencies</a><a
                    href="06-about.php" data-i18n="nav.about" class="active">About</a><a href="07-contact.php" data-i18n="nav.contact">Contact</a>

            </nav>
            <?php
      if (isset($_SESSION['user_id'])) {
        echo '<div class="nav-right">';
        echo '<a href="dashboard.php" style="font-size:14px;font-weight:500"><span data-i18n="nav.dashboard">Dashboard</span></a>';


        echo '<a href="logout.php" class="btn btn-secondary"><span data-i18n="nav.logout">Logout</span></a>';
        echo '
        <button class="nav-toggle" aria-label="Open menu" aria-expanded="false"><span></span></button>
        </div>';
      } else {

        echo '            <div class="nav-right"><a href="08-login.php" class="nav-text"><span data-i18n="nav.signin">Sign in</span></a><a href="10-register-choose.php"
                    class="btn btn-primary"><span data-i18n="nav.join">Join SAMSAR</span> <span class="arrow">→</span></a>
            <button class="nav-toggle" aria-label="Open menu" aria-expanded="false"><span></span></button>
            </div>';

      }

      ?>



        </div>
    </header>
    <main>
        <section class="hero">
            <div class="container">
                <span class="eyebrow reveal" data-i18n="about.eyebrow">About SAMSAR</span>
                <h1 class="reveal" data-delay="60"><span data-i18n="about.hero.title1">The trusted broker,</span><br /><em data-i18n="about.hero.title2">reimagined.</em></h1>
                <p class="reveal" data-delay="120" data-i18n="about.hero.subtitle">In Morocco, the <em>samsar</em> has always been the soul of property
                    — the
                    one who knows the door behind the door. We kept the trust. We rebuilt the rest.</p>
            </div>
        </section>

        <section class="story">
            <div class="container story-grid">
                <div class="story-img reveal"><img
                        src="https://images.unsplash.com/photo-1539020140153-e479b8c22e70?auto=format&fit=crop&w=1200&q=85"
                        alt="Marrakech medina" /></div>
                <div class="story-text">
                    <span class="eyebrow reveal" data-i18n="about.origin.eyebrow">Origin</span>
                    <h2 class="reveal" data-delay="60"><span data-i18n="about.origin.title1">Born in Casablanca,</span><br /><span data-i18n="about.origin.title2">built for Morocco.</span></h2>
                    <p class="reveal" data-delay="120" data-i18n="about.origin.p1">SAMSAR was founded in 2026 by three Moroccan technologists who
                        had each,
                        separately, tried to buy a home in Marrakech — and each, separately, lost weeks to opaque
                        listings, phantom
                        commissions, and contradicting samsars.</p>
                    <p class="reveal" data-delay="180" data-i18n="about.origin.p2">We started as a quiet experiment: what if every listing was
                        verified before
                        it went live, every commission was disclosed in writing, and every contract was offered in
                        Arabic, French
                        and English? A year later, fourteen agencies and 1,200 listings later — SAMSAR is the answer.
                    </p>
                </div>
            </div>
        </section>

        <section class="values">
            <div class="container">
                <span class="eyebrow reveal" data-i18n="about.values.eyebrow">What we believe</span>
                <h2 class="reveal" data-delay="60" data-i18n="about.values.title">Three principles. No exceptions.</h2>
                <div class="val-grid">
                    <div class="val reveal"><span class="num">01</span>
                        <h3 data-i18n="about.values.v1.title">Verified before published</h3>
                        <p data-i18n="about.values.v1.text">Every title deed is cross-checked with the Conservation Foncière. Every photograph is matched
                            to GPS
                            data. No exceptions.</p>
                    </div>
                    <div class="val reveal" data-delay="100"><span class="num">02</span>
                        <h3 data-i18n="about.values.v2.title">Transparent commission</h3>
                        <p data-i18n="about.values.v2.text">A flat 2.5% commission, itemised in writing, disclosed before any visit. No referral
                            kickbacks. No
                            surprise fees at closing.</p>
                    </div>
                    <div class="val reveal" data-delay="200"><span class="num">03</span>
                        <h3 data-i18n="about.values.v3.title">Bilingual contracts</h3>
                        <p data-i18n="about.values.v3.text">Arabic, French, English. Drafted by a Moroccan notary you choose — never one we refer for a
                            fee.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="team">
            <div class="container">
                <span class="eyebrow reveal" data-i18n="about.team.eyebrow">The team</span>
                <h2 class="reveal" data-delay="60" data-i18n="about.team.title">A small studio in Casablanca.</h2>
                <div class="team-grid">
                    <div class="member reveal"><img
                            src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=500&q=80"
                            alt="Salma El Idrissi" /><strong>Salma El Idrissi</strong><span data-i18n="about.team.role1">Co-founder · Product</span>
                    </div>
                    <div class="member reveal" data-delay="80"><img
                            src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=500&q=80"
                            alt="Yassine Berrada" /><strong>Yassine Berrada</strong><span data-i18n="about.team.role2">Co-founder ·
                            Engineering</span></div>
                    <div class="member reveal" data-delay="160"><img
                            src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=500&q=80"
                            alt="Imane Cherkaoui" /><strong>Imane Cherkaoui</strong><span data-i18n="about.team.role3">Co-founder · Brokerage</span>
                    </div>
                    <div class="member reveal" data-delay="240"><img
                            src="https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=500&q=80"
                            alt="Karim Benjelloun" /><strong>Karim Benjelloun</strong><span data-i18n="about.team.role4">Head of Network</span></div>
                </div>
            </div>
        </section>

        <section class="cta-band">
            <div class="container cta-inner">
                <h2 class="reveal"><span data-i18n="about.cta.title1">Ready to </span><em data-i18n="about.cta.title2">start looking?</em></h2>
                <a href="02-properties.php" class="btn btn-light"><span data-i18n="about.cta.button">Browse properties</span> <span class="arrow">→</span></a>
            </div>
        </section>
    </main>
    <footer class="footer">
        <div class="container footer-inner"><span>© 2026 SAMSAR · Casablanca, Morocco</span></div>
    </footer>
    <script src="scripts/samsar-transitions.js"></script>
    <script src="scripts/06-about.js"></script>
    <script>
    (function() {
        if (window.SamsarTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            var old = document.querySelector('.page-trans');
            if (old) old.remove();
            setTimeout(function() {
                SamsarTransition.play('flip-y', 'slow')
            }, 50);
        }
    })();
    </script>
    <script src="scripts/responsive-nav.js"></script>
</body>

</html>
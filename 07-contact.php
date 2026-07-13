<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title data-i18n-doctitle="contact.title">SAMSAR · Contact</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,300;1,9..144,400&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles/07-contact.css" />
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
            <a href="index.php" class="brand"> <svg class="brand-mark" viewBox="0 0 1080 1080" fill="currentColor"
                    aria-hidden="true">
                    <path
                        d="M734.34,464.81v-21.85c0-2.87-1.34-5.57-3.62-7.31l-152.36-116.23c-17.21-13.13-40.93-13.69-58.74-1.39l-170,117.41c-2.48,1.72-3.97,4.54-3.97,7.56v48.22c0,5.08,4.11,9.19,9.19,9.19h517.47c5.08,0,9.19,4.11,9.19,9.19v362.76c0,5.08-4.11,9.19-9.19,9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-189.17c0-5.08,4.11-9.19,9.19-9.19h128.79c5.08,0,9.19,4.11,9.19,9.19v42c0,5.08,4.11,9.19,9.19,9.19h370.3c5.08,0,9.19-4.11,9.19-9.19v-68.42c0-5.08-4.11-9.19-9.19-9.19H207.68c-5.08,0-9.19-4.11-9.19-9.19v-272.61c0-3.02,1.48-5.85,3.97-7.56l223.99-154.69,97.47-67.32c17.82-12.3,41.53-11.74,58.74,1.39l94.18,71.86,57.49,43.85,143.55,109.51c2.28,1.74,3.62,4.44,3.62,7.31v94.68c0,5.08-4.11,9.19-9.19,9.19h-128.79c-5.08,0-9.19-4.11-9.19-9.19Z" />
                </svg><span class="brand-word">SAMSAR</span></a>
            <nav class="nav-links"><a href="02-properties.php" data-i18n="nav.properties">Properties</a><a href="04-agencies.php" data-i18n="nav.agencies">Agencies</a><a
                    href="06-about.php" data-i18n="nav.about">About</a><a href="07-contact.php" data-i18n="nav.contact" class="active">Contact</a></nav>

            <?php
            if (isset($_SESSION['user_id'])) {
                echo '<div class="nav-right">';
                echo '<a href="dashboard.php" style="font-size:14px;font-weight:500"><span data-i18n="nav.dashboard">Dashboard</span></a>';


                echo '<a href="logout.php" class="btn btn-secondary"><span data-i18n="nav.logout">Logout</span></a>';
                echo '
            </div>';
            } else {
                echo '            <div class="nav-right"><a href="08-login.php" class="nav-text"><span data-i18n="nav.signin">Sign in</span></a><a href="10-register-choose.php"
                    class="btn btn-primary"><span data-i18n="nav.join">Join SAMSAR</span> <span class="arrow">→</span></a></div>
        </div>';
            }
            ?>

    </header>
    <main>
        <div class="container layout">
            <section class="intro">
                <span class="eyebrow reveal" data-i18n="nav.contact">Contact</span>
                <h1 class="reveal" data-delay="60"><span data-i18n="contact.hero.title1">Let's talk</span><br /><em data-i18n="contact.hero.title2">property.</em></h1>
                <p class="reveal" data-delay="120" data-i18n="contact.hero.subtitle">Whether you're looking, listing or just exploring — we'd love to
                    hear
                    from
                    you. Our team replies within one business day, in Arabic, French or English.</p>

                <div class="contact-blocks">
                    <div class="cb reveal" data-delay="180"><span class="cb-label" data-i18n="contact.general">General</span><a
                            href="mailto:hello@samsar.ma">hello@samsar.ma</a></div>
                    <div class="cb reveal" data-delay="220"><span class="cb-label" data-i18n="agencyprofile.phone">Phone</span><a
                            href="tel:+212524000000">+212 5
                            24 00 00 00</a></div>
                    <div class="cb reveal" data-delay="260"><span class="cb-label" data-i18n="agencyprofile.office">Office</span>22 Boulevard
                        d'Anfa<br />Casablanca 20000, Morocco</div>
                    <div class="cb reveal" data-delay="300"><span class="cb-label" data-i18n="agencyprofile.hours">Hours</span><span data-i18n="contact.hours.value">Monday–Saturday<br />9:00
                        – 19:00
                        (GMT+1)</span></div>
                </div>
            </section>

            <section class="form-block reveal" data-delay="160">
                <form id="contact-form">
                    <h2 data-i18n="contact.form.title">Send a message</h2>
                    <div class="row">
                        <div class="field"><label for="name" data-i18n="modal.fullname">Full name</label><input id="name" type="text" required
                                placeholder="Yassine El Amrani" data-i18n-placeholder="modal.fullname.placeholder" /></div>
                        <div class="field"><label for="email" data-i18n="modal.email">Email</label><input id="email" type="email" required
                                placeholder="you@email.com" data-i18n-placeholder="contact.form.email.placeholder" /></div>
                    </div>
                    <div class="row">
                        <div class="field"><label for="phone" data-i18n="agencyprofile.phone">Phone</label><input id="phone" type="tel"
                                placeholder="+212 …" data-i18n-placeholder="modal.phone.placeholder" />
                        </div>
                        <div class="field"><label for="topic" data-i18n="contact.form.topic">Topic</label>
                            <select id="topic">
                                <option data-i18n="contact.form.topic.general">General enquiry</option>
                                <option data-i18n="contact.form.topic.buy">I want to buy</option>
                                <option data-i18n="contact.form.topic.sell">I want to sell</option>
                                <option data-i18n="contact.form.topic.rent">I want to rent</option>
                                <option data-i18n="contact.form.topic.partnership">Partnership</option>
                                <option data-i18n="contact.form.topic.press">Press</option>
                            </select>
                        </div>
                    </div>
                    <div class="field"><label for="msg" data-i18n="propdetails.message">Message</label><textarea id="msg" rows="6" required
                            placeholder="Tell us a little about what you're looking for…" data-i18n-placeholder="contact.form.message.placeholder"></textarea></div>
                    <button class="btn btn-primary" type="submit"><span data-i18n="contact.form.submit">Send message</span> <span class="arrow">→</span></button>
                </form>
            </section>
        </div>

        <section class="map-section">

        </section>
    </main>
    <footer class="footer">
        <div class="container footer-inner"><span>© 2026 SAMSAR · Casablanca, Morocco</span></div>
    </footer>
    <script src="scripts/07-contact.js"></script>
</body>

</html>
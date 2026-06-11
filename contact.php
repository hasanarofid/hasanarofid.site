<?php
session_start();
$lang = $_SESSION['lang'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang === 'id' ? 'Hubungi Kami' : 'Contact Us' ?> | Hasan Arofid</title>
    <link rel="icon" type="image/png" href="images/logohasanarofid.png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f8fafc; --surface: #ffffff; --border: rgba(0,0,0,0.08); --text: #0f172a; --text-muted: #475569; --accent: #3b82f6; --gradient: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); }
        [data-theme="dark"] { --bg: #030712; --surface: #0f172a; --border: rgba(255,255,255,0.08); --text: #f8fafc; --text-muted: #94a3b8; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.8; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 0 auto; padding: 0 24px; }
        nav { padding: 32px 0; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-weight: 800; font-size: 1.5rem; text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px; }
        .logo img { height: 40px; }
        .back-link { color: var(--text-muted); text-decoration: none; font-weight: 500; }
        
        article { padding: 40px 0 100px; text-align: center; }
        h1 { font-family: 'Poppins', sans-serif; font-size: 2.5rem; margin-bottom: 16px; }
        p.subtitle { color: var(--text-muted); font-size: 1.1rem; margin-bottom: 48px; }
        
        .contact-card { background: var(--surface); border: 1px solid var(--border); padding: 40px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 500px; margin: 0 auto; }
        .contact-method { margin-bottom: 32px; }
        .contact-method h3 { margin-bottom: 8px; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); }
        .contact-value { font-size: 1.5rem; font-weight: 700; color: var(--text); text-decoration: none; display: block; margin-bottom: 16px; }
        
        .btn { display: inline-flex; align-items: center; padding: 14px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; transition: 0.3s; cursor: pointer; border: none; font-size: 1rem; }
        .btn-whatsapp { background: #25d366; color: white; }
        .btn-whatsapp:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3); }

        footer { padding: 60px 0; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <a href="index.php" class="logo">
                <img src="images/logohasanarofid.png" alt="Logo">
                <span>Hasan Arofid</span>
            </a>
            <a href="index.php" class="back-link">← <?= $lang === 'id' ? 'Kembali' : 'Back' ?></a>
        </nav>

        <article>
            <h1><?= $lang === 'id' ? 'Mari Berdiskusi' : 'Let\'s Connect' ?></h1>
            <p class="subtitle"><?= $lang === 'id' ? 'Siap membantu mewujudkan proyek impian Anda.' : 'Ready to help you build your dream project.' ?></p>
            
            <div class="contact-card">
                <div class="contact-method">
                    <h3>WhatsApp</h3>
                    <a href="https://wa.me/628814959247" class="contact-value">+62 881-4959-247</a>
                    <a href="https://wa.me/628814959247" class="btn btn-whatsapp"><?= $lang === 'id' ? 'Chat di WhatsApp' : 'Chat on WhatsApp' ?></a>
                </div>
                
                <div style="border-top: 1px solid var(--border); padding-top: 32px;">
                    <h3>Instagram</h3>
                    <a href="https://instagram.com/hasanarofid" target="_blank" style="color: var(--accent); text-decoration: none; font-weight: 600;">@hasanarofid</a>
                </div>
            </div>
        </article>
    </div>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Hasan Arofid. All rights reserved.</p>
            <div style="margin: 20px 0; display: flex; justify-content: center; flex-wrap: wrap; gap: 24px; font-size: 0.9rem;">
                <a href="about.php" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Tentang Kami' : 'About Us' ?></a>
                <a href="privacy-policy.php" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Kebijakan Privasi' : 'Privacy Policy' ?></a>
                <a href="terms-of-service.php" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Syarat & Ketentuan' : 'Terms of Service' ?></a>
                <a href="disclaimer.php" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Penafian' : 'Disclaimer' ?></a>
                <a href="contact.php" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Kontak' : 'Contact' ?></a>
            </div>
        </div>
    </footer>

    <script>
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
    </script>
</body>
</html>

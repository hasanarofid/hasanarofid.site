<?php
session_start();
$lang = $_SESSION['lang'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang === 'id' ? 'Penafian (Disclaimer)' : 'Disclaimer' ?> | Hasan Arofid</title>
    <link rel="icon" type="image/png" href="images/logohasanarofid.png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f8fafc; --surface: #ffffff; --border: rgba(0,0,0,0.08); --text: #0f172a; --text-muted: #475569; --accent: #3b82f6; }
        [data-theme="dark"] { --bg: #030712; --surface: #0f172a; --border: rgba(255,255,255,0.08); --text: #f8fafc; --text-muted: #94a3b8; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.8; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 0 auto; padding: 0 24px; }
        nav { padding: 32px 0; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-weight: 800; font-size: 1.5rem; text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px; }
        .logo img { height: 40px; }
        .back-link { color: var(--text-muted); text-decoration: none; font-weight: 500; }
        
        article { padding: 40px 0 100px; }
        h1 { font-family: 'Poppins', sans-serif; font-size: 2.5rem; margin-bottom: 32px; }
        h2 { font-family: 'Poppins', sans-serif; margin-top: 40px; margin-bottom: 16px; }
        p { margin-bottom: 20px; }
        footer { padding: 60px 0; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); }
        .footer-links { display: flex; justify-content: center; flex-wrap: wrap; gap: 24px; margin-top: 20px; font-size: 0.9rem; }
        .footer-links a { color: var(--text-muted); text-decoration: none; }
        .footer-links a:hover { color: var(--accent); }
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
            <?php if ($lang === 'id'): ?>
                <h1>Penafian (Disclaimer)</h1>
                <p>Terakhir diperbarui: <?= date('d F Y') ?></p>
                <p>Semua informasi di situs web ini - hasanarofid.site - diterbitkan dengan itikad baik dan hanya untuk tujuan informasi umum. Hasan Arofid tidak memberikan jaminan apa pun tentang kelengkapan, keandalan, dan keakuratan informasi ini.</p>
                
                <h2>Tindakan Anda</h2>
                <p>Segala tindakan yang Anda ambil atas informasi yang Anda temukan di situs web ini (Hasan Arofid), sepenuhnya merupakan risiko Anda sendiri. Hasan Arofid tidak akan bertanggung jawab atas kerugian dan/atau kerusakan sehubungan dengan penggunaan situs web kami.</p>
                
                <h2>Tautan Eksternal</h2>
                <p>Dari situs web kami, Anda dapat mengunjungi situs web lain dengan mengikuti hyperlink ke situs eksternal tersebut. Meskipun kami berusaha untuk hanya menyediakan tautan berkualitas ke situs web yang berguna dan etis, kami tidak memiliki kendali atas konten dan sifat situs-situs tersebut.</p>
                
                <h2>Persetujuan</h2>
                <p>Dengan menggunakan situs web kami, Anda dengan ini menyetujui penafian kami dan menyetujui ketentuan-ketentuannya.</p>
            <?php else: ?>
                <h1>Disclaimer</h1>
                <p>Last updated: <?= date('F d, Y') ?></p>
                <p>All the information on this website - hasanarofid.site - is published in good faith and for general information purpose only. Hasan Arofid does not make any warranties about the completeness, reliability and accuracy of this information.</p>
                
                <h2>Your Action</h2>
                <p>Any action you take upon the information you find on this website (Hasan Arofid), is strictly at your own risk. Hasan Arofid will not be liable for any losses and/or damages in connection with the use of our website.</p>
                
                <h2>External Links</h2>
                <p>From our website, you can visit other websites by following hyperlinks to such external sites. While we strive to provide only quality links to useful and ethical websites, we have no control over the content and nature of these sites.</p>
                
                <h2>Consent</h2>
                <p>By using our website, you hereby consent to our disclaimer and agree to its terms.</p>
            <?php endif; ?>
        </article>
    </div>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Hasan Arofid. All rights reserved.</p>
            <div class="footer-links">
                <a href="about.php"><?= $lang === 'id' ? 'Tentang Kami' : 'About Us' ?></a>
                <a href="privacy-policy.php"><?= $lang === 'id' ? 'Kebijakan Privasi' : 'Privacy Policy' ?></a>
                <a href="terms-of-service.php"><?= $lang === 'id' ? 'Syarat & Ketentuan' : 'Terms of Service' ?></a>
                <a href="disclaimer.php"><?= $lang === 'id' ? 'Penafian' : 'Disclaimer' ?></a>
                <a href="contact.php"><?= $lang === 'id' ? 'Kontak' : 'Contact' ?></a>
            </div>
        </div>
    </footer>

    <script>
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
    </script>
</body>
</html>

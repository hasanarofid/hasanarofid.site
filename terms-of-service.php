<?php
session_start();
$lang = $_SESSION['lang'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang === 'id' ? 'Syarat dan Ketentuan' : 'Terms of Service' ?> | Hasan Arofid</title>
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
                <h1>Syarat dan Ketentuan</h1>
                <p>Selamat datang di hasanarofid.site!</p>
                <p>Syarat dan ketentuan ini menguraikan aturan dan peraturan untuk penggunaan situs web Hasan Arofid.</p>
                
                <h2>Lisensi</h2>
                <p>Kecuali dinyatakan lain, Hasan Arofid dan/atau pemberi lisensinya memiliki hak kekayaan intelektual untuk semua materi di hasanarofid.site. Semua hak kekayaan intelektual dilindungi undang-undang.</p>
                
                <h2>Tanggung Jawab Konten</h2>
                <p>Kami tidak bertanggung jawab atas konten apa pun yang muncul di situs web Anda. Anda setuju untuk melindungi dan membela kami dari semua klaim yang muncul di situs web Anda.</p>
                
                <h2>Penafian</h2>
                <p>Sejauh diizinkan oleh hukum yang berlaku, kami mengecualikan semua pernyataan, jaminan, dan ketentuan yang berkaitan dengan situs web kami dan penggunaan situs web ini.</p>
            <?php else: ?>
                <h1>Terms of Service</h1>
                <p>Welcome to hasanarofid.site!</p>
                <p>These terms and conditions outline the rules and regulations for the use of Hasan Arofid's Website.</p>
                
                <h2>License</h2>
                <p>Unless otherwise stated, Hasan Arofid and/or its licensors own the intellectual property rights for all material on hasanarofid.site. All intellectual property rights are reserved.</p>
                
                <h2>Content Liability</h2>
                <p>We shall not be hold responsible for any content that appears on your Website. You agree to protect and defend us against all claims that is rising on your Website.</p>
                
                <h2>Disclaimer</h2>
                <p>To the maximum extent permitted by applicable law, we exclude all representations, warranties and conditions relating to our website and the use of this website.</p>
            <?php endif; ?>
        </article>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Hasan Arofid. All rights reserved.</p>
    </footer>

    <script>
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
    </script>
</body>
</html>

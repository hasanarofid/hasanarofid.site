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

  <style>
    /* Floating WhatsApp */
    .whatsapp-float {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #25d366;
      color: white;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      box-shadow: 0 10px 25px rgba(37, 211, 102, 0.3);
      z-index: 2000;
      text-decoration: none;
      transition: 0.3s;
    }
    .whatsapp-float:hover {
      transform: scale(1.1) translateY(-5px);
      box-shadow: 0 15px 30px rgba(37, 211, 102, 0.4);
    }
  </style>
  <a href="https://wa.me/628814959247?text=Halo%20Hasan,%20saya%20ingin%20konsultasi%20mengenai%20pembuatan%20website"
    class="whatsapp-float" target="_blank" aria-label="Chat on WhatsApp">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="currentColor">
      <path
        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
    </svg>
  </a>
</body>
</html>

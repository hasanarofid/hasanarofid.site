<?php
session_start();
$lang = $_SESSION['lang'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang === 'id' ? 'Tentang Kami' : 'About Us' ?> | Hasan Arofid</title>
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
                <h1>Tentang Kami</h1>
                <p>Selamat datang di platform digital Hasan Arofid.</p>
                <p>Kami adalah penyedia layanan pengembangan web profesional dan arsitektur perangkat lunak yang berbasis di Indonesia. Dengan pengalaman lebih dari 10 tahun, kami mengkhususkan diri dalam membangun aplikasi web yang berkinerja tinggi, dapat diskalakan, dan inovatif.</p>
                
                <h2>Misi Kami</h2>
                <p>Misi kami adalah memberdayakan bisnis dengan menghadirkan solusi digital kelas atas. Kami percaya bahwa setiap bisnis layak mendapatkan platform yang bukan hanya indah secara visual tetapi juga memberikan pengalaman pengguna yang unggul, aman, dan dapat diandalkan secara operasional.</p>
                
                <h2>Keahlian Kami</h2>
                <p>Kami memiliki keahlian dalam berbagai tumpukan teknologi modern termasuk PHP (Laravel), Node.js, React, arsitektur database (PostgreSQL, MySQL), dan infrastruktur cloud. Mulai dari sistem ERP, solusi e-commerce, hingga platform SaaS kustom, kami menyesuaikan arsitektur untuk memenuhi kebutuhan spesifik dan target pertumbuhan Anda.</p>
                
                <h2>Mengapa Memilih Kami?</h2>
                <p>Fokus kami bukan hanya pada penulisan kode, tetapi pada pemecahan masalah bisnis Anda. Kami menekankan pada:
                <ul>
                    <li><strong>Performa:</strong> Aplikasi yang cepat dan teroptimasi.</li>
                    <li><strong>Skalabilitas:</strong> Sistem yang tumbuh bersama bisnis Anda.</li>
                    <li><strong>Keamanan:</strong> Melindungi data Anda dengan praktik terbaik industri.</li>
                </ul>
                </p>
                
            <?php else: ?>
                <h1>About Us</h1>
                <p>Welcome to Hasan Arofid's digital platform.</p>
                <p>We are a professional web development and software architecture service provider based in Indonesia. With over 10 years of experience, we specialize in building high-performance, scalable, and innovative web applications.</p>
                
                <h2>Our Mission</h2>
                <p>Our mission is to empower businesses by delivering top-tier digital solutions. We believe that every business deserves a platform that is not only visually stunning but also provides an excellent user experience, is secure, and is operationally reliable.</p>
                
                <h2>Our Expertise</h2>
                <p>We are proficient in a variety of modern technology stacks including PHP (Laravel), Node.js, React, database architecture (PostgreSQL, MySQL), and cloud infrastructure. From ERP systems and e-commerce solutions to custom SaaS platforms, we tailor the architecture to meet your specific needs and growth targets.</p>
                
                <h2>Why Choose Us?</h2>
                <p>Our focus is not just on writing code, but on solving your business problems. We emphasize:
                <ul>
                    <li><strong>Performance:</strong> Fast and optimized applications.</li>
                    <li><strong>Scalability:</strong> Systems that grow with your business.</li>
                    <li><strong>Security:</strong> Protecting your data with industry best practices.</li>
                </ul>
                </p>
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

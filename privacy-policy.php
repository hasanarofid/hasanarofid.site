<?php
session_start();
$lang = $_SESSION['lang'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang === 'id' ? 'Kebijakan Privasi' : 'Privacy Policy' ?> | Hasan Arofid</title>
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
        ul { margin-bottom: 24px; padding-left: 20px; }
        li { margin-bottom: 10px; }
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
                <h1>Kebijakan Privasi</h1>
                <p>Terakhir diperbarui: <?= date('d F Y') ?></p>
                <p>Di Hasan Arofid (hasanarofid.site), privasi pengunjung kami adalah salah satu prioritas utama kami. Dokumen Kebijakan Privasi ini berisi jenis informasi yang dikumpulkan dan dicatat oleh hasanarofid.site dan bagaimana kami menggunakannya.</p>
                
                <h2>Informasi yang Kami Kumpulkan</h2>
                <p>Jika Anda menghubungi kami secara langsung, kami mungkin menerima informasi tambahan tentang Anda seperti nama, alamat email, nomor telepon, isi pesan dan/atau lampiran yang mungkin Anda kirimkan kepada kami.</p>
                
                <h2>Log Files</h2>
                <p>hasanarofid.site mengikuti prosedur standar menggunakan file log. File-file ini mencatat pengunjung ketika mereka mengunjungi situs web. Informasi yang dikumpulkan oleh file log termasuk alamat protokol internet (IP), jenis browser, Penyedia Layanan Internet (ISP), stempel tanggal dan waktu, halaman rujukan/keluar, dan mungkin jumlah klik.</p>
                
                <h2>Cookie Google DoubleClick DART</h2>
                <p>Google adalah salah satu vendor pihak ketiga di situs kami. Google juga menggunakan cookie, yang dikenal sebagai cookie DART, untuk menayangkan iklan kepada pengunjung situs kami berdasarkan kunjungan mereka ke www.website.com dan situs lain di internet.</p>
                
                <h2>Kebijakan Privasi Pihak Ketiga</h2>
                <p>Kebijakan Privasi hasanarofid.site tidak berlaku untuk pengiklan atau situs web lain. Karena itu, kami menyarankan Anda untuk berkonsultasi dengan masing-masing Kebijakan Privasi dari server iklan pihak ketiga ini untuk informasi lebih rinci.</p>
            <?php else: ?>
                <h1>Privacy Policy</h1>
                <p>Last updated: <?= date('F d, Y') ?></p>
                <p>At Hasan Arofid (hasanarofid.site), one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by hasanarofid.site and how we use it.</p>
                
                <h2>Information we collect</h2>
                <p>If you contact us directly, we may receive additional information about you such as your name, email address, phone number, the contents of the message and/or attachments you may send us, and any other information you may choose to provide.</p>
                
                <h2>Log Files</h2>
                <p>hasanarofid.site follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks.</p>
                
                <h2>Google DoubleClick DART Cookie</h2>
                <p>Google is one of a third-party vendor on our site. It also uses cookies, known as DART cookies, to serve ads to our site visitors based upon their visit to www.website.com and other sites on the internet.</p>
                
                <h2>Third Party Privacy Policies</h2>
                <p>hasanarofid.site's Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information.</p>
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

<?php
$dbFile = __DIR__ . '/stats.db';
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $articles = $db->query("SELECT * FROM articles WHERE status = 'published' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Database connection failed."); }

session_start();
$lang = $_SESSION['lang'] ?? 'en';

$translations = [
    'en' => [
        'title' => 'Blog & Insights',
        'subtitle' => 'Deep dives into web architecture, performance, and digital business growth.',
        'back' => 'Back to Home',
        'read_more' => 'Read Full Article',
        'no_articles' => 'No articles found.'
    ],
    'id' => [
        'title' => 'Blog & Wawasan',
        'subtitle' => 'Pembahasan mendalam tentang arsitektur web, performa, dan pertumbuhan bisnis digital.',
        'back' => 'Kembali ke Beranda',
        'read_more' => 'Baca Selengkapnya',
        'no_articles' => 'Tidak ada artikel ditemukan.'
    ]
];
$t = $translations[$lang];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['title'] ?> | Hasan Arofid</title>
    <meta name="description" content="<?= $t['subtitle'] ?>">
    <link rel="icon" type="image/png" href="images/logohasanarofid.png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f8fafc; --surface: #ffffff; --border: rgba(0,0,0,0.08); --text: #0f172a; --text-muted: #475569; --accent: #3b82f6; --gradient: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); }
        [data-theme="dark"] { --bg: #030712; --surface: #0f172a; --border: rgba(255,255,255,0.08); --text: #f8fafc; --text-muted: #94a3b8; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        
        nav { padding: 32px 0; border-bottom: 1px solid var(--border); background: var(--surface); position: sticky; top: 0; z-index: 100; }
        .nav-content { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-weight: 800; font-size: 1.5rem; text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px; }
        .logo img { height: 40px; }
        .back-link { color: var(--text-muted); text-decoration: none; font-weight: 500; transition: 0.2s; }
        .back-link:hover { color: var(--accent); }

        header { padding: 80px 0 60px; text-align: center; }
        h1 { font-family: 'Poppins', sans-serif; font-size: 3rem; margin-bottom: 16px; }
        .subtitle { color: var(--text-muted); font-size: 1.2rem; max-width: 700px; margin: 0 auto; }

        .article-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 32px; padding-bottom: 100px; }
        .article-card { background: var(--surface); border: 1px solid var(--border); border-radius: 24px; padding: 40px; transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1); text-decoration: none; color: inherit; display: flex; flex-direction: column; position: relative; overflow: hidden; }
        .article-card::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--gradient); opacity: 0; transition: 0.3s; }
        .article-card:hover { border-color: var(--accent); transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .article-card:hover::after { opacity: 1; }
        .article-date { font-size: 0.85rem; color: var(--accent); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; display: block; }
        .article-card h3 { font-size: 1.5rem; margin-bottom: 16px; line-height: 1.3; font-family: 'Poppins', sans-serif; }
        .article-card p { font-size: 1rem; color: var(--text-muted); margin-bottom: 32px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .article-link { color: var(--text); font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 12px; transition: 0.3s; }
        .article-card:hover .article-link { color: var(--accent); gap: 16px; }

        footer { padding: 60px 0; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); }

        @media (max-width: 768px) {
            h1 { font-size: 2.2rem; }
            .article-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav>
        <div class="container nav-content">
            <a href="index.php" class="logo">
                <img src="images/logohasanarofid.png" alt="Logo">
                <span>Hasan Arofid</span>
            </a>
            <a href="index.php" class="back-link">← <?= $t['back'] ?></a>
        </div>
    </nav>

    <div class="container">
        <header>
            <h1><?= $t['title'] ?></h1>
            <p class="subtitle"><?= $t['subtitle'] ?></p>
        </header>

        <div class="article-grid">
            <?php if (empty($articles)): ?>
                <p style="text-align: center; grid-column: 1/-1; padding: 40px; color: var(--text-muted);"><?= $t['no_articles'] ?></p>
            <?php else: ?>
                <?php foreach ($articles as $a): ?>
                <a href="blog.php?slug=<?= $a['slug'] ?>" class="article-card">
                    <span class="article-date"><?= date('M d, Y', strtotime($a['created_at'])) ?></span>
                    <h3><?= htmlspecialchars($a['title']) ?></h3>
                    <p><?= htmlspecialchars($a['excerpt']) ?></p>
                    <span class="article-link"><?= $t['read_more'] ?> <span>→</span></span>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
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

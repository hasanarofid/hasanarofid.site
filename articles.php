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
        <p>&copy; <?= date('Y') ?> Hasan Arofid. All rights reserved.</p>
    </footer>

    <script>
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
    </script>
</body>
</html>

<?php
$dbFile = __DIR__ . '/stats.db';
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $articles = $db->query("SELECT * FROM articles WHERE status = 'published' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Database connection failed."); }

session_start();
$lang = $_SESSION['lang'] ?? 'id';

$translations = [
    'en' => [
        'title' => 'Engineering Blog & Technical Insights',
        'subtitle' => 'Deep dives into scalable web architecture, database optimization, Core Web Vitals, and modern software engineering.',
        'back' => 'Back to Home',
        'search_placeholder' => 'Search topics, technologies, or keywords...',
        'read_more' => 'Read Guide',
        'no_articles' => 'No articles found matching your criteria.'
    ],
    'id' => [
        'title' => 'Blog Rekayasa Perangkat Lunak & Wawasan Teknis',
        'subtitle' => 'Pembahasan mendalam seputar arsitektur web modern, optimasi performa database, Core Web Vitals, dan keamanan aplikasi bisnis.',
        'back' => 'Kembali ke Beranda',
        'search_placeholder' => 'Cari topik, teknologi, atau kata kunci...',
        'read_more' => 'Baca Panduan',
        'no_articles' => 'Tidak ada artikel yang cocok dengan pencarian Anda.'
    ]
];
$t = $translations[$lang] ?? $translations['id'];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['title'] ?> | Hasan Arofid</title>
    <meta name="description" content="<?= $t['subtitle'] ?>">
    <link rel="canonical" href="https://hasanarofid.site/articles" />
    <link rel="icon" type="image/png" href="/images/logohasanarofid.png" />
    <link rel="author" type="text/plain" href="https://hasanarofid.site/llms.txt" />

    <!-- Open Graph -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://hasanarofid.site/articles" />
    <meta property="og:title" content="<?= $t['title'] ?> | Hasan Arofid" />
    <meta property="og:description" content="<?= $t['subtitle'] ?>" />
    <meta property="og:image" content="https://hasanarofid.site/images/hasanarofid.png" />

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="https://hasanarofid.site/articles" />
    <meta property="twitter:title" content="<?= $t['title'] ?> | Hasan Arofid" />
    <meta property="twitter:description" content="<?= $t['subtitle'] ?>" />
    <meta property="twitter:image" content="https://hasanarofid.site/images/hasanarofid.png" />

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Beranda",
          "item": "https://hasanarofid.site/"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Artikel & Blog",
          "item": "https://hasanarofid.site/articles"
        }
      ]
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: rgba(0,0,0,0.08);
            --text: #0f172a;
            --text-muted: #475569;
            --accent: #2563eb;
            --accent-light: #dbeafe;
            --gradient: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        }
        [data-theme="dark"] {
            --bg: #030712;
            --surface: #0f172a;
            --border: rgba(255,255,255,0.08);
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #3b82f6;
            --accent-light: rgba(59, 130, 246, 0.15);
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        
        nav { padding: 24px 0; border-bottom: 1px solid var(--border); background: var(--surface); position: sticky; top: 0; z-index: 100; }
        .nav-content { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-weight: 800; font-size: 1.4rem; text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px; }
        .logo img { height: 38px; }
        .back-link { color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: 0.2s; }
        .back-link:hover { color: var(--accent); }

        header { padding: 60px 0 40px; text-align: center; }
        h1 { font-family: 'Poppins', sans-serif; font-size: clamp(2.2rem, 5vw, 3.2rem); margin-bottom: 16px; font-weight: 800; line-height: 1.25; }
        .subtitle { color: var(--text-muted); font-size: 1.15rem; max-width: 780px; margin: 0 auto 36px; }

        /* Search & Filter Bar */
        .filter-wrapper { max-width: 650px; margin: 0 auto 48px; display: flex; flex-direction: column; gap: 16px; }
        .search-input { width: 100%; padding: 16px 24px; border-radius: 16px; border: 1px solid var(--border); background: var(--surface); color: var(--text); font-size: 1.05rem; box-sizing: border-box; box-shadow: 0 4px 20px rgba(0,0,0,0.04); transition: 0.3s; }
        .search-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 4px 25px rgba(37,99,235,0.15); }

        .article-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 28px; padding-bottom: 100px; }
        .article-card { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; padding: 32px; transition: 0.3s ease; text-decoration: none; color: inherit; display: flex; flex-direction: column; position: relative; overflow: hidden; }
        .article-card::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--gradient); opacity: 0; transition: 0.3s; }
        .article-card:hover { border-color: var(--accent); transform: translateY(-6px); box-shadow: 0 16px 32px rgba(0,0,0,0.08); }
        .article-card:hover::after { opacity: 1; }
        
        .card-meta-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; font-size: 0.85rem; }
        .article-date { color: var(--accent); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .article-read-time { color: var(--text-muted); font-weight: 500; }
        
        .article-card h3 { font-size: 1.35rem; margin: 0 0 14px; line-height: 1.4; font-family: 'Poppins', sans-serif; font-weight: 700; }
        .article-card p { font-size: 0.98rem; color: var(--text-muted); margin: 0 0 24px; flex-grow: 1; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .article-link { color: var(--accent); font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; transition: 0.2s; margin-top: auto; }
        .article-card:hover .article-link { gap: 12px; }

        footer { padding: 60px 0; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); }

        @media (max-width: 640px) {
            .article-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav>
        <div class="container nav-content">
            <a href="/" class="logo">
                <img src="/images/logohasanarofid.png" alt="Logo">
                <span>Hasan Arofid</span>
            </a>
            <a href="/" class="back-link">← <?= $t['back'] ?></a>
        </div>
    </nav>

    <div class="container">
        <header>
            <h1><?= $t['title'] ?></h1>
            <p class="subtitle"><?= $t['subtitle'] ?></p>
            
            <div class="filter-wrapper">
                <input type="text" id="searchInput" class="search-input" placeholder="<?= $t['search_placeholder'] ?>" onkeyup="filterArticles()">
            </div>
        </header>

        <div class="article-grid" id="articlesGrid">
            <?php if (empty($articles)): ?>
                <p style="text-align: center; grid-column: 1/-1; padding: 40px; color: var(--text-muted);"><?= $t['no_articles'] ?></p>
            <?php else: ?>
                <?php foreach ($articles as $a): 
                    $readingTime = max(1, ceil(str_word_count(strip_tags($a['content'])) / 200));
                ?>
                <a href="/blog/<?= htmlspecialchars($a['slug']) ?>" class="article-card" data-title="<?= htmlspecialchars(strtolower($a['title'])) ?>" data-excerpt="<?= htmlspecialchars(strtolower($a['excerpt'])) ?>">
                    <div class="card-meta-top">
                        <span class="article-date"><?= date('d M Y', strtotime($a['created_at'])) ?></span>
                        <span class="article-read-time">⏱️ <?= $readingTime ?> min read</span>
                    </div>
                    <h3><?= htmlspecialchars($a['title']) ?></h3>
                    <p><?= htmlspecialchars($a['excerpt'] ?: strip_tags(substr($a['content'], 0, 140))) ?></p>
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
                <a href="/about" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Tentang Kami' : 'About Us' ?></a>
                <a href="/privacy-policy" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Kebijakan Privasi' : 'Privacy Policy' ?></a>
                <a href="/terms-of-service" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Syarat & Ketentuan' : 'Terms of Service' ?></a>
                <a href="/disclaimer" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Penafian' : 'Disclaimer' ?></a>
                <a href="/contact" style="color: var(--text-muted); text-decoration: none;"><?= $lang === 'id' ? 'Kontak' : 'Contact' ?></a>
            </div>
        </div>
    </footer>

    <script>
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');

        function filterArticles() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('#articlesGrid .article-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const title = card.getAttribute('data-title') || '';
                const excerpt = card.getAttribute('data-excerpt') || '';
                if (title.includes(query) || excerpt.includes(query)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

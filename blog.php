<?php
$dbFile = __DIR__ . '/stats.db';
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Database connection failed."); }

$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare("SELECT * FROM articles WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header("Location: index.php");
    exit;
}

session_start();
$lang = $_SESSION['lang'] ?? 'en';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> | Hasan Arofid Blog</title>
    <meta name="description" content="<?= htmlspecialchars($article['excerpt']) ?>">
    
    <!-- Open Graph / Facebook / Threads -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://hasanarofid.site/blog/<?= $article['slug'] ?>">
    <meta property="og:title" content="<?= htmlspecialchars($article['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($article['excerpt']) ?>">
    <meta property="og:image" content="https://hasanarofid.site/images/logohasanarofid.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://hasanarofid.site/blog/<?= $article['slug'] ?>">
    <meta property="twitter:title" content="<?= htmlspecialchars($article['title']) ?>">
    <meta property="twitter:description" content="<?= htmlspecialchars($article['excerpt']) ?>">
    <meta property="twitter:image" content="https://hasanarofid.site/images/logohasanarofid.png">

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
        
        article { padding: 60px 0 120px; }
        .article-header { margin-bottom: 48px; text-align: center; }
        .article-header h1 { font-family: 'Poppins', sans-serif; font-size: clamp(2rem, 5vw, 3.5rem); line-height: 1.2; margin-bottom: 24px; }
        .article-meta { color: var(--text-muted); font-size: 0.95rem; font-weight: 500; }
        
        .article-content { font-size: 1.15rem; color: var(--text); }
        .article-content h1, .article-content h2, .article-content h3 { margin-top: 48px; margin-bottom: 16px; font-family: 'Poppins', sans-serif; }
        .article-content p { margin-bottom: 24px; }
        .article-content img { max-width: 100%; border-radius: 16px; margin: 32px 0; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .article-content ul, .article-content ol { margin-bottom: 24px; padding-left: 24px; }
        .article-content li { margin-bottom: 12px; }
        .article-content blockquote { border-left: 4px solid var(--accent); padding-left: 24px; font-style: italic; color: var(--text-muted); margin: 32px 0; }
        
        .share-section { margin-top: 60px; padding: 32px; background: var(--surface); border: 1px solid var(--border); border-radius: 20px; text-align: center; }
        .share-buttons { display: flex; justify-content: center; gap: 16px; margin-top: 20px; }
        .share-btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 700; color: white; font-size: 0.9rem; transition: 0.3s; }
        .btn-wa { background: #25d366; }
        .btn-tw { background: #1da1f2; }
        .btn-li { background: #0077b5; }
        .btn-th { background: #000000; }
        .btn-copy { background: #64748b; cursor: pointer; border: none; font-family: inherit; }
        .share-btn:hover { transform: translateY(-3px); filter: brightness(1.1); }
        .share-btn:active { transform: scale(0.95); }

        .cta-box { margin-top: 40px; padding: 40px; background: var(--gradient); border-radius: 24px; color: white; text-align: center; }
        .cta-box h3 { margin-bottom: 16px; }
        .cta-box .btn-white { background: white; color: var(--accent); padding: 12px 32px; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-block; }

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
            <a href="index.php#blog" class="back-link">← <?= $lang === 'id' ? 'Kembali ke Blog' : 'Back to Blog' ?></a>
        </nav>

        <article>
            <div class="article-header">
                <div class="article-meta"><?= date('F d, Y', strtotime($article['created_at'])) ?></div>
                <h1><?= htmlspecialchars($article['title']) ?></h1>
            </div>
            
            <div class="article-content ql-editor">
                <?= $article['content'] ?>
            </div>

            <div class="share-section">
                <p><strong><?= $lang === 'id' ? 'Sukai artikel ini? Bagikan ke rekan Anda:' : 'Like this article? Share it with your network:' ?></strong></p>
                <div class="share-buttons">
                    <a href="https://api.whatsapp.com/send?text=Baca artikel menarik ini: <?= urlencode($article['title']) ?> - https://hasanarofid.site/blog/<?= $article['slug'] ?>" target="_blank" class="share-btn btn-wa">WhatsApp</a>
                    <a href="https://www.threads.net/intent/post?text=<?= urlencode($article['title'] . ' - https://hasanarofid.site/blog/' . $article['slug']) ?>" target="_blank" class="share-btn btn-th">Threads</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=https://hasanarofid.site/blog/<?= $article['slug'] ?>" target="_blank" class="share-btn btn-li">LinkedIn</a>
                    <button onclick="copyLink()" class="share-btn btn-copy" id="copyBtn"><?= $lang === 'id' ? 'Salin Link' : 'Copy Link' ?></button>
                </div>
            </div>

            <div class="cta-box">
                <h3><?= $lang === 'id' ? 'Butuh Solusi Web Profesional?' : 'Need Professional Web Solutions?' ?></h3>
                <p><?= $lang === 'id' ? 'Kami membantu bisnis Anda tumbuh dengan teknologi terbaru.' : 'We help your business grow with the latest technology.' ?></p>
                <a href="index.php#contact" class="btn-white"><?= $lang === 'id' ? 'Konsultasi Sekarang' : 'Consult Now' ?></a>
            </div>
        </article>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Hasan Arofid. All rights reserved.</p>
    </footer>

    <script>
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');

        function copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                const btn = document.getElementById('copyBtn');
                const originalText = btn.innerText;
                btn.innerText = '<?= $lang === 'id' ? 'Tersalin!' : 'Copied!' ?>';
                btn.style.background = '#10b981';
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.background = '#64748b';
                }, 2000);
            });
        }
    </script>
</body>
</html>

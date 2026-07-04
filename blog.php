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
            <a href="articles.php" class="back-link">← <?= $lang === 'id' ? 'Kembali ke Artikel' : 'Back to Articles' ?></a>
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

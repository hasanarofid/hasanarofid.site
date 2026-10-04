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

$relatedArticles = [];
if ($article) {
    $relStmt = $db->prepare("SELECT id, title, slug, excerpt, created_at FROM articles WHERE id != ? AND status = 'published' ORDER BY RANDOM() LIMIT 3");
    $relStmt->execute([$article['id']]);
    $relatedArticles = $relStmt->fetchAll(PDO::FETCH_ASSOC);
}

if (!$article) {
    http_response_code(404);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Hasan Arofid</title>
    <meta name="robots" content="noindex, follow">
    <link rel="icon" type="image/png" href="/images/logohasanarofid.png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f8fafc; --text: #0f172a; --text-muted: #475569; --accent: #3b82f6; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; text-align: center; padding: 20px; }
        .box { max-width: 500px; background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        h1 { font-family: 'Poppins', sans-serif; font-size: 4rem; margin: 0; color: var(--accent); }
        h2 { margin: 16px 0; font-size: 1.5rem; }
        p { color: var(--text-muted); margin-bottom: 24px; }
        a { display: inline-block; background: var(--accent); color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="box">
        <h1>404</h1>
        <h2>Artikel Tidak Ditemukan</h2>
        <p>Maaf, artikel yang Anda cari telah dipindahkan atau tidak tersedia lagi.</p>
        <a href="/articles">Lihat Semua Artikel</a>
    </div>
</body>
</html>
<?php
    exit;
}

session_start();
$lang = $_SESSION['lang'] ?? 'id';

// Calculate reading time
$wordCount = str_word_count(strip_tags($article['content']));
$readingTime = max(1, ceil($wordCount / 200));

// Process Content: Auto generate IDs for H2 and H3, and build Table of Contents (TOC)
$toc = [];
$contentWithIds = preg_replace_callback('/<h([23])(.*?)>(.*?)<\/h[23]>/i', function($matches) use (&$toc) {
    $level = $matches[1];
    $attrs = $matches[2];
    $titleText = strip_tags($matches[3]);
    $slugId = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titleText)));
    if (empty($slugId)) $slugId = 'section-' . count($toc);
    $toc[] = [
        'level' => (int)$level,
        'id' => $slugId,
        'title' => $titleText
    ];
    return "<h{$level}{$attrs} id=\"{$slugId}\">{$matches[3]}</h{$level}>";
}, $article['content']);

// Auto extract FAQ for FAQ schema if present
$faqSchema = [];
if (preg_match('/<h2>.*?(?:FAQ|Pertanyaan).*?<\/h2>(.*)/is', $article['content'], $faqMatch)) {
    preg_match_all('/<h3>(.*?)<\/h3>\s*<p>(.*?)<\/p>/is', $faqMatch[1], $faqItems, PREG_SET_ORDER);
    foreach ($faqItems as $item) {
        $q = trim(strip_tags($item[1]));
        $a = trim(strip_tags($item[2]));
        if (!empty($q) && !empty($a)) {
            $faqSchema[] = [
                "@type" => "Question",
                "name" => $q,
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $a
                ]
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> | Hasan Arofid</title>
    <meta name="description" content="<?= htmlspecialchars($article['excerpt']) ?>">
    <meta name="keywords" content="web development, laravel, fullstack, database, devops, performance, tutorial web, hasan arofid">
    
    <!-- Open Graph / Facebook / Threads -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://hasanarofid.site/blog/<?= $article['slug'] ?>">
    <meta property="og:title" content="<?= htmlspecialchars($article['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($article['excerpt']) ?>">
    <meta property="og:image" content="https://hasanarofid.site/images/hasanarofid.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://hasanarofid.site/blog/<?= $article['slug'] ?>">
    <meta property="twitter:title" content="<?= htmlspecialchars($article['title']) ?>">
    <meta property="twitter:description" content="<?= htmlspecialchars($article['excerpt']) ?>">
    <meta property="twitter:image" content="https://hasanarofid.site/images/hasanarofid.png">

    <link rel="canonical" href="https://hasanarofid.site/blog/<?= $article['slug'] ?>" />
    <link rel="icon" type="image/png" href="/images/logohasanarofid.png" />
    <link rel="author" type="text/plain" href="https://hasanarofid.site/llms.txt" />

    <!-- Schema.org BlogPosting Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BlogPosting",
      "headline": <?= json_encode($article['title']) ?>,
      "description": <?= json_encode($article['excerpt']) ?>,
      "wordCount": <?= (int)$wordCount ?>,
      "inLanguage": "id-ID",
      "datePublished": <?= json_encode(date('c', strtotime($article['created_at']))) ?>,
      "dateModified": <?= json_encode(date('c', strtotime($article['created_at']))) ?>,
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "https://hasanarofid.site/blog/<?= $article['slug'] ?>"
      },
      "author": {
        "@type": "Person",
        "name": "Hasan Arofid",
        "url": "https://hasanarofid.site/",
        "jobTitle": "Senior Full-Stack Engineer",
        "sameAs": [
          "https://github.com/hasanarofid",
          "https://linkedin.com/in/hasan-arofid-47869a130/"
        ]
      },
      "publisher": {
        "@type": "Organization",
        "name": "Hasan Arofid",
        "url": "https://hasanarofid.site/",
        "logo": {
          "@type": "ImageObject",
          "url": "https://hasanarofid.site/images/logohasanarofid.png"
        }
      },
      "image": "https://hasanarofid.site/images/hasanarofid.png"
    }
    </script>

    <?php if (!empty($faqSchema)): ?>
    <!-- Schema.org FAQPage Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": <?= json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
    }
    </script>
    <?php endif; ?>

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
          "name": "Artikel",
          "item": "https://hasanarofid.site/articles"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": <?= json_encode($article['title']) ?>,
          "item": "https://hasanarofid.site/blog/<?= $article['slug'] ?>"
        }
      ]
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: rgba(0,0,0,0.08);
            --text: #0f172a;
            --text-muted: #475569;
            --accent: #2563eb;
            --accent-light: #dbeafe;
            --code-bg: #0f172a;
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
            --code-bg: #030712;
        }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.8; margin: 0; padding: 0; }
        .container { max-width: 860px; margin: 0 auto; padding: 0 24px; }
        
        nav { padding: 24px 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: var(--bg); backdrop-filter: blur(12px); z-index: 100; }
        .logo { font-weight: 800; font-size: 1.4rem; text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px; }
        .logo img { height: 36px; }
        .back-link { color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 0.95rem; transition: 0.2s; }
        .back-link:hover { color: var(--accent); }
        
        article { padding: 40px 0 100px; }
        .article-header { margin-bottom: 36px; text-align: left; }
        .article-badges { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
        .badge-category { background: var(--accent-light); color: var(--accent); padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-reading { color: var(--text-muted); font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 4px; }
        .article-header h1 { font-family: 'Poppins', sans-serif; font-size: clamp(2rem, 4.5vw, 3.2rem); line-height: 1.25; margin: 0 0 20px; font-weight: 800; }
        .article-meta-row { display: flex; align-items: center; gap: 14px; color: var(--text-muted); font-size: 0.95rem; padding-bottom: 24px; border-bottom: 1px solid var(--border); }
        .author-mini-img { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); }
        
        /* Table of Contents Box */
        .toc-box { background: var(--surface); border: 1px solid var(--border); border-left: 4px solid var(--accent); border-radius: 16px; padding: 24px 28px; margin: 36px 0 48px; box-shadow: 0 8px 24px rgba(0,0,0,0.03); }
        .toc-header { font-family: 'Poppins', sans-serif; font-size: 1.15rem; font-weight: 700; margin: 0 0 16px; display: flex; justify-content: space-between; align-items: center; }
        .toc-list { list-style: none; padding: 0; margin: 0; }
        .toc-list li { margin-bottom: 10px; line-height: 1.5; }
        .toc-list li.toc-level-3 { padding-left: 20px; font-size: 0.95rem; }
        .toc-list a { color: var(--text-muted); text-decoration: none; transition: 0.2s; }
        .toc-list a:hover { color: var(--accent); text-decoration: underline; }

        /* Article Typography */
        .article-content { font-size: 1.15rem; color: var(--text); }
        .article-content h2 { margin-top: 52px; margin-bottom: 18px; font-family: 'Poppins', sans-serif; font-size: 1.85rem; font-weight: 700; line-height: 1.3; color: var(--text); border-bottom: 1px solid var(--border); padding-bottom: 10px; }
        .article-content h3 { margin-top: 36px; margin-bottom: 14px; font-family: 'Poppins', sans-serif; font-size: 1.4rem; font-weight: 600; line-height: 1.4; color: var(--text); }
        .article-content p { margin-bottom: 24px; font-size: 1.12rem; }
        .article-content img { max-width: 100%; border-radius: 16px; margin: 32px 0; box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
        .article-content ul, .article-content ol { margin-bottom: 28px; padding-left: 28px; }
        .article-content li { margin-bottom: 12px; }
        .article-content strong { font-weight: 700; color: var(--text); }
        .article-content blockquote { border-left: 4px solid var(--accent); background: var(--surface); padding: 20px 24px; font-style: normal; border-radius: 0 16px 16px 0; color: var(--text); margin: 32px 0; box-shadow: 0 4px 16px rgba(0,0,0,0.02); }
        
        /* Code Box */
        .code-container { position: relative; margin: 28px 0; border-radius: 14px; overflow: hidden; background: var(--code-bg); }
        .article-content pre { margin: 0; padding: 20px 24px; overflow-x: auto; font-family: 'JetBrains Mono', monospace; font-size: 0.95rem; color: #f8fafc; line-height: 1.6; }
        .article-content code { font-family: 'JetBrains Mono', monospace; font-size: 0.92em; background: rgba(125,125,125,0.12); padding: 2px 6px; border-radius: 6px; color: var(--accent); }
        .article-content pre code { background: transparent; padding: 0; color: inherit; }
        .btn-copy-code { position: absolute; top: 12px; right: 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-copy-code:hover { background: var(--accent); border-color: var(--accent); }

        /* Social Share */
        .share-section { margin-top: 60px; padding: 32px; background: var(--surface); border: 1px solid var(--border); border-radius: 20px; text-align: center; }
        .share-buttons { display: flex; justify-content: center; gap: 14px; margin-top: 16px; flex-wrap: wrap; }
        .share-btn { padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 700; color: white; font-size: 0.9rem; transition: 0.3s; }
        .btn-wa { background: #25d366; }
        .btn-tw { background: #1da1f2; }
        .btn-li { background: #0077b5; }
        .btn-copy { background: #64748b; cursor: pointer; border: none; font-family: inherit; }
        .share-btn:hover { transform: translateY(-2px); filter: brightness(1.1); }

        /* E-E-A-T Author Box */
        .author-box { margin-top: 48px; padding: 32px; background: var(--surface); border: 1px solid var(--border); border-radius: 20px; display: flex; align-items: center; gap: 24px; text-align: left; }
        .author-box img { width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); flex-shrink: 0; }
        .author-info h4 { margin: 0 0 6px; font-family: 'Poppins', sans-serif; font-size: 1.25rem; font-weight: 700; }
        .author-info p { margin: 0 0 12px; color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; }
        .author-links a { color: var(--accent); text-decoration: none; font-weight: 600; font-size: 0.9rem; margin-right: 18px; }
        .author-links a:hover { text-decoration: underline; }

        /* Related Articles */
        .related-section { margin-top: 60px; }
        .related-section h3 { font-family: 'Poppins', sans-serif; font-size: 1.6rem; margin-bottom: 24px; text-align: center; font-weight: 700; }
        .related-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
        .related-card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 24px; text-decoration: none; color: inherit; display: flex; flex-direction: column; justify-content: space-between; transition: 0.3s; }
        .related-card:hover { transform: translateY(-4px); border-color: var(--accent); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }
        .related-card h4 { margin: 0 0 12px; font-size: 1.1rem; line-height: 1.4; color: var(--text); }
        .related-card p { color: var(--text-muted); font-size: 0.85rem; margin: 0 0 16px; line-height: 1.5; flex-grow: 1; }
        .related-meta { color: var(--accent); font-size: 0.85rem; font-weight: 700; }

        .cta-box { margin-top: 50px; padding: 40px; background: var(--gradient); border-radius: 24px; color: white; text-align: center; }
        .cta-box h3 { margin: 0 0 12px; font-size: 1.6rem; font-family: 'Poppins', sans-serif; }
        .cta-box p { margin: 0 0 24px; opacity: 0.9; }
        .cta-box .btn-white { background: white; color: var(--accent); padding: 12px 32px; border-radius: 12px; text-decoration: none; font-weight: 700; display: inline-block; transition: 0.3s; }
        .cta-box .btn-white:hover { transform: scale(1.03); }

        footer { padding: 60px 0; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); }

        @media (max-width: 640px) {
            .author-box { flex-direction: column; text-align: center; }
            .article-meta-row { flex-direction: column; align-items: flex-start; gap: 8px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <a href="/" class="logo">
                <img src="/images/logohasanarofid.png" alt="Logo">
                <span>Hasan Arofid</span>
            </a>
            <a href="/articles" class="back-link">← <?= $lang === 'id' ? 'Kembali ke Artikel' : 'Back to Articles' ?></a>
        </nav>

        <article>
            <div class="article-header">
                <div class="article-badges">
                    <span class="badge-category">Arsitektur & Web</span>
                    <span class="badge-reading">⏱️ <?= $readingTime ?> menit baca</span>
                </div>
                <h1><?= htmlspecialchars($article['title']) ?></h1>
                <div class="article-meta-row">
                    <img src="/images/hasanarofid.png" alt="Hasan Arofid" class="author-mini-img">
                    <div>
                        <strong>Hasan Arofid</strong> • Ditulis pada <?= date('d F Y', strtotime($article['created_at'])) ?>
                    </div>
                </div>
            </div>

            <?php if (!empty($toc)): ?>
            <!-- Table of Contents (TOC) -->
            <div class="toc-box">
                <div class="toc-header">
                    <span>📑 Daftar Isi Artikel</span>
                </div>
                <ul class="toc-list">
                    <?php foreach ($toc as $item): ?>
                    <li class="toc-level-<?= $item['level'] ?>">
                        <a href="#<?= htmlspecialchars($item['id']) ?>"><?= htmlspecialchars($item['title']) ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="article-content" id="articleBody">
                <?= $contentWithIds ?>
            </div>

            <!-- E-E-A-T Author Box -->
            <div class="author-box">
                <img src="/images/hasanarofid.png" alt="Hasan Arofid">
                <div class="author-info">
                    <h4>Tentang Penulis: Hasan Arofid</h4>
                    <p><?= $lang === 'id' ? 'Senior Fullstack Software Engineer & Konsultan Web dengan pengalaman 10+ tahun merancang arsitektur sistem skala tinggi, optimasi performa web, dan keamanan aplikasi bisnis.' : 'Senior Fullstack Software Engineer & Web Consultant with 10+ years of experience crafting scalable web architectures, cloud systems, and high-performance business digital assets.' ?></p>
                    <div class="author-links">
                        <a href="https://github.com/hasanarofid" target="_blank" rel="noopener">GitHub Profile →</a>
                        <a href="https://linkedin.com/in/hasan-arofid-47869a130/" target="_blank" rel="noopener">LinkedIn Profile →</a>
                        <a href="/#contact"><?= $lang === 'id' ? 'Konsultasi Proyek' : 'Contact Author' ?> →</a>
                    </div>
                </div>
            </div>

            <div class="share-section">
                <p><strong><?= $lang === 'id' ? 'Bagikan artikel ini kepada rekan developer Anda:' : 'Like this article? Share it with your network:' ?></strong></p>
                <div class="share-buttons">
                    <a href="https://api.whatsapp.com/send?text=Baca artikel teknis menarik ini: <?= urlencode($article['title']) ?> - https://hasanarofid.site/blog/<?= $article['slug'] ?>" target="_blank" class="share-btn btn-wa">WhatsApp</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=https://hasanarofid.site/blog/<?= $article['slug'] ?>" target="_blank" class="share-btn btn-li">LinkedIn</a>
                    <a href="https://twitter.com/intent/tweet?text=<?= urlencode($article['title'] . ' https://hasanarofid.site/blog/' . $article['slug']) ?>" target="_blank" class="share-btn btn-tw">Twitter / X</a>
                    <button onclick="copyLink()" class="share-btn btn-copy" id="copyBtn"><?= $lang === 'id' ? 'Salin Link' : 'Copy Link' ?></button>
                </div>
            </div>

            <?php if (!empty($relatedArticles)): ?>
            <!-- Related Articles (Internal Linking) -->
            <div class="related-section">
                <h3><?= $lang === 'id' ? 'Panduan & Artikel Terkait Lainnya' : 'Related Articles' ?></h3>
                <div class="related-grid">
                    <?php foreach ($relatedArticles as $rel): ?>
                    <a href="/blog/<?= htmlspecialchars($rel['slug']) ?>" class="related-card">
                        <div>
                            <h4><?= htmlspecialchars($rel['title']) ?></h4>
                            <p><?= htmlspecialchars(mb_substr(strip_tags($rel['excerpt'] ?: $rel['title']), 0, 100)) ?>...</p>
                        </div>
                        <span class="related-meta"><?= $lang === 'id' ? 'Baca Panduan →' : 'Read More →' ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="cta-box">
                <h3><?= $lang === 'id' ? 'Butuh Konsultasi Arsitektur Web atau Optimasi SEO?' : 'Need Professional Web Solutions?' ?></h3>
                <p><?= $lang === 'id' ? 'Bangun sistem web cepat, aman, dan siap scale-up bersama engineer berpengalaman.' : 'We help your business grow with the latest technology.' ?></p>
                <a href="/#contact" class="btn-white"><?= $lang === 'id' ? 'Konsultasi Gratis Sekarang' : 'Consult Now' ?></a>
            </div>
        </article>
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

        // Add copy button to pre code blocks
        document.querySelectorAll('pre').forEach(pre => {
            const container = document.createElement('div');
            container.className = 'code-container';
            pre.parentNode.insertBefore(container, pre);
            container.appendChild(pre);

            const copyBtn = document.createElement('button');
            copyBtn.className = 'btn-copy-code';
            copyBtn.innerText = 'Salin Kode';
            copyBtn.addEventListener('click', () => {
                const code = pre.querySelector('code') ? pre.querySelector('code').innerText : pre.innerText;
                navigator.clipboard.writeText(code).then(() => {
                    copyBtn.innerText = 'Tersalin!';
                    setTimeout(() => { copyBtn.innerText = 'Salin Kode'; }, 2000);
                });
            });
            container.appendChild(copyBtn);
        });
    </script>
</body>
</html>

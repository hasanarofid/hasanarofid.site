<?php
$dbFile = __DIR__ . '/stats.db';
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $products = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

session_start();
$lang = $_SESSION['lang'] ?? 'en';
$t = [
    'en' => [
        'title' => 'Digital Products | Hasan Arofid',
        'subtitle' => 'Premium resources and tools for developers and creators.',
        'back' => 'Back to Home',
        'buy' => 'Get it Now',
    ],
    'id' => [
        'title' => 'Produk Digital | Hasan Arofid',
        'subtitle' => 'Sumber daya dan alat premium untuk pengembang dan kreator.',
        'back' => 'Kembali ke Beranda',
        'buy' => 'Dapatkan Sekarang',
    ]
];
$active_t = $t[$lang];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $active_t['title'] ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="images/logohasanarofid.png" />

  <style>
    :root {
      --bg: #f8fafc;
      --surface: #ffffff;
      --border: rgba(0, 0, 0, 0.08);
      --text: #0f172a;
      --text-muted: #475569;
      --accent: #3b82f6;
      --accent-secondary: #8b5cf6;
      --gradient: linear-gradient(135deg, var(--accent), var(--accent-secondary));
    }

    [data-theme="dark"] {
      --bg: #030712;
      --surface: #0f172a;
      --border: rgba(255, 255, 255, 0.08);
      --text: #f8fafc;
      --text-muted: #94a3b8;
    }


    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.6;
      margin: 0;
      padding: 0;
    }

    .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
    
    nav {
      padding: 32px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo { 
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 800; 
        font-size: 1.5rem; 
        background: var(--gradient); 
        -webkit-background-clip: text; 
        background-clip: text; 
        -webkit-text-fill-color: transparent; 
        text-decoration: none;
    }
    .logo-img {
        height: 42px;
        width: auto;
        -webkit-text-fill-color: initial;
    }


    .back-link {
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 500;
        transition: 0.3s;
    }
    .back-link:hover { color: var(--text); }

    .theme-toggle {
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--text);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.3s;
        font-size: 1.1rem;
    }
    .theme-toggle:hover { transform: scale(1.1); border-color: var(--accent); }

    header {
        text-align: center;
        padding: 80px 0;
    }
    header h1 {
        font-family: 'Poppins', sans-serif;
        font-size: clamp(2.5rem, 8vw, 4rem);
        margin: 0 0 16px;
    }
    header p {
        color: var(--text-muted);
        font-size: 1.25rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 32px;
        padding-bottom: 120px;
    }

    .product-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 24px;
        overflow: hidden;
        transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }
    .product-card:hover {
        transform: translateY(-10px);
        border-color: var(--accent);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }

    .product-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: #1e293b;
    }

    .product-content {
        padding: 32px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-tag {
        display: inline-block;
        padding: 4px 12px;
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent);
        border-radius: 99px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    .product-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        margin: 0 0 12px;
    }

    .product-desc {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin-bottom: 24px;
        flex-grow: 1;
    }

    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }

    .product-price {
        font-weight: 700;
        font-size: 1.25rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
        background: var(--gradient);
        color: white;
    }
    .btn:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
    }

    footer {
        padding: 60px 0;
        text-align: center;
        border-top: 1px solid var(--border);
        color: var(--text-muted);
    }

    @media (max-width: 768px) {
        .products-grid { grid-template-columns: 1fr; }
        header h1 { font-size: 2.5rem; }
        .logo { font-size: 1.2rem; gap: 8px; }
        .logo-img { height: 32px; }
    }

  </style>
</head>
<body>
  <div class="container">
    <nav>
      <a href="index.php" class="logo">
        <img src="images/logohasanarofid.png" alt="Hasan Arofid Logo" class="logo-img">
        <span>Hasan Arofid</span>
      </a>

      <div style="display: flex; align-items: center; gap: 20px;">
        <div class="theme-toggle" id="themeToggle">🌓</div>
        <a href="index.php" class="back-link">← <?= $active_t['back'] ?></a>
      </div>
    </nav>

    <header>
      <h1><?= $active_t['title'] ?></h1>
      <p><?= $active_t['subtitle'] ?></p>
    </header>

    <div class="products-grid">
      <?php foreach ($products as $p): ?>
        <div class="product-card">
          <?php if ($p['image_url']): ?>
            <img src="<?= htmlspecialchars($p['image_url']) ?>" class="product-img" alt="<?= htmlspecialchars($p['name']) ?>">
          <?php else: ?>
            <div class="product-img" style="display: flex; align-items: center; justify-content: center; font-size: 3rem;">📦</div>
          <?php endif; ?>
          <div class="product-content">
            <span class="product-tag"><?= strtoupper(htmlspecialchars($p['platform'])) ?></span>
            <h2 class="product-title"><?= htmlspecialchars($p['name']) ?></h2>
            <p class="product-desc"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
            <div class="product-footer">
              <span class="product-price"><?= htmlspecialchars($p['price']) ?></span>
              <a href="<?= htmlspecialchars($p['link']) ?>" target="_blank" class="btn"><?= $active_t['buy'] ?></a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <footer>
    <div class="container">
      <p>&copy; <?= date('Y') ?> Hasan Arofid. All rights reserved.</p>
    </div>
  </footer>

  <script>
    // Theme Logic
    const themeToggle = document.getElementById('themeToggle');
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeToggle.textContent = '☀️';
    } else {
        themeToggle.textContent = '🌓';
    }

    themeToggle.addEventListener('click', () => {
        const theme = document.documentElement.getAttribute('data-theme');
        if (theme === 'dark') {
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
            themeToggle.textContent = '🌓';
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            themeToggle.textContent = '☀️';
        }
    });

  </script>
</body>
</html>

<?php
// Visitor Tracking Logic
$dbFile = __DIR__ . '/stats.db';
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $db->exec("CREATE TABLE IF NOT EXISTS visits (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        visit_date DATE DEFAULT (date('now')),
        ip_hash TEXT,
        ip_address TEXT,
        user_agent TEXT,
        country TEXT,
        city TEXT,
        is_bot INTEGER DEFAULT 0,
        device_type TEXT,
        created_at DATETIME DEFAULT (datetime('now'))
    )");

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ipHash = hash('sha256', $ip);
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $isBot = 0;
    if (preg_match('/(googlebot|bingbot|slurp|duckduckbot|baiduspider|yandexbot|sogou|exabot|facebookexternalhit|ia_archiver)/i', $ua)) {
        $isBot = 1;
    }

    $device = 'Desktop';
    if (preg_match('/(android|iphone|ipad|mobile)/i', $ua)) {
        $device = 'Mobile';
    }

    $country = 'Unknown';
    $city = 'Unknown';
    
    if ($ip !== '127.0.0.1' && $ip !== '::1' && !empty($ip)) {
        $ctx = stream_context_create(['http' => ['timeout' => 1]]);
        $geo = @file_get_contents("http://ip-api.com/json/$ip?fields=status,country,city", false, $ctx);
        if ($geo) {
            $geoData = json_decode($geo, true);
            if (($geoData['status'] ?? '') === 'success') {
                $country = $geoData['country'] ?? 'Unknown';
                $city = $geoData['city'] ?? 'Unknown';
            }
        }
    }

    $stmt = $db->prepare("INSERT INTO visits (ip_hash, ip_address, user_agent, country, city, is_bot, device_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$ipHash, $ip, $ua, $country, $city, $isBot, $device]);

    // Language Detection
    session_start();
    if (isset($_GET['lang'])) {
        $_SESSION['lang'] = $_GET['lang'] === 'id' ? 'id' : 'en';
    }

    if (!isset($_SESSION['lang'])) {
        if ($country === 'Indonesia') {
            $_SESSION['lang'] = 'id';
        } else {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
            $_SESSION['lang'] = ($browserLang === 'id') ? 'id' : 'en';
        }
    }
    $lang = $_SESSION['lang'];

    $translations = [
        'en' => [
            'meta_title' => 'Hasan Arofid | Senior Fullstack Engineer & Systems Architect',
            'meta_desc' => 'Senior Fullstack Engineer with 10+ years of experience building scalable, production-ready systems for enterprises and global startups.',
            'hero_head' => 'I build scalable, <span>production-ready</span> systems.',
            'hero_sub' => 'Senior Fullstack Engineer specializing in high-performance backend architecture, reliable system design, and enterprise-grade reliability.',
            'cta_book' => 'Book a Call',
            'cta_projects' => 'View Projects',
            'trust_exp' => '10+ Years Experience',
            'trust_projects' => '50+ Projects Delivered',
            'trust_uptime' => '99.9% System Reliability',
            'about_title' => 'The Mindset Behind the Code',
            'about_story' => 'With over a decade in the trenches, I don\'t just write features—I solve business problems. My focus is on high-availability systems that survive real-world production stress and scale elegantly as you grow.',
            'expertise_title' => 'Core Expertise',
            'exp_backend' => 'Backend Architecture',
            'exp_backend_desc' => 'Designing distributed systems that are fast, secure, and maintainable.',
            'exp_api' => 'API & Integrations',
            'exp_api_desc' => 'Complex API orchestration and third-party ecosystem connectivity.',
            'exp_scaling' => 'System Scaling',
            'exp_scaling_desc' => 'Optimizing performance for high-traffic applications and large datasets.',
            'exp_devops' => 'DevOps & CI/CD',
            'exp_devops_desc' => 'Automating deployments and ensuring robust production observability.',
            'how_work_title' => 'How I Work',
            'how_work_step1' => 'Discovery & Deep Audit',
            'how_work_step2' => 'Strategic Architecture Design',
            'how_work_step3' => 'Resilient Implementation',
            'how_work_step4' => 'Optimization & Scaling',
            'systems_title' => 'Systems I\'ve Built',
            'systems_desc' => 'Highlighting technical complexity and business impact for enterprise grade solutions.',
            'project_impact' => 'Impact',
            'project_problem' => 'Problem',
            'project_solution' => 'Solution',
            'tech_title' => 'Trusted Tech Stack',
            'cta_foot' => 'Let’s build something impactful.',
            'products_title' => 'Digital Products',
            'products_subtitle' => 'Premium tools and resources to accelerate your development workflow.',
            'more_products' => 'View All Products',
            'footer_copy' => '© ' . date('Y') . ' Hasan Arofid. Built for performance and reliability.',
        ],
        'id' => [
            'meta_title' => 'Hasan Arofid | Senior Fullstack Engineer & Arsitek Sistem',
            'meta_desc' => 'Senior Fullstack Engineer dengan 10+ tahun pengalaman membangun sistem skala besar yang siap produksi untuk perusahaan dan startup global.',
            'hero_head' => 'Saya membangun sistem skala besar yang <span>siap produksi</span>.',
            'hero_sub' => 'Senior Fullstack Engineer spesialis arsitektur backend performa tinggi, desain sistem handal, dan reliabilitas kelas enterprise.',
            'cta_book' => 'Jadwalkan Konsultasi',
            'cta_projects' => 'Lihat Proyek',
            'trust_exp' => '10+ Tahun Pengalaman',
            'trust_projects' => '50+ Proyek Selesai',
            'trust_uptime' => '99.9% Reliabilitas Sistem',
            'about_title' => 'Mindset di Balik Kode',
            'about_story' => 'Lebih dari satu dekade di industri, saya tidak hanya menulis fitur—saya menyelesaikan masalah bisnis. Fokus saya adalah sistem ketersediaan tinggi yang mampu bertahan di stres produksi nyata.',
            'expertise_title' => 'Keahlian Utama',
            'exp_backend' => 'Arsitektur Backend',
            'exp_backend_desc' => 'Merancang sistem terdistribusi yang cepat, aman, dan mudah dikelola.',
            'exp_api' => 'API & Integrasi',
            'exp_api_desc' => 'Orkestrasi API kompleks dan konektivitas ekosistem pihak ketiga.',
            'exp_scaling' => 'Skalabilitas Sistem',
            'exp_scaling_desc' => 'Optimasi performa untuk aplikasi traffic tinggi dan data skala besar.',
            'exp_devops' => 'DevOps & CI/CD',
            'exp_devops_desc' => 'Otomasi deployment dan memastikan observabilitas produksi yang kuat.',
            'how_work_title' => 'Bagaimana Saya Bekerja',
            'how_work_step1' => 'Discovery & Audit Mendalam',
            'how_work_step2' => 'Desain Arsitektur Strategis',
            'how_work_step3' => 'Implementasi Resilien',
            'how_work_step4' => 'Optimasi & Skalabilitas',
            'systems_title' => 'Sistem Yang Saya Bangun',
            'systems_desc' => 'Menyoroti kompleksitas teknis dan dampak bisnis untuk solusi kelas enterprise.',
            'project_impact' => 'Dampak',
            'project_problem' => 'Masalah',
            'project_solution' => 'Solusi',
            'tech_title' => 'Tech Stack Terpercaya',
            'cta_foot' => 'Mari bangun sesuatu yang berdampak.',
            'products_title' => 'Produk Digital',
            'products_subtitle' => 'Alat dan sumber daya premium untuk mempercepat workflow pengembangan Anda.',
            'more_products' => 'Lihat Semua Produk',
            'footer_copy' => '© ' . date('Y') . ' Hasan Arofid. Dibangun untuk performa dan reliabilitas.',
        ]
    ];
    $t = $translations[$lang];

    // Fetch latest products
    $stmtProducts = $db->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT 3");
    $stmtProducts->execute();
    $latestProducts = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $latestProducts = []; }
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $t['meta_title'] ?></title>

  <meta name="description" content="<?= $t['meta_desc'] ?>" />
  <meta name="keywords" content="fullstack engineer, web architecture, Laravel, Node.js, React, System Scaling, Enterprise Software" />

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://hasanarofid.site/" />
  <meta property="og:title" content="<?= $t['meta_title'] ?>" />
  <meta property="og:description" content="<?= $t['meta_desc'] ?>" />
  <meta property="og:image" content="https://hasanarofid.site/images/hasanarofid.png" />

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image" />
  <meta property="twitter:url" content="https://hasanarofid.site/" />
  <meta property="twitter:title" content="<?= $t['meta_title'] ?>" />
  <meta property="twitter:description" content="<?= $t['meta_desc'] ?>" />
  <meta property="twitter:image" content="https://hasanarofid.site/images/hasanarofid.png" />
  <link rel="canonical" href="https://hasanarofid.site/" />

  <!-- GEO Tags -->
  <meta name="geo.region" content="ID-JI" />
  <meta name="geo.placename" content="Surabaya" />
  <meta name="geo.position" content="-7.323285;112.727786" />
  <meta name="ICBM" content="-7.323285, 112.727786" />

  <!-- Schema.org Markup -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Person",
    "name": "Hasan Arofid",
    "url": "https://hasanarofid.site/",
    "image": "https://hasanarofid.site/images/hasanarofid.png",
    "sameAs": [
      "https://github.com/hasanarofid",
      "https://www.linkedin.com/in/hasan-arofid-47869a130/",
      "https://www.instagram.com/hasanarofid/"
    ],
    "jobTitle": "Senior Fullstack Engineer",
    "worksFor": {
      "@type": "Organization",
      "name": "Freelance / Self-Employed"
    },
    "description": "Senior Fullstack Engineer with 10+ years of experience building scalable, production-ready systems."
  }
  </script>

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ProfessionalService",
    "name": "Hasan Arofid - Systems Architect",
    "image": "https://hasanarofid.site/images/hasanarofid.png",
    "@id": "https://hasanarofid.site/",
    "url": "https://hasanarofid.site/",
    "telephone": "+6285767113554",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Ketintang",
      "addressLocality": "Surabaya",
      "postalCode": "60231",
      "addressCountry": "ID"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": -7.323285,
      "longitude": 112.727786
    }
  }
  </script>


  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --bg: #030712;
      --surface: #0f172a;
      --surface-hover: #1e293b;
      --border: rgba(255, 255, 255, 0.08);
      --text: #f8fafc;
      --text-muted: #94a3b8;
      --accent: #3b82f6;
      --accent-secondary: #8b5cf6;
      --gradient: linear-gradient(135deg, var(--accent), var(--accent-secondary));
      --glass: rgba(15, 23, 42, 0.8);
    }

    [data-theme="light"] {
      --bg: #f8fafc;
      --surface: #ffffff;
      --surface-hover: #f1f5f9;
      --border: rgba(0, 0, 0, 0.08);
      --text: #0f172a;
      --text-muted: #475569;
      --glass: rgba(255, 255, 255, 0.8);
    }

    * { box-sizing: border-box; scroll-behavior: smooth; }
    html, body {
      margin: 0;
      padding: 0;
      width: 100%;
      overflow-x: hidden;
      position: relative;
    }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.6;
    }

    h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; margin: 0; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; position: relative; z-index: 2; }

    nav {
      position: fixed; top: 0; left: 0; width: 100%; height: 72px;
      background: var(--glass); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border); z-index: 1000;
      display: flex; align-items: center;
    }
    .nav-content { display: flex; justify-content: space-between; align-items: center; width: 100%; }
    .logo { 
        font-weight: 800; 
        font-size: 1.5rem; 
        letter-spacing: -0.03em;
        background: var(--gradient); 
        -webkit-background-clip: text; 
        background-clip: text; 
        -webkit-text-fill-color: transparent; 
        text-decoration: none;
        transition: 0.3s;
    }
    .logo:hover { opacity: 0.8; transform: scale(0.98); }

    .nav-links { display: flex; gap: 32px; align-items: center; }
    .nav-links a { 
        color: var(--text-muted); 
        text-decoration: none; 
        font-size: 0.95rem; 
        font-weight: 500; 
        transition: 0.2s;
        position: relative;
    }
    .nav-links a::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--gradient);
        transition: 0.3s;
    }
    .nav-links a:hover { color: var(--text); }
    .nav-links a:hover::after { width: 100%; }

    .lang-toggle { background: var(--surface); border: 1px solid var(--border); color: var(--text); padding: 4px 14px; border-radius: 999px; font-size: 0.85rem; cursor: pointer; text-decoration: none; font-weight: 600; }

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

    /* Mobile Menu Toggle */
    .menu-toggle {
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        z-index: 1100;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: var(--surface);
        border: 1px solid var(--border);
    }
    .menu-toggle span {
        width: 20px;
        height: 2px;
        background: var(--text);
        transition: 0.3s;
        border-radius: 2px;
    }



    .scroll-progress { position: fixed; top: 0; left: 0; height: 3px; background: var(--gradient); z-index: 1001; width: 0%; transition: width 0.1s; }

    .btn { display: inline-flex; align-items: center; padding: 12px 28px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: 0.3s; cursor: pointer; }
    .btn-primary { background: var(--gradient); color: white; border: none; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3); }
    .btn-outline { border: 1px solid var(--border); color: var(--text); background: rgba(255,255,255,0.05); }
    .btn-outline:hover { background: rgba(255,255,255,0.1); border-color: var(--accent); }

    section { padding: 120px 0; }
    .section-title { font-size: 2.5rem; margin-bottom: 16px; text-align: center; }
    .section-subtitle { color: var(--text-muted); text-align: center; max-width: 700px; margin: 0 auto 64px; font-size: 1.1rem; }

    [data-reveal] { opacity: 0; transform: translateY(20px); transition: 0.8s cubic-bezier(0.2, 0, 0, 1); }
    [data-reveal].active { opacity: 1; transform: translateY(0); }

    .hero { min-height: 100vh; display: flex; align-items: center; position: relative; overflow: hidden; padding-top: 72px; }
    .hero-content { max-width: 850px; }
    .hero-title { font-size: clamp(2.2rem, 10vw, 4.2rem); line-height: 1.1; margin-bottom: 24px; word-wrap: break-word; }

    .hero-title span { background: var(--gradient); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    .hero-sub { font-size: 1.25rem; color: var(--text-muted); margin-bottom: 40px; max-width: 650px; }
    .hero-actions { display: flex; gap: 16px; flex-wrap: wrap; }
    .hero-bg { position: absolute; inset: 0; z-index: 1; background: radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.1), transparent 40%); }

    .trust-bar { padding: 48px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: rgba(255,255,255,0.01); }
    .trust-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 24px; }

    .trust-item { text-align: center; }
    .trust-item h4 { font-size: 2.5rem; margin-bottom: 8px; background: var(--gradient); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    .trust-item p { color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

    .exp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; }
    .exp-card { padding: 48px; background: var(--surface); border: 1px solid var(--border); border-radius: 24px; transition: 0.3s; }
    .exp-card:hover { border-color: var(--accent); transform: translateY(-5px); }
    .exp-icon { font-size: 2rem; margin-bottom: 24px; display: block; }

    .process-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 32px; margin-top: 48px; }
    .process-item { position: relative; padding-left: 32px; border-left: 2px solid var(--border); }
    .process-item::before { content: ''; position: absolute; left: -7px; top: 0; width: 12px; height: 12px; border-radius: 50%; background: var(--accent); }

    .projects-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 350px), 1fr)); gap: 24px; }

    .project-card { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; transition: 0.4s; }
    .project-card:hover { border-color: var(--accent); transform: translateY(-8px); }
    .project-img { width: 100%; height: 260px; object-fit: cover; cursor: zoom-in; transition: 0.3s; }
    .project-img:hover { opacity: 0.8; }
    .project-content { padding: 32px; }
    .project-tag { display: inline-block; padding: 4px 12px; background: rgba(59, 130, 246, 0.1); color: var(--accent); border-radius: 99px; font-size: 0.8rem; font-weight: 600; margin-bottom: 16px; }

    /* Lightbox */
    .lightbox {
        position: fixed;
        inset: 0;
        background: rgba(3, 7, 18, 0.95);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        padding: 40px;
        backdrop-filter: blur(8px);
    }
    .lightbox.active { display: flex; }
    .lightbox img {
        max-width: 100%;
        max-height: 90vh;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    .lightbox-close {
        position: absolute;
        top: 24px;
        right: 24px;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        opacity: 0.7;
    }
    .lightbox-close:hover { opacity: 1; }


    .marquee { overflow: hidden; white-space: nowrap; padding: 64px 0; background: rgba(255,255,255,0.02); }
    .marquee-content { display: inline-block; animation: marquee 40s linear infinite; }
    .marquee-content span { font-size: 1.8rem; font-weight: 800; margin-right: 64px; color: var(--text-muted); opacity: 0.3; }
    @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    footer { padding: 80px 0; border-top: 1px solid var(--border); color: var(--text-muted); text-align: center; }
    .social-links { display: flex; justify-content: center; gap: 32px; margin-top: 32px; }
    .social-links a { color: var(--text-muted); text-decoration: none; font-weight: 500; transition: 0.2s; }
    .social-links a:hover { color: var(--accent); }

    .contact-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-input { padding: 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface); color: white; width: 100%; font-family: inherit; }
    .form-input:focus { outline: none; border-color: var(--accent); }


    @media (max-width: 768px) {
      .hero-title { font-size: clamp(2rem, 12vw, 2.5rem); }

      
      .menu-toggle { display: flex; }
      
      .nav-links {
          position: fixed;
          top: 0;
          right: 0;
          width: 80%;
          height: 100vh;
          background: #030712;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          gap: 40px;
          transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
          z-index: 1050;
          border-left: 1px solid var(--border);
          box-shadow: -20px 0 40px rgba(0,0,0,0.5);
          transform: translateX(120%);
          display: flex;
      }
      
      .nav-links.active { transform: translateX(0); }

      
      .nav-links a { font-size: 1.5rem; }

      .menu-toggle.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
      .menu-toggle.active span:nth-child(2) { opacity: 0; }
      .menu-toggle.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }


      section { padding: 80px 0; }
      .projects-grid { grid-template-columns: 1fr; }
      .contact-form-grid { grid-template-columns: 1fr; }
    }


  </style>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7190047001129861"
     crossorigin="anonymous"></script>
</head>
<body>
  <div class="scroll-progress" id="scrollProgress"></div>
  
  <div class="lightbox" id="imageLightbox">
    <span class="lightbox-close">&times;</span>
    <img src="" alt="Preview">
  </div>
  
  <nav>
    <div class="container nav-content">
      <a href="#" class="logo">Hasan Arofid</a>
      
      <div class="nav-links" id="navLinks">
        <a href="#about"><?= $lang === 'id' ? 'Tentang' : 'About' ?></a>
        <a href="#expertise"><?= $lang === 'id' ? 'Keahlian' : 'Expertise' ?></a>
        <a href="#projects"><?= $lang === 'id' ? 'Proyek' : 'Projects' ?></a>
        <a href="#digital-products"><?= $lang === 'id' ? 'Produk' : 'Products' ?></a>
        <a href="#contact"><?= $lang === 'id' ? 'Kontak' : 'Contact' ?></a>
        <div class="theme-toggle" id="themeToggle">🌓</div>
        <a href="?lang=<?= $lang === 'id' ? 'en' : 'id' ?>" class="lang-toggle"><?= $lang === 'id' ? 'EN' : 'ID' ?></a>
      </div>

      <div class="menu-toggle" id="mobileMenuToggle">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </nav>



  <section class="hero">
    <div class="hero-bg"></div>
    <div class="container">
      <div class="hero-content" data-reveal>
        <h1 class="hero-title"><?= $t['hero_head'] ?></h1>
        <p class="hero-sub"><?= $t['hero_sub'] ?></p>
        <div class="hero-actions">
          <a href="#contact" class="btn btn-primary"><?= $t['cta_book'] ?></a>
          <a href="#projects" class="btn btn-outline"><?= $t['cta_projects'] ?></a>
        </div>
      </div>
    </div>
  </section>

  <div class="trust-bar">
    <div class="container">
      <div class="trust-grid">
        <div class="trust-item" data-reveal>
          <h4>10+</h4>
          <p><?= $t['trust_exp'] ?></p>
        </div>
        <div class="trust-item" data-reveal data-delay="100">
          <h4>50+</h4>
          <p><?= $t['trust_projects'] ?></p>
        </div>
        <div class="trust-item" data-reveal data-delay="200">
          <h4>99.9%</h4>
          <p><?= $t['trust_uptime'] ?></p>
        </div>
      </div>
    </div>
  </div>

  <section id="about">
    <div class="container">
      <div class="section-title" data-reveal><?= $t['about_title'] ?></div>
      <div class="section-subtitle" data-reveal><?= $t['about_story'] ?></div>
      
      <div class="process-grid">
        <div class="process-item" data-reveal>
          <h3>01</h3>
          <p><strong><?= $t['how_work_step1'] ?></strong></p>
        </div>
        <div class="process-item" data-reveal data-delay="100">
          <h3>02</h3>
          <p><strong><?= $t['how_work_step2'] ?></strong></p>
        </div>
        <div class="process-item" data-reveal data-delay="200">
          <h3>03</h3>
          <p><strong><?= $t['how_work_step3'] ?></strong></p>
        </div>
        <div class="process-item" data-reveal data-delay="300">
          <h3>04</h3>
          <p><strong><?= $t['how_work_step4'] ?></strong></p>
        </div>
      </div>
    </div>
  </section>

  <section id="expertise" style="background: rgba(255,255,255,0.01);">
    <div class="container">
      <div class="section-title" data-reveal><?= $t['expertise_title'] ?></div>
      <div class="exp-grid">
        <div class="exp-card" data-reveal>
          <span class="exp-icon">🏗️</span>
          <h3><?= $t['exp_backend'] ?></h3>
          <p><?= $t['exp_backend_desc'] ?></p>
        </div>
        <div class="exp-card" data-reveal data-delay="100">
          <span class="exp-icon">🔗</span>
          <h3><?= $t['exp_api'] ?></h3>
          <p><?= $t['exp_api_desc'] ?></p>
        </div>
        <div class="exp-card" data-reveal data-delay="200">
          <span class="exp-icon">⚖️</span>
          <h3><?= $t['exp_scaling'] ?></h3>
          <p><?= $t['exp_scaling_desc'] ?></p>
        </div>
        <div class="exp-card" data-reveal data-delay="300">
          <span class="exp-icon">🛡️</span>
          <h3><?= $t['exp_devops'] ?></h3>
          <p><?= $t['exp_devops_desc'] ?></p>
        </div>
      </div>
    </div>
  </section>

  <section id="projects">
    <div class="container">
      <div class="section-title" data-reveal><?= $t['systems_title'] ?></div>
      <div class="section-subtitle" data-reveal><?= $t['systems_desc'] ?></div>
      
      <div class="projects-grid">
        <div class="project-card" data-reveal onclick="window.open('https://amtechev.com/', '_blank')">
          <img src="images/amtechev.png" class="project-img" alt="Amtech EV">
          <div class="project-content">
            <span class="project-tag">Enterprise SaaS</span>
            <h3>Amtech EV Infrastructure</h3>
            <p>Built a unified management system for Malaysia's leading EV network. Handling real-time hardware telemetry and multi-tenant billing.</p>
            <div style="margin-top: 16px; font-weight: 600; color: var(--accent);"><?= $t['project_impact'] ?>: 100% Automated Billing & Monitoring</div>
          </div>
        </div>
        <div class="project-card" data-reveal data-delay="100">
          <img src="images/porto3.png" class="project-img preview-trigger" alt="School oversight system preview">
          <div class="project-content">
            <span class="project-tag">Laravel</span>
            <h3>School Oversight System</h3>
            <p>Compliance and supervision system for schools with audit trails, scheduling, and actionable dashboards.</p>
            <div style="margin-top: 16px; font-weight: 600; color: var(--accent);"><?= $t['project_impact'] ?>: 40% Operational Efficiency Increase</div>
          </div>
        </div>

        <div class="project-card" data-reveal data-delay="200">
          <img src="images/nitajaya.png" class="project-img preview-trigger" alt="Nitajaya system preview">
          <div class="project-content">
            <span class="project-tag">PHP • POS</span>
            <h3>Nitajaya Catering & POS</h3>
            <p>Integrated catering management and point-of-sale system. Streamlining order processing and inventory tracking.</p>
            <div style="margin-top: 16px; font-weight: 600; color: var(--accent);"><?= $t['project_impact'] ?>: Optimized Order Lifecycle</div>
          </div>
        </div>



      </div>
    </div>
  </section>

  <?php if (!empty($latestProducts)): ?>
  <section id="digital-products" style="background: rgba(255,255,255,0.01);">
    <div class="container">
      <div class="section-title" data-reveal><?= $t['products_title'] ?></div>
      <div class="section-subtitle" data-reveal><?= $t['products_subtitle'] ?></div>
      
      <div class="projects-grid">
        <?php foreach ($latestProducts as $p): ?>
          <div class="project-card" data-reveal>
            <?php if ($p['image_url']): ?>
              <img src="<?= htmlspecialchars($p['image_url']) ?>" class="project-img" alt="<?= htmlspecialchars($p['name']) ?>">
            <?php else: ?>
              <div class="project-img" style="display: flex; align-items: center; justify-content: center; font-size: 3rem; background: #1e293b;">📦</div>
            <?php endif; ?>
            <div class="project-content">
              <span class="project-tag"><?= strtoupper(htmlspecialchars($p['platform'])) ?></span>
              <h3><?= htmlspecialchars($p['name']) ?></h3>
              <p><?= htmlspecialchars(substr($p['description'], 0, 100)) . (strlen($p['description']) > 100 ? '...' : '') ?></p>
              <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; color: var(--accent);"><?= htmlspecialchars($p['price']) ?></span>
                <a href="<?= htmlspecialchars($p['link']) ?>" target="_blank" style="color: var(--text); text-decoration: none; font-weight: 600; font-size: 0.9rem;"><?= $lang === 'id' ? 'Beli Sekarang' : 'Buy Now' ?> →</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div style="text-align: center; margin-top: 64px;" data-reveal>
        <a href="products.php" class="btn btn-outline"><?= $t['more_products'] ?></a>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <div class="marquee">
    <div class="marquee-content">
      <span>LARAVEL</span><span>NODE.JS</span><span>REACT</span><span>POSTGRESQL</span><span>DOCKER</span><span>REDIS</span><span>TYPESCRIPT</span>
      <span>LARAVEL</span><span>NODE.JS</span><span>REACT</span><span>POSTGRESQL</span><span>DOCKER</span><span>REDIS</span><span>TYPESCRIPT</span>
    </div>
  </div>

  <section id="contact">
    <div class="container" style="max-width: 700px;">
      <div class="section-title" data-reveal><?= $t['cta_foot'] ?></div>
      <form id="dev-request-form" style="display: grid; gap: 20px; margin-top: 48px;">
        <div class="contact-form-grid">
          <input type="text" name="name" placeholder="<?= $lang === 'id' ? 'Nama' : 'Name' ?>" required class="form-input">
          <input type="email" name="email" placeholder="Email" required class="form-input">
        </div>
        <textarea name="description" placeholder="<?= $lang === 'id' ? 'Ceritakan tantangan teknis Anda...' : 'Tell me about your technical challenge...' ?>" rows="6" required class="form-input"></textarea>
        <input type="hidden" name="service" value="enterprise-consulting">
        <button type="submit" class="btn btn-primary" style="justify-content: center; font-size: 1.1rem; width: 100%;"><?= $lang === 'id' ? 'Kirim Pesan Strategis' : 'Send Strategic Message' ?></button>
      </form>

      <div id="form-status" style="margin-top: 24px; text-align: center; font-weight: 500;"></div>
    </div>
  </section>

  <footer>
    <div class="container">
      <p><?= $t['footer_copy'] ?></p>
      <div class="social-links">
        <a href="https://github.com/hasanarofid" target="_blank">GitHub</a>
        <a href="https://www.linkedin.com/in/hasan-arofid-47869a130/" target="_blank">LinkedIn</a>
        <a href="https://www.instagram.com/hasanarofid/" target="_blank">Instagram</a>
      </div>
    </div>
  </footer>

  <script>
    // Theme Logic
    const themeToggle = document.getElementById('themeToggle');
    const currentTheme = localStorage.getItem('theme') || 'dark';
    
    if (currentTheme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
        themeToggle.textContent = '☀️';
    }

    themeToggle.addEventListener('click', () => {
        const theme = document.documentElement.getAttribute('data-theme');
        if (theme === 'light') {
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('theme', 'dark');
            themeToggle.textContent = '🌓';
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
            themeToggle.textContent = '☀️';
        }
    });

    // Scroll Progress
    window.addEventListener('scroll', () => {
      const winScroll = document.documentElement.scrollTop;
      const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      const scrolled = (winScroll / height) * 100;
      document.getElementById("scrollProgress").style.width = scrolled + "%";
    });

    // Mobile Menu Toggle
    const menuToggle = document.getElementById('mobileMenuToggle');
    const navLinks = document.getElementById('navLinks');
    
    menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('active');
        navLinks.classList.toggle('active');
        document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : '';
    });

    // Close menu when link is clicked
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('active');
            navLinks.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // Reveal Animations

    const observerOptions = { threshold: 0.1 };
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const delay = entry.target.getAttribute('data-delay') || 0;
          setTimeout(() => { entry.target.classList.add('active'); }, delay);
        }
      });
    }, observerOptions);
    document.querySelectorAll('[data-reveal]').forEach(el => observer.observe(el));

    // Form Handler
    const form = document.getElementById('dev-request-form');
    const status = document.getElementById('form-status');
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = form.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.textContent = '<?= $lang === 'id' ? 'Mengirim...' : 'Sending...' ?>';
      
      try {
        const response = await fetch('mail.php', { method: 'POST', body: new FormData(form) });
        const result = await response.json();
        if (result.success) {
          status.style.color = '#4ade80';
          status.textContent = '<?= $lang === 'id' ? 'Berhasil! Saya akan segera merespons.' : 'Success! I will respond shortly.' ?>';
          form.reset();
        } else {
          status.style.color = '#f87171';
          status.textContent = result.message;
        }
      } catch (error) {
        status.style.color = '#f87171';
        status.textContent = 'Connection failed.';
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = '<?= $lang === 'id' ? 'Kirim Pesan Strategis' : 'Send Strategic Message' ?>';
      }
    });

    // Lightbox Logic
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImg = lightbox.querySelector('img');
    const closeLightbox = lightbox.querySelector('.lightbox-close');

    document.querySelectorAll('.preview-trigger').forEach(img => {
        img.addEventListener('click', () => {
            lightboxImg.src = img.src;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    lightbox.addEventListener('click', (e) => {
        if (e.target !== lightboxImg) {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

  </script>
</body>
</html>

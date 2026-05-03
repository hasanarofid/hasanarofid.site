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
            'meta_title' => 'Hasan Arofid | Professional Web Development Services',
            'meta_desc' => 'Transform your business with high-performance, professional websites. Expert in Landing Pages, Company Profiles, and Custom Systems.',
            'hero_head' => 'Professional Web Solutions for <span>Business Growth</span>.',
            'hero_sub' => 'We help your brand stand out digitally with modern, responsive, and functional designs focused on the best conversion & user experience.',
            'cta_book' => 'Free WhatsApp Consultation',
            'cta_projects' => 'View Our Portfolio',
            'trust_exp' => '10+ Years of Excellence',
            'trust_projects' => '50+ Businesses Helped',
            'trust_uptime' => '99.9% Reliability Guaranteed',
            'about_title' => 'Why Partner With Us?',
            'about_story' => 'With over a decade of experience, we don\'t just build websites; we create digital assets that solve business problems and drive growth. Our focus is on high-performance solutions that deliver results.',
            'expertise_title' => 'Our Specialized Services',
            'exp_backend' => 'Landing Page Design',
            'exp_backend_desc' => 'High-converting landing pages tailored to turn visitors into loyal customers.',
            'exp_api' => 'Company Profile',
            'exp_api_desc' => 'Establish a professional digital presence that builds trust and credibility.',
            'exp_scaling' => 'Custom Web Systems',
            'exp_scaling_desc' => 'Tailor-made systems (POS, ERP, SaaS) designed to streamline your business operations.',
            'exp_devops' => 'E-Commerce Solutions',
            'exp_devops_desc' => 'Scalable online stores with seamless payment integration and management.',
            'how_work_title' => 'Our Simple Process',
            'how_work_step1' => 'Consultation & Discovery',
            'how_work_step2' => 'Strategic Design & UI/UX',
            'how_work_step3' => 'Development & Testing',
            'how_work_step4' => 'Launch & Ongoing Support',
            'systems_title' => 'Successful Projects',
            'systems_desc' => 'A showcase of high-impact digital solutions we\'ve built for businesses across various industries.',
            'project_impact' => 'Business Result',
            'project_problem' => 'Challenge',
            'project_solution' => 'Solution',
            'tech_title' => 'Our Tech Excellence',
            'cta_foot' => 'Ready to grow your business?',
            'testimonials_title' => 'What Our Clients Say',
            'testimonials_sub' => 'Hear from the business owners and partners who have grown their digital presence with us.',
            'products_title' => 'Digital Assets',
            'products_subtitle' => 'Ready-to-use tools and resources to accelerate your business growth.',
            'more_products' => 'Browse All Products',
            'footer_copy' => '© ' . date('Y') . ' Hasan Arofid. Expertly crafted for performance.',
        ],
        'id' => [
            'meta_title' => 'Hasan Arofid | Jasa Pembuatan Website Profesional',
            'meta_desc' => 'Transformasi bisnis Anda dengan website profesional berperforma tinggi. Spesialis Landing Page, Company Profile, dan Sistem Kustom.',
            'hero_head' => 'Solusi Web Profesional untuk <span>Pertumbuhan Bisnis</span>.',
            'hero_sub' => 'Kami membantu brand Anda tampil menonjol secara digital dengan desain modern, responsif, dan fungsional yang fokus pada konversi & user experience terbaik.',
            'cta_book' => 'Konsultasi WhatsApp Gratis',
            'cta_projects' => 'Lihat Portofolio',
            'trust_exp' => '10+ Tahun Pengalaman',
            'trust_projects' => '50+ Bisnis Terbantu',
            'trust_uptime' => '99.9% Garansi Reliabilitas',
            'about_title' => 'Mengapa Bermitra Dengan Kami?',
            'about_story' => 'Dengan pengalaman lebih dari satu dekade, kami tidak hanya membuat website; kami menciptakan aset digital yang menyelesaikan masalah bisnis dan mendorong pertumbuhan.',
            'expertise_title' => 'Layanan Spesialis Kami',
            'exp_backend' => 'Desain Landing Page',
            'exp_backend_desc' => 'Halaman landas konversi tinggi yang dirancang khusus untuk mengubah pengunjung menjadi pelanggan.',
            'exp_api' => 'Company Profile',
            'exp_api_desc' => 'Bangun citra digital profesional yang membangun kepercayaan dan kredibilitas brand Anda.',
            'exp_scaling' => 'Sistem Web Kustom',
            'exp_scaling_desc' => 'Sistem yang dibuat khusus (POS, ERP, SaaS) untuk menyederhanakan operasional bisnis Anda.',
            'exp_devops' => 'Solusi E-Commerce',
            'exp_devops_desc' => 'Toko online yang skalabel dengan integrasi pembayaran dan manajemen yang mulus.',
            'how_work_title' => 'Proses Kerja Kami',
            'how_work_step1' => 'Konsultasi & Penemuan',
            'how_work_step2' => 'Desain Strategis & UI/UX',
            'how_work_step3' => 'Pengembangan & Testing',
            'how_work_step4' => 'Peluncuran & Dukungan',
            'systems_title' => 'Proyek Yang Berhasil',
            'systems_desc' => 'Kumpulan solusi digital berdampak tinggi yang telah kami bangun untuk berbagai industri bisnis.',
            'project_impact' => 'Hasil Bisnis',
            'project_problem' => 'Tantangan',
            'project_solution' => 'Solusi',
            'tech_title' => 'Keunggulan Teknologi',
            'cta_foot' => 'Siap menumbuhkan bisnis Anda?',
            'testimonials_title' => 'Apa Kata Klien Kami',
            'testimonials_sub' => 'Dengarkan pengalaman para pemilik bisnis dan mitra yang telah tumbuh bersama solusi digital kami.',
            'products_title' => 'Produk Digital',
            'products_subtitle' => 'Alat dan sumber daya siap pakai untuk mempercepat pertumbuhan bisnis Anda.',
            'more_products' => 'Lihat Semua Produk',
            'footer_copy' => '© ' . date('Y') . ' Hasan Arofid. Dibuat secara ahli untuk performa tinggi.',
        ]
    ];
    $t = $translations[$lang];

    // Fetch latest products
    try {
        $stmtProducts = $db->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT 3");
        $stmtProducts->execute();
        $latestProducts = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $latestProducts = [];
    }

    // Fetch SEO Settings
    try {
        $seoData = $db->query("SELECT * FROM seo_settings WHERE page_name = 'homepage'")->fetch(PDO::FETCH_ASSOC);
        if ($seoData) {
            $t['meta_title'] = $seoData['title'];
            $t['meta_desc'] = $seoData['description'];
            $t['meta_keywords'] = $seoData['keywords'];
        }
    } catch (Exception $e) {}

    // Fetch Articles
    try {
        $latestArticles = $db->query("SELECT * FROM articles WHERE status = 'published' ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { $latestArticles = []; }

} catch (PDOException $e) { 
    $latestProducts = []; 
    $latestArticles = [];
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $t['meta_title'] ?></title>
  <meta name="google-site-verification" content="E-tyAYsOQMugMAc2KAkBnFdVc9mAbKbId7ZOAK3gpDQ" />

  <meta name="description" content="<?= $t['meta_desc'] ?>" />
  <meta name="keywords" content="<?= $t['meta_keywords'] ?? 'fullstack engineer, web architecture, Laravel, Node.js, React, System Scaling, Enterprise Software' ?>" />

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
  <link rel="icon" type="image/png" href="images/logohasanarofid.png" />


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
    "name": "Hasan Arofid - Jasa Pembuatan Website Profesional",
    "image": "https://hasanarofid.site/images/hasanarofid.png",
    "@id": "https://hasanarofid.site/",
    "url": "https://hasanarofid.site/",
    "telephone": "+628814959247",
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
      "longitude": -7.323285
    },
    "serviceType": "Web Development, SEO, E-commerce",
    "areaServed": "Indonesia",
    "priceRange": "$$"
  }
  </script>


  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --bg: #f8fafc;
      --surface: #ffffff;
      --surface-hover: #f1f5f9;
      --text: #0f172a;
      --text-muted: #64748b;
      --border: rgba(0,0,0,0.05);
      --accent: #3b82f6;
      --gradient: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
      --glass: rgba(255, 255, 255, 0.8);
    }

    [data-theme="dark"] {
      --bg: #030712;
      --surface: #0f172a;
      --surface-hover: #1e293b;
      --text: #f8fafc;
      --text-muted: #94a3b8;
      --border: rgba(255,255,255,0.05);
      --glass: rgba(15, 23, 42, 0.8);
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
        display: flex;
        align-items: center;
        gap: 12px;
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
    .logo-img {
        height: 42px;
        width: auto;
        -webkit-text-fill-color: initial; /* Reset gradient clip for image */
    }
    .logo-subtitle {
        font-size: 0.9rem; 
        font-weight: 400; 
        opacity: 0.7; 
        margin-left: 8px; 
        color: var(--text-muted); 
        -webkit-text-fill-color: var(--text-muted);
    }
    .logo:hover { opacity: 0.8; transform: scale(0.98); }



    .nav-links { display: flex; gap: 28px; align-items: center; }
    .nav-links a { 
        color: var(--text-muted); 
        text-decoration: none; 
        font-size: 0.9rem; 
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

    .nav-actions { display: flex; gap: 16px; align-items: center; }
    .btn-nav { padding: 8px 20px; font-size: 0.85rem; border-radius: 99px; }

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
    .btn-primary { background: var(--gradient); color: #ffffff !important; border: none; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3); }
    .btn-outline { border: 1px solid var(--border); color: var(--text); background: rgba(0,0,0,0.02); }
    .btn-outline:hover { background: rgba(0,0,0,0.05); border-color: var(--accent); }

    section { padding: 120px 0; }
    .section-title { font-size: 2.5rem; margin-bottom: 16px; text-align: center; }
    .section-subtitle { color: var(--text-muted); text-align: center; max-width: 700px; margin: 0 auto 64px; font-size: 1.1rem; }

    [data-reveal] { opacity: 0; transform: translateY(20px); transition: 0.8s cubic-bezier(0.2, 0, 0, 1); }
    [data-reveal].active { opacity: 1; transform: translateY(0); }

    .hero { min-height: 100vh; display: flex; align-items: center; position: relative; overflow: hidden; padding-top: 72px; }
    .hero-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 48px; align-items: center; width: 100%; }
    .hero-content { max-width: 850px; }
    .hero-title { font-size: clamp(2.2rem, 5vw, 4rem); line-height: 1.1; margin-bottom: 24px; word-wrap: break-word; }
    .hero-title span { background: var(--gradient); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    .hero-sub { font-size: 1.15rem; color: var(--text-muted); margin-bottom: 40px; max-width: 600px; }
    .hero-actions { display: flex; gap: 16px; flex-wrap: wrap; }
    
    .hero-visual { position: relative; display: flex; justify-content: center; align-items: center; padding: 20px; }
    .mockup-container { position: relative; width: 100%; max-width: 550px; perspective: 1200px; height: 450px; display: flex; align-items: center; }
    .mockup-laptop { 
        width: 100%; 
        height: 340px; 
        object-fit: cover; 
        object-position: top; 
        border-radius: 12px; 
        box-shadow: 0 30px 60px rgba(0,0,0,0.1); 
        transform: rotateY(-15deg) rotateX(5deg); 
        position: relative; 
        z-index: 2; 
        border: 4px solid var(--surface-hover);
    }
    .mockup-phone { 
        position: absolute; 
        bottom: 40px; 
        left: -30px; 
        width: 150px; 
        height: 300px; 
        object-fit: cover; 
        object-position: top;
        border-radius: 28px; 
        border: 7px solid var(--surface-hover); 
        box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
        z-index: 3; 
        transform: rotateY(15deg); 
    }

    .hero-bg { position: absolute; inset: 0; z-index: 1; background: radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.1), transparent 50%); }

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
    .whatsapp-float:hover { transform: scale(1.1) translateY(-5px); box-shadow: 0 15px 30px rgba(37, 211, 102, 0.4); }

    .trust-bar { padding: 60px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: rgba(0,0,0,0.01); }
    .trust-title { text-align: center; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 32px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; }
    .trust-grid { display: flex; justify-content: center; align-items: center; gap: 60px; flex-wrap: wrap; opacity: 0.6; filter: grayscale(1); }
    .trust-grid img { height: 32px; width: auto; }

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
        background: rgba(0, 0, 0, 0.9);
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
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
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


    .marquee { overflow: hidden; white-space: nowrap; padding: 64px 0; background: rgba(0,0,0,0.02); }
    .marquee-content { display: inline-block; animation: marquee 40s linear infinite; }
    .marquee-content span { font-size: 1.8rem; font-weight: 800; margin-right: 64px; color: var(--text-muted); opacity: 0.3; }
    @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    footer { padding: 80px 0; border-top: 1px solid var(--border); color: var(--text-muted); text-align: center; }
    .social-links { display: flex; justify-content: center; gap: 32px; margin-top: 32px; }
    .social-links a { color: var(--text-muted); text-decoration: none; font-weight: 500; transition: 0.2s; }
    .social-links a:hover { color: var(--accent); }

    .contact-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-input { padding: 16px; border-radius: 12px; border: 1px solid var(--border); background: var(--surface); color: var(--text); width: 100%; font-family: inherit; }
    .form-input:focus { outline: none; border-color: var(--accent); }

    .testi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px; }
    .testi-card { padding: 40px; background: var(--surface); border: 1px solid var(--border); border-radius: 24px; position: relative; }
    .testi-quote { font-size: 1.1rem; font-style: italic; color: var(--text); margin-bottom: 24px; position: relative; z-index: 2; }
    .testi-quote::before { content: '"'; font-size: 4rem; position: absolute; top: -20px; left: -10px; color: var(--accent); opacity: 0.1; z-index: 1; }
    .testi-author { display: flex; align-items: center; gap: 16px; }
    .testi-avatar { width: 48px; height: 48px; border-radius: 50%; background: var(--gradient); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; }
    .testi-info h4 { font-size: 1rem; margin-bottom: 2px; }
    .testi-info p { font-size: 0.85rem; color: var(--text-muted); }


    @media (max-width: 768px) {
      .hero-title { font-size: clamp(2rem, 12vw, 2.5rem); }

      
      .menu-toggle { display: flex; }
      
      .nav-links {
          position: fixed;
          top: 0;
          right: 0;
          width: 80%;
          height: 100vh;
          background: var(--bg);
          flex-direction: column;
          justify-content: center;
          align-items: center;
          gap: 40px;
          transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
          z-index: 1050;
          border-left: 1px solid var(--border);
          box-shadow: -20px 0 40px rgba(0,0,0,0.05);
          transform: translateX(120%);
          display: flex;
      }
      
      .nav-links.active { transform: translateX(0); }

      
      .nav-links a { font-size: 1.5rem; }

      .menu-toggle.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
      .menu-toggle.active span:nth-child(2) { opacity: 0; }
      .menu-toggle.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }


      section { padding: 80px 0; }
      .hero-grid { grid-template-columns: 1fr; text-align: center; gap: 40px; }
      .hero-content { margin: 0 auto; }
      .hero-actions { justify-content: center; }
      .mockup-container { max-width: 320px; height: 350px; margin: 0 auto; }
      .mockup-laptop { height: 240px; }
      .mockup-phone { width: 110px; height: 220px; left: -15px; bottom: 30px; border-width: 4px; }
      .projects-grid { grid-template-columns: 1fr; }
      .contact-form-grid { grid-template-columns: 1fr; }
      
      .logo { font-size: 1.2rem; gap: 8px; }
      .logo-img { height: 32px; }
      .logo-subtitle { display: none; }
    }



    .article-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px; margin-top: 48px; }
    .article-card { background: var(--surface); border: 1px solid var(--border); border-radius: 24px; padding: 40px; transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1); text-decoration: none; color: inherit; display: flex; flex-direction: column; position: relative; overflow: hidden; }
    .article-card::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: var(--gradient); opacity: 0; transition: 0.3s; }
    .article-card:hover { border-color: var(--accent); transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    .article-card:hover::after { opacity: 1; }
    .article-date { font-size: 0.85rem; color: var(--accent); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; display: block; }
    .article-card h3 { font-size: 1.5rem; margin-bottom: 16px; line-height: 1.3; font-family: 'Poppins', sans-serif; }
    .article-card p { font-size: 1rem; color: var(--text-muted); margin-bottom: 32px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .article-link { color: var(--text); font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 12px; transition: 0.3s; }
    .article-card:hover .article-link { color: var(--accent); gap: 16px; }

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
      <a href="#" class="logo">
        <img src="images/logohasanarofid.png" alt="Hasan Arofid Logo" class="logo-img">
        <span>Hasan Arofid <span class="logo-subtitle">| <?= $lang === 'id' ? 'Jasa Website' : 'Web Services' ?></span></span>
      </a>

      
      <div class="nav-links" id="navLinks">
        <a href="#about"><?= $lang === 'id' ? 'Tentang' : 'About' ?></a>
        <a href="#expertise"><?= $lang === 'id' ? 'Layanan' : 'Services' ?></a>
        <a href="#projects"><?= $lang === 'id' ? 'Portofolio' : 'Portfolio' ?></a>
        <a href="#testimonials"><?= $lang === 'id' ? 'Testimoni' : 'Testimonials' ?></a>
        <div class="theme-toggle" id="themeToggle">🌓</div>
        <a href="?lang=<?= $lang === 'id' ? 'en' : 'id' ?>" class="lang-toggle"><?= $lang === 'id' ? 'EN' : 'ID' ?></a>
        <a href="https://wa.me/628814959247" class="btn btn-primary btn-nav"><?= $lang === 'id' ? 'Hubungi Kami' : 'Contact Us' ?></a>
      </div>

      <div class="menu-toggle" id="mobileMenuToggle">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </nav>



  <a href="https://wa.me/628814959247?text=Halo%20Hasan,%20saya%20ingin%20konsultasi%20mengenai%20pembuatan%20website" class="whatsapp-float" target="_blank" aria-label="Chat on WhatsApp">
    <svg width="34" height="34" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <section class="hero">
    <div class="hero-bg"></div>
    <div class="container">
      <div class="hero-grid">
        <div class="hero-content" data-reveal>
          <h1 class="hero-title"><?= $t['hero_head'] ?></h1>
          <p class="hero-sub"><?= $t['hero_sub'] ?></p>
          <div class="hero-actions">
            <a href="https://wa.me/628814959247?text=Halo%20Hasan,%20saya%20ingin%20konsultasi%20mengenai%20pembuatan%20website" class="btn btn-primary" target="_blank"><?= $t['cta_book'] ?></a>
            <a href="#projects" class="btn btn-outline"><?= $t['cta_projects'] ?></a>
          </div>
        </div>
        <div class="hero-visual" data-reveal data-delay="200">
          <div class="mockup-container">
            <img src="images/amtechev.png" alt="Laptop Mockup" class="mockup-laptop">
            <img src="images/porto3.png" alt="Phone Mockup" class="mockup-phone">
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="trust-bar">
    <div class="container">
      <div class="trust-title"><?= $lang === 'id' ? 'Dipercaya oleh Bisnis & Industri Terkemuka' : 'Trusted by Leading Businesses & Industries' ?></div>
      <div class="trust-grid">
        <div class="trust-item" data-reveal><h4>10+</h4><p><?= $t['trust_exp'] ?></p></div>
        <div class="trust-item" data-reveal data-delay="100"><h4>50+</h4><p><?= $t['trust_projects'] ?></p></div>
        <div class="trust-item" data-reveal data-delay="200"><h4>99.9%</h4><p><?= $t['trust_uptime'] ?></p></div>
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
          <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 8px;"><?= $lang === 'id' ? 'Diskusi mendalam untuk memahami visi dan target bisnis Anda.' : 'Deep discussion to understand your business vision and targets.' ?></p>
        </div>
        <div class="process-item" data-reveal data-delay="100">
          <h3>02</h3>
          <p><strong><?= $t['how_work_step2'] ?></strong></p>
          <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 8px;"><?= $lang === 'id' ? 'Perancangan UI/UX yang fokus pada pengalaman pengguna dan konversi.' : 'UI/UX design focused on user experience and conversion.' ?></p>
        </div>
        <div class="process-item" data-reveal data-delay="200">
          <h3>03</h3>
          <p><strong><?= $t['how_work_step3'] ?></strong></p>
          <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 8px;"><?= $lang === 'id' ? 'Pengembangan sistem yang cepat, aman, dan mudah dikelola.' : 'Fast, secure, and easy-to-manage system development.' ?></p>
        </div>
        <div class="process-item" data-reveal data-delay="300">
          <h3>04</h3>
          <p><strong><?= $t['how_work_step4'] ?></strong></p>
          <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 8px;"><?= $lang === 'id' ? 'Website live dan siap membantu pertumbuhan bisnis Anda.' : 'Website live and ready to help your business grow.' ?></p>
        </div>
      </div>
    </div>
  </section>

  <section id="expertise" style="background: rgba(0,0,0,0.01);">
    <div class="container">
      <div class="section-title" data-reveal><?= $t['expertise_title'] ?></div>
      <div class="exp-grid">
        <div class="exp-card" data-reveal>
          <span class="exp-icon">🎯</span>
          <h3><?= $t['exp_backend'] ?></h3>
          <p><?= $t['exp_backend_desc'] ?></p>
          <a href="https://wa.me/628814959247?text=Halo%20Hasan,%20saya%20tertarik%20dengan%20paket%20Landing%20Page" class="btn btn-outline" style="margin-top: 24px; width: 100%; justify-content: center;"><?= $lang === 'id' ? 'Pelajari Selengkapnya' : 'Learn More' ?></a>
        </div>
        <div class="exp-card" data-reveal data-delay="100">
          <span class="exp-icon">🏢</span>
          <h3><?= $t['exp_api'] ?></h3>
          <p><?= $t['exp_api_desc'] ?></p>
          <a href="https://wa.me/628814959247?text=Halo%20Hasan,%20saya%20tertarik%20dengan%20paket%20Company%20Profile" class="btn btn-outline" style="margin-top: 24px; width: 100%; justify-content: center;"><?= $lang === 'id' ? 'Pelajari Selengkapnya' : 'Learn More' ?></a>
        </div>
        <div class="exp-card" data-reveal data-delay="200">
          <span class="exp-icon">⚙️</span>
          <h3><?= $t['exp_scaling'] ?></h3>
          <p><?= $t['exp_scaling_desc'] ?></p>
          <a href="https://wa.me/628814959247?text=Halo%20Hasan,%20saya%20tertarik%20dengan%20sistem%20Web%20Custom" class="btn btn-outline" style="margin-top: 24px; width: 100%; justify-content: center;"><?= $lang === 'id' ? 'Pelajari Selengkapnya' : 'Learn More' ?></a>
        </div>
        <div class="exp-card" data-reveal data-delay="300">
          <span class="exp-icon">🛍️</span>
          <h3><?= $t['exp_devops'] ?></h3>
          <p><?= $t['exp_devops_desc'] ?></p>
          <a href="https://wa.me/628814959247?text=Halo%20Hasan,%20saya%20tertarik%20dengan%20solusi%20E-Commerce" class="btn btn-outline" style="margin-top: 24px; width: 100%; justify-content: center;"><?= $lang === 'id' ? 'Pelajari Selengkapnya' : 'Learn More' ?></a>
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
  <section id="digital-products" style="background: rgba(0,0,0,0.01);">
    <div class="container">
      <div class="section-title" data-reveal><?= $t['products_title'] ?></div>
      <div class="section-subtitle" data-reveal><?= $t['products_subtitle'] ?></div>
      
      <div class="projects-grid">
        <?php foreach ($latestProducts as $p): ?>
          <div class="project-card" data-reveal>
            <?php if ($p['image_url']): ?>
              <img src="<?= htmlspecialchars($p['image_url']) ?>" class="project-img" alt="<?= htmlspecialchars($p['name']) ?>">
            <?php else: ?>
              <div class="project-img" style="display: flex; align-items: center; justify-content: center; font-size: 3rem; background: var(--surface-hover);">📦</div>
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

  <section id="testimonials">
    <div class="container">
      <div class="section-title" data-reveal><?= $t['testimonials_title'] ?></div>
      <div class="section-subtitle" data-reveal><?= $t['testimonials_sub'] ?></div>
      
      <div class="testi-grid">
        <div class="testi-card" data-reveal>
          <p class="testi-quote"><?= $lang === 'id' ? 'Sistem infrastruktur yang dibangun Hasan sangat stabil. Operasional jaringan EV kami di Malaysia menjadi 100% otomatis dan sangat efisien.' : 'The infrastructure system built by Hasan is incredibly stable. Our EV network operations in Malaysia are now 100% automated and highly efficient.' ?></p>
          <div class="testi-author">
            <div class="testi-avatar">A</div>
            <div class="testi-info">
              <h4>Amtech EV Team</h4>
              <p>Enterprise SaaS Partner</p>
            </div>
          </div>
        </div>
        <div class="testi-card" data-reveal data-delay="100">
          <p class="testi-quote"><?= $lang === 'id' ? 'Manajemen pesanan kami sekarang jauh lebih cepat dan terintegrasi. Penjualan meningkat karena website sangat mudah digunakan oleh pelanggan.' : 'Our order management is now much faster and integrated. Sales increased because the website is so easy for customers to use.' ?></p>
          <div class="testi-author">
            <div class="testi-avatar">N</div>
            <div class="testi-info">
              <h4>Owner Nitajaya Catering</h4>
              <p>F&B Business Owner</p>
            </div>
          </div>
        </div>
        <div class="testi-card" data-reveal data-delay="200">
          <p class="testi-quote"><?= $lang === 'id' ? 'Solusi digital yang diberikan benar-benar menjawab masalah operasional kami. Sistem pengawasan sekolah kami menjadi jauh lebih transparan.' : 'The digital solution provided truly addressed our operational challenges. Our school oversight system has become much more transparent.' ?></p>
          <div class="testi-author">
            <div class="testi-avatar">S</div>
            <div class="testi-info">
              <h4>Education Oversight</h4>
              <p>Government/Institutional Client</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="marquee">
    <div class="marquee-content">
      <span>LARAVEL</span><span>NODE.JS</span><span>REACT</span><span>POSTGRESQL</span><span>DOCKER</span><span>REDIS</span><span>TYPESCRIPT</span>
      <span>LARAVEL</span><span>NODE.JS</span><span>REACT</span><span>POSTGRESQL</span><span>DOCKER</span><span>REDIS</span><span>TYPESCRIPT</span>
    </div>
  </div>

  <section id="contact">
    <div class="container" style="max-width: 700px;">
      <div class="section-title" data-reveal><?= $lang === 'id' ? 'Dapatkan Penawaran Gratis' : 'Get a Free Quote' ?></div>
      <div class="section-subtitle" data-reveal><?= $lang === 'id' ? 'Siap memulai proyek Anda? Beritahu kami kebutuhan Anda.' : 'Ready to start your project? Tell us your needs.' ?></div>
      <form id="dev-request-form" style="display: grid; gap: 20px; margin-top: 48px;">
        <div class="contact-form-grid">
          <input type="text" name="name" placeholder="<?= $lang === 'id' ? 'Nama' : 'Name' ?>" required class="form-input">
          <input type="email" name="email" placeholder="Email" required class="form-input">
        </div>
        <textarea name="description" placeholder="<?= $lang === 'id' ? 'Ceritakan tantangan teknis Anda...' : 'Tell me about your technical challenge...' ?>" rows="6" required class="form-input"></textarea>
        <input type="hidden" name="service" value="web-development-service">
        <button type="submit" class="btn btn-primary" style="justify-content: center; font-size: 1.1rem; width: 100%;"><?= $lang === 'id' ? 'Kirim Pesan Strategis' : 'Send Strategic Message' ?></button>
      </form>

      <div id="form-status" style="margin-top: 24px; text-align: center; font-weight: 500;"></div>
    </div>
  </section>

  <?php if (!empty($latestArticles)): ?>
  <section id="blog" style="background: rgba(0,0,0,0.01);">
    <div class="container">
      <div class="section-title" data-reveal><?= $lang === 'id' ? 'Wawasan & Strategi Digital' : 'Insights & Digital Strategy' ?></div>
      <div class="section-subtitle" data-reveal><?= $lang === 'id' ? 'Artikel terbaru untuk membantu Anda memahami tren teknologi dan strategi pertumbuhan bisnis online.' : 'Latest articles to help you understand tech trends and online business growth strategies.' ?></div>
      
      <div class="article-grid">
        <?php foreach ($latestArticles as $a): ?>
        <a href="blog.php?slug=<?= $a['slug'] ?>" class="article-card" data-reveal>
          <span class="article-date"><?= date('M d, Y', strtotime($a['created_at'])) ?></span>
          <h3><?= htmlspecialchars($a['title']) ?></h3>
          <p><?= htmlspecialchars($a['excerpt']) ?></p>
          <span class="article-link"><?= $lang === 'id' ? 'Baca Selengkapnya' : 'Read Full Article' ?> <span>→</span></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

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
    const body = document.body;
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

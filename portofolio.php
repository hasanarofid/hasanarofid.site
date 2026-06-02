<?php
session_start();
if (isset($_GET['lang'])) {
  $_SESSION['lang'] = $_GET['lang'] === 'id' ? 'id' : 'en';
}

if (!isset($_SESSION['lang'])) {
  $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
  $_SESSION['lang'] = ($browserLang === 'id') ? 'id' : 'en';
}
$lang = $_SESSION['lang'];

$translations = [
  'en' => [
    'meta_title' => 'Portfolio | Hasan Arofid',
    'meta_desc' => 'Explore the successful digital solutions and systems we have built.',
    'portfolio_title' => 'Our Portfolio',
    'portfolio_desc' => 'A comprehensive showcase of the systems and digital solutions we\'ve crafted for various clients.',
    'internal_systems' => 'Internal Systems',
    'internal_systems_desc' => 'Custom enterprise software built to optimize internal business operations.',
    'public_systems' => 'Public Systems',
    'public_systems_desc' => 'High-performance web platforms built for public engagement and scale.',
    'back_home' => 'Back to Home',
    'project_impact' => 'Business Result',
    'desc_school' => 'Internal monitoring and management system for schools, streamlining operational tasks and audits. Ensuring high availability and efficiency for school administrators.',
    'desc_mrlux' => 'Comprehensive enterprise resource planning software featuring delivery notes, inventory tracking, sales processing, and robust business logic for wholesale distribution.',
    'desc_nitajaya' => 'Integrated catering management and point-of-sale system. Streamlining order processing and inventory tracking for improved operational efficiency.',
    'desc_nolimits' => 'A dynamic and engaging public-facing platform designed for training programs, showcasing modern UI, seamless user experience, and optimized conversion pathways.',
    'desc_amtech' => 'Built a unified management system for Malaysia\'s leading EV network. Handling real-time hardware telemetry and multi-tenant billing.',
    'desc_gringgo' => 'A technology platform focusing on waste management and environmental solutions to empower communities.',
  ],
  'id' => [
    'meta_title' => 'Portofolio | Hasan Arofid',
    'meta_desc' => 'Jelajahi solusi digital dan sistem yang telah kami bangun.',
    'portfolio_title' => 'Portofolio Kami',
    'portfolio_desc' => 'Kumpulan lengkap sistem dan solusi digital yang telah kami buat untuk berbagai klien.',
    'internal_systems' => 'Sistem Internal',
    'internal_systems_desc' => 'Perangkat lunak enterprise kustom yang dibangun untuk mengoptimalkan operasional internal bisnis.',
    'public_systems' => 'Sistem Publik',
    'public_systems_desc' => 'Platform web berkinerja tinggi yang dibangun untuk keterlibatan publik dan skala besar.',
    'back_home' => 'Kembali ke Beranda',
    'project_impact' => 'Hasil Bisnis',
    'desc_school' => 'Sistem pemantauan dan manajemen internal untuk sekolah, menyederhanakan tugas operasional dan audit. Memastikan ketersediaan dan efisiensi tinggi bagi pengelola sekolah.',
    'desc_mrlux' => 'Perangkat lunak perencanaan sumber daya perusahaan (ERP) komprehensif yang dilengkapi surat jalan, pelacakan inventaris, pemrosesan penjualan, dan logika bisnis yang kuat untuk distribusi grosir.',
    'desc_nitajaya' => 'Sistem manajemen katering dan point-of-sale yang terintegrasi. Menyederhanakan pemrosesan pesanan dan pelacakan inventaris untuk efisiensi operasional yang lebih baik.',
    'desc_nolimits' => 'Platform publik yang dinamis dan interaktif untuk program pelatihan, menampilkan UI modern, pengalaman pengguna yang mulus, dan alur konversi yang dioptimalkan.',
    'desc_amtech' => 'Membangun sistem manajemen terpadu untuk jaringan EV terkemuka di Malaysia. Menangani telemetri perangkat keras secara real-time dan penagihan multi-tenant.',
    'desc_gringgo' => 'Platform teknologi yang berfokus pada pengelolaan sampah dan solusi lingkungan untuk memberdayakan komunitas.',
  ]
];
$t = $translations[$lang];

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['meta_title'] ?></title>
    <meta name="description" content="<?= $t['meta_desc'] ?>" />

    <!-- Open Graph / LinkedIn / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://hasanarofid.site/portofolio" />
    <meta property="og:title" content="<?= $t['meta_title'] ?>" />
    <meta property="og:description" content="<?= $t['meta_desc'] ?>" />
    <meta property="og:image" content="https://hasanarofid.site/images/hasanarofid.png" />

    <!-- Twitter / Threads -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="https://hasanarofid.site/portofolio" />
    <meta property="twitter:title" content="<?= $t['meta_title'] ?>" />
    <meta property="twitter:description" content="<?= $t['meta_desc'] ?>" />
    <meta property="twitter:image" content="https://hasanarofid.site/images/hasanarofid.png" />

    <link rel="canonical" href="https://hasanarofid.site/portofolio" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/logohasanarofid.png" />
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-hover: #f1f5f9;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: rgba(0, 0, 0, 0.05);
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
            --border: rgba(255, 255, 255, 0.05);
            --glass: rgba(15, 23, 42, 0.8);
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.6; margin: 0; padding: 0; }
        h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        
        nav { position: fixed; top: 0; width: 100%; height: 72px; background: var(--glass); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); z-index: 1000; display: flex; align-items: center; }
        .logo { font-weight: 800; font-size: 1.5rem; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-decoration: none; display: flex; align-items: center; gap: 12px; }
        .logo-img { height: 42px; width: auto; }
        
        .nav-actions { display: flex; gap: 16px; align-items: center; }
        .lang-toggle { background: var(--surface); border: 1px solid var(--border); color: var(--text); padding: 4px 14px; border-radius: 999px; font-size: 0.85rem; text-decoration: none; font-weight: 600; }
        .theme-toggle { background: var(--surface); border: 1px solid var(--border); color: var(--text); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; font-size: 1.1rem; }

        .header-spacing { padding-top: 120px; }
        .section-title { font-size: 2.5rem; text-align: center; margin-bottom: 16px; }
        .section-subtitle { color: var(--text-muted); text-align: center; max-width: 700px; margin: 0 auto 64px; font-size: 1.1rem; }
        
        .category-title { font-size: 1.8rem; margin: 48px 0 16px; padding-bottom: 12px; border-bottom: 2px solid var(--border); }
        .category-desc { color: var(--text-muted); margin-bottom: 32px; font-size: 1.05rem; }

        .projects-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 32px; }
        .project-card { background: var(--surface); border: 1px solid var(--border); border-radius: 20px; overflow: hidden; transition: 0.3s; display: flex; flex-direction: column; }
        .project-card:hover { transform: translateY(-8px); border-color: var(--accent); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .project-content { padding: 32px; flex-grow: 1; }
        .project-tag { display: inline-block; padding: 6px 16px; background: var(--surface-hover); color: var(--accent); border-radius: 99px; font-size: 0.85rem; font-weight: 600; margin-bottom: 16px; }
        .project-content h3 { font-size: 1.4rem; margin-bottom: 12px; }
        .project-content p { color: var(--text-muted); font-size: 0.95rem; }
        
        .image-gallery { display: grid; gap: 8px; padding: 16px; background: var(--surface-hover); border-bottom: 1px solid var(--border); }
        .gallery-2 { grid-template-columns: 1fr 1fr; }
        .gallery-4 { grid-template-columns: 1fr 1fr; }
        .image-gallery img { width: 100%; height: 160px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border); transition: 0.3s; }
        .image-gallery img:hover { transform: scale(1.02); }
        
        .gallery-1 img { width: 100%; height: 328px; }
        
        .btn { display: inline-flex; align-items: center; padding: 12px 28px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: 0.3s; cursor: pointer; }
        .btn-primary { background: var(--gradient); color: #ffffff; }
        .btn-outline { border: 1px solid var(--border); color: var(--text); background: rgba(0,0,0,0.02); }
        .btn-outline:hover { background: rgba(0,0,0,0.05); border-color: var(--accent); }

        [data-reveal] { animation: fadeInUp 0.8s ease forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        
        footer { padding: 40px 0; text-align: center; color: var(--text-muted); border-top: 1px solid var(--border); margin-top: 60px; font-size: 0.9rem; }
        
        /* Modal Styles */
        .image-gallery img { cursor: zoom-in; }
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            z-index: 9999; display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: 0.4s ease; padding: 24px;
        }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-content {
            position: relative; max-width: 90%; max-height: 90vh;
            transform: scale(0.95) translateY(20px); opacity: 0;
            transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .modal-overlay.active .modal-content { transform: scale(1) translateY(0); opacity: 1; }
        .modal-image {
            max-width: 100%; max-height: 85vh; border-radius: 16px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1); object-fit: contain;
        }
        .modal-close {
            position: absolute; top: -20px; right: -20px;
            width: 44px; height: 44px; background: var(--surface); color: var(--text);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 24px; cursor: pointer; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            border: 1px solid var(--border); transition: 0.3s; z-index: 10000;
        }
        .modal-close:hover { background: var(--accent); color: #fff; transform: rotate(90deg) scale(1.1); }
    </style>
</head>
<body>
    <nav>
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <a href="/" class="logo">
                <img src="images/logohasanarofid.png" alt="Logo" class="logo-img">
                Hasan Arofid
            </a>
            <div class="nav-actions">
                <a href="?lang=<?= $lang === 'en' ? 'id' : 'en' ?>" class="lang-toggle">
                    <?= strtoupper($lang === 'en' ? 'id' : 'en') ?>
                </a>
                <button class="theme-toggle" id="theme-btn" aria-label="Toggle theme">🌓</button>
            </div>
        </div>
    </nav>

    <div class="container header-spacing">
        <div style="text-align: center; margin-bottom: 24px;" data-reveal>
            <a href="/" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.9rem;">← <?= $t['back_home'] ?></a>
        </div>

        <div class="section-title" data-reveal><?= $t['portfolio_title'] ?></div>
        <div class="section-subtitle" data-reveal><?= $t['portfolio_desc'] ?></div>
        
        <h2 class="category-title" data-reveal><?= $t['internal_systems'] ?></h2>
        <p class="category-desc" data-reveal><?= $t['internal_systems_desc'] ?></p>

        <div class="projects-grid">
            <div class="project-card" data-reveal>
                <div class="image-gallery gallery-2">
                    <img src="images/point-sekolah.png" alt="School System Dashboard 1" loading="lazy">
                    <img src="images/point-sekolah2.png" alt="School System Dashboard 2" loading="lazy">
                </div>
                <div class="project-content">
                    <span class="project-tag">Education Tech</span>
                    <h3>School Management System</h3>
                    <p><?= $t['desc_school'] ?></p>
                </div>
            </div>

            <div class="project-card" data-reveal>
                <div class="image-gallery gallery-4">
                    <img src="images/mrluxindonesia.png" alt="Mr Lux Dashboard" loading="lazy">
                    <img src="images/mrluxindonesia1.png" alt="Mr Lux Modules" loading="lazy">
                    <img src="images/mrluxindonesia2.png" alt="Mr Lux Inventory" loading="lazy">
                    <img src="images/mrluxindonesia3.png" alt="Mr Lux Reports" loading="lazy">
                </div>
                <div class="project-content">
                    <span class="project-tag">Enterprise ERP</span>
                    <h3>Mr. Lux Indonesia</h3>
                    <p><?= $t['desc_mrlux'] ?></p>
                </div>
            </div>

            <div class="project-card" data-reveal>
                <div class="image-gallery gallery-1" style="padding: 0; border-bottom: 1px solid var(--border);">
                    <img src="images/nitajaya.png" alt="Nitajaya Catering & POS" loading="lazy" style="border-radius: 20px 20px 0 0; border: none;">
                </div>
                <div class="project-content">
                    <span class="project-tag">PHP • POS</span>
                    <h3>Nitajaya Catering & POS</h3>
                    <p><?= $t['desc_nitajaya'] ?></p>
                </div>
            </div>
        </div>

        <h2 class="category-title" style="margin-top: 80px;" data-reveal><?= $t['public_systems'] ?></h2>
        <p class="category-desc" data-reveal><?= $t['public_systems_desc'] ?></p>

        <div class="projects-grid" style="margin-bottom: 80px;">
            <div class="project-card" data-reveal onclick="window.open('https://nolimitstraining.id/', '_blank')" style="cursor: pointer;">
                <div class="image-gallery gallery-1">
                    <img src="images/nolimitstraining.png" alt="No Limits Training Platform" loading="lazy">
                </div>
                <div class="project-content">
                    <span class="project-tag">Public Platform</span>
                    <h3>No Limits Training</h3>
                    <p><?= $t['desc_nolimits'] ?></p>
                </div>
            </div>

            <div class="project-card" data-reveal onclick="window.open('https://amtechev.com/', '_blank')" style="cursor: pointer;">
                <div class="image-gallery gallery-1" style="padding: 0; border-bottom: 1px solid var(--border);">
                    <img src="images/amtechev.png" alt="Amtech EV" loading="lazy" style="border-radius: 20px 20px 0 0; border: none;">
                </div>
                <div class="project-content">
                    <span class="project-tag">Enterprise SaaS</span>
                    <h3>Amtech EV Infrastructure</h3>
                    <p><?= $t['desc_amtech'] ?></p>
                </div>
            </div>

            <div class="project-card" data-reveal onclick="window.open('https://gringgo.org/', '_blank')" style="cursor: pointer;">
                <div class="image-gallery gallery-1" style="padding: 0; border-bottom: 1px solid var(--border);">
                    <img src="images/gringgo.png" alt="Gringgo Platform" loading="lazy" style="border-radius: 20px 20px 0 0; border: none;">
                </div>
                <div class="project-content">
                    <span class="project-tag">Web Platform</span>
                    <h3>Gringgo</h3>
                    <p><?= $t['desc_gringgo'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            © <?= date('Y') ?> Hasan Arofid. Expertly crafted for performance.
        </div>
    </footer>

    <script>
        // Theme Toggle Logic
        const themeBtn = document.getElementById('theme-btn');
        const root = document.documentElement;
        
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            root.setAttribute('data-theme', savedTheme);
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            root.setAttribute('data-theme', 'dark');
        }

        themeBtn.addEventListener('click', () => {
            const currentTheme = root.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });

        // Image Modal Logic
        const modalOverlay = document.createElement('div');
        modalOverlay.className = 'modal-overlay';
        modalOverlay.innerHTML = `
            <div class="modal-content">
                <div class="modal-close">&times;</div>
                <img src="" class="modal-image" alt="Preview">
            </div>
        `;
        document.body.appendChild(modalOverlay);

        const modalImg = modalOverlay.querySelector('.modal-image');
        const modalClose = modalOverlay.querySelector('.modal-close');

        document.querySelectorAll('.image-gallery img').forEach(img => {
            img.addEventListener('click', (e) => {
                e.stopPropagation(); // prevent card click
                modalImg.src = img.src;
                modalOverlay.classList.add('active');
            });
        });

        const closeModal = () => modalOverlay.classList.remove('active');
        
        modalClose.addEventListener('click', closeModal);
        modalOverlay.addEventListener('click', (e) => {
            if(e.target === modalOverlay) closeModal();
        });
        document.addEventListener('keydown', (e) => {
            if(e.key === 'Escape') closeModal();
        });
    </script>
</body>
</html>

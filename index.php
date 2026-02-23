<?php
// Visitor Tracking Logic
$dbFile = __DIR__ . '/stats.db';
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table with expanded columns
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

    // Capture visitor data
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ipHash = hash('sha256', $ip);
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    // Simple Bot/SEO detection
    $isBot = 0;
    if (preg_match('/(googlebot|bingbot|slurp|duckduckbot|baiduspider|yandexbot|sogou|exabot|facebookexternalhit|ia_archiver)/i', $ua)) {
        $isBot = 1;
    }

    // Device detection
    $device = 'Desktop';
    if (preg_match('/(android|iphone|ipad|mobile)/i', $ua)) {
        $device = 'Mobile';
    }

    // Geo-location (Simple caching to avoid per-visit API hits could be added, but for now simple)
    $country = 'Unknown';
    $city = 'Unknown';
    
    // To avoid slowing down the site, we only do GeoIP for new IPs or occasionally
    // For this implementation, we'll try to fetch it but with a short timeout
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
} catch (PDOException $e) {
    // Silently fail
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hasan Arofid | Senior Fullstack Engineer & Web Designing Services</title>

  <meta name="description" content="Hasan Arofid – Senior Fullstack Engineer with 10+ years experience providing premium web designing services in Delhi, India, the UK, and globally." />
  <meta name="keywords" content="web designing services, web designing services near me, web designing services in delhi, web designing services india, web designing services uk, fullstack engineer, PHP, Laravel, Node.js, React" />
  <meta name="geo.region" content="ID-JB" />
  <meta name="geo.placename" content="Bekasi" />
  <meta name="geo.position" content="-6.2383;106.9756" />
  <meta name="ICBM" content="-6.2383, 106.9756" />

  <!-- Open Graph / SEO -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://hasanarofid.site/" />
  <meta property="og:title" content="Hasan Arofid | Premium Web Designing Services" />
  <meta property="og:description" content="Senior Fullstack Engineer offering scalable web systems and specialized web designing services globally, including Delhi, India, and the UK." />
  <meta property="og:image" content="images/porto-operra.png" />

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ProfessionalService",
    "name": "Hasan Arofid - Web Designing Services",
    "image": "https://hasanarofid.site/images/porto-operra.png",
    "@id": "https://hasanarofid.site/",
    "url": "https://hasanarofid.site/",
    "telephone": "+628123456789", 
    "priceRange": "$$",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Delhi",
      "addressCountry": "IN"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": 28.6139,
      "longitude": 77.2090
    },
    "servesCrawl": true,
    "areaServed": [
      {
        "@type": "City",
        "name": "Delhi"
      },
      {
        "@type": "Country",
        "name": "India"
      },
      {
        "@type": "Country",
        "name": "United Kingdom"
      }
    ],
    "description": "Premium web designing services and fullstack engineering for businesses globally, specializing in scalable systems and CRM solutions."
  }
  </script>

  <style>
    :root {
      color-scheme: dark;
      --bg: #0b1224;
      --panel: rgba(255, 255, 255, 0.04);
      --card: rgba(255, 255, 255, 0.06);
      --stroke: rgba(255, 255, 255, 0.08);
      --text: #e5e7eb;
      --muted: #94a3b8;
      --accent: #38bdf8;
      --accent-2: #a855f7;
      --shadow: 0 20px 70px rgba(0, 0, 0, 0.35);
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      margin: 0;
      padding: 0;
      background: radial-gradient(circle at 20% 20%, rgba(56, 189, 248, 0.08), transparent 30%),
        radial-gradient(circle at 80% 0%, rgba(168, 85, 247, 0.08), transparent 25%),
        var(--bg);
      color: var(--text);
      line-height: 1.7;
      min-height: 100vh;
    }

    .container {
      max-width: 980px;
      margin: 0 auto;
      padding: 64px 24px 96px;
    }

    .panel {
      background: var(--panel);
      border: 1px solid var(--stroke);
      border-radius: 18px;
      padding: 32px;
      box-shadow: var(--shadow);
      backdrop-filter: blur(12px);
    }

    header h1 {
      font-size: 2.8rem;
      letter-spacing: -0.02em;
      margin: 0 0 8px;
    }

    .subtitle {
      color: var(--muted);
      margin: 0 0 20px;
      font-size: 1.05rem;
    }

    .lede {
      margin: 0 0 28px;
      font-size: 1.05rem;
      color: #cbd5e1;
      max-width: 680px;
    }

    .actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 16px;
    }

    .btn {
      padding: 12px 16px;
      border-radius: 12px;
      font-weight: 600;
      text-decoration: none;
      border: 1px solid var(--stroke);
      color: var(--text);
      transition: transform 120ms ease, border-color 120ms ease, background 120ms ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      font-size: 1rem;
    }

    .btn:hover {
      transform: translateY(-1px);
      border-color: var(--accent);
      background: rgba(56, 189, 248, 0.08);
    }

    .btn.primary {
      background: linear-gradient(120deg, var(--accent), var(--accent-2));
      color: #0b1224;
      border: none;
    }

    h2 {
      font-size: 1.4rem;
      letter-spacing: -0.01em;
      margin: 32px 0 12px;
      color: var(--accent);
    }

    p {
      font-size: 1rem;
      margin: 0 0 12px;
    }

    .list {
      display: grid;
      gap: 8px;
    }

    .list .item {
      background: var(--card);
      border: 1px solid var(--stroke);
      border-radius: 12px;
      padding: 12px 14px;
    }

    .badges {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 12px 0 0;
    }

    .badge {
      padding: 8px 10px;
      background: var(--card);
      border: 1px solid var(--stroke);
      border-radius: 999px;
      color: #cbd5e1;
      font-size: 0.9rem;
    }

    .portfolio {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 16px;
      margin-top: 12px;
    }

    .card {
      background: var(--card);
      border: 1px solid var(--stroke);
      border-radius: 14px;
      padding: 14px;
      display: grid;
      gap: 10px;
      transition: border-color 120ms ease, transform 120ms ease, box-shadow 120ms ease;
    }

    .card:hover {
      border-color: var(--accent);
      transform: translateY(-2px);
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.25);
    }

    .card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 10px;
      border: 1px solid var(--stroke);
      background: #0b1224;
      cursor: zoom-in;
    }

    .card h3 {
      margin: 0;
      font-size: 1.05rem;
      letter-spacing: -0.01em;
    }

    .meta {
      color: var(--muted);
      font-size: 0.95rem;
      margin: 0;
    }

    /* Form Styles */
    .contact-form {
      display: grid;
      gap: 16px;
      margin-top: 24px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-group label {
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--muted);
    }

    .form-group input, 
    .form-group textarea, 
    .form-group select {
      background: var(--card);
      border: 1px solid var(--stroke);
      border-radius: 10px;
      padding: 12px;
      color: var(--text);
      font-size: 1rem;
      transition: border-color 120ms ease;
    }

    .form-group input:focus, 
    .form-group textarea:focus, 
    .form-group select:focus {
      outline: none;
      border-color: var(--accent);
    }

    .social-links {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      margin-top: 12px;
    }

    .social-links a {
      color: var(--accent);
      text-decoration: none;
      font-weight: 600;
      letter-spacing: -0.01em;
    }

    .social-links a:hover {
      text-decoration: underline;
    }

    footer {
      margin-top: 40px;
      font-size: 0.95rem;
      color: var(--muted);
      text-align: left;
    }

    .lightbox {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      z-index: 999;
      opacity: 0;
      pointer-events: none;
      transition: opacity 150ms ease;
    }

    .lightbox.is-open {
      opacity: 1;
      pointer-events: auto;
    }

    .lightbox-content {
      background: var(--panel);
      border: 1px solid var(--stroke);
      border-radius: 16px;
      padding: 12px;
      max-width: 960px;
      width: min(960px, 90vw);
      max-height: 90vh;
      overflow: auto;
      box-shadow: var(--shadow);
    }

    .lightbox img {
      width: 100%;
      height: auto;
      border-radius: 12px;
      display: block;
    }

    .lightbox-close {
      position: absolute;
      top: 20px;
      right: 24px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid var(--stroke);
      color: var(--text);
      border-radius: 999px;
      padding: 8px 12px;
      cursor: pointer;
      font-weight: 700;
      transition: background 120ms ease, border-color 120ms ease;
    }

    .lightbox-close:hover {
      background: rgba(56, 189, 248, 0.15);
      border-color: var(--accent);
    }

    #form-status {
      margin-top: 16px;
      padding: 12px;
      border-radius: 10px;
      display: none;
    }

    #form-status.success {
      display: block;
      background: rgba(34, 197, 94, 0.1);
      color: #4ade80;
      border: 1px solid rgba(34, 197, 94, 0.2);
    }

    #form-status.error {
      display: block;
      background: rgba(239, 68, 68, 0.1);
      color: #f87171;
      border: 1px solid rgba(239, 68, 68, 0.2);
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="panel">
      <header>
        <h1>Hasan Arofid</h1>
        <p class="subtitle">Senior Fullstack Engineer • 10+ Years Experience</p>
        <p class="lede">
          I build resilient web systems and ship reliable features. From backend-heavy platforms to end-to-end delivery,
          I turn complex business workflows into dependable software with a calm, production-first mindset. Providing professional web designing services globally.
        </p>
        <div class="actions">
          <a class="btn primary" href="#request-form">Start a Project</a>
          <a class="btn" href="https://github.com/hasanarofid" target="_blank">View GitHub</a>
        </div>
        <div class="social-links">
          <a href="https://www.linkedin.com/in/hasan-arofid-47869a130/" target="_blank">LinkedIn</a>
          <a href="https://www.instagram.com/hasanarofid/" target="_blank">Instagram</a>
        </div>
      </header>

      <main>
        <section>
          <h2>What I Do</h2>
          <div class="list">
            <div class="item">Fullstack web application development</div>
            <div class="item">Backend architecture, API integration, and data flows</div>
            <div class="item">Performance tuning, observability, and incident recovery</div>
            <div class="item">Technical consulting for growing products and teams</div>
            <div class="item">Premium web designing services for Delhi, India, UK, and beyond</div>
          </div>
        </section>

        <section>
          <h2>Core Stack</h2>
          <div class="badges">
            <span class="badge">Node.js</span>
            <span class="badge">PHP / Laravel</span>
            <span class="badge">TypeScript</span>
            <span class="badge">React</span>
            <span class="badge">PostgreSQL</span>
            <span class="badge">Microservices</span>
            <span class="badge">Redis & Queues</span>
            <span class="badge">CI/CD</span>
            <span class="badge">Cloud & Containers</span>
            <span class="badge">Vue.js</span>
          </div>
        </section>

        <section>
          <h2>Featured Product</h2>
          <div class="card" style="grid-template-columns: 1fr; border-color: var(--accent); background: linear-gradient(145deg, var(--card), rgba(56, 189, 248, 0.05)); margin-bottom: 32px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; align-items: center;">
              <div>
                <h3 style="font-size: 1.5rem; margin-bottom: 8px; color: var(--text);">Operra — Multi-CRM & WhatsApp Solution</h3>
                <p style="color: var(--muted); margin-bottom: 16px;">
                  An enterprise-grade CRM system integrating lead management, sales pipeline, and WhatsApp automation into one centralized dashboard for maximum business efficiency.
                </p>
                <div class="badges" style="margin-bottom: 20px;">
                  <span class="badge">Laravel 11</span>
                  <span class="badge">WhatsApp API</span>
                  <span class="badge">Multi-Tenant</span>
                  <span class="badge">Redis Queues</span>
                  <span class="badge">PostgreSQL</span>
                </div>
                <a href="http://operra.hasanarofid.site/" target="_blank" class="btn primary">Visit Operra →</a>
              </div>
              <div style="border-radius: 12px; overflow: hidden; border: 1px solid var(--stroke);">
                <img src="images/porto-operra.png" alt="Operra Dashboard Preview" style="height: auto; cursor: zoom-in; width: 100%; display: block;" />
              </div>
            </div>
          </div>
        </section>

        <section>
          <h2>Other Portfolio Highlights</h2>
          <div class="portfolio">
            <div class="card">
              <img src="images/porto1.png" alt="School CMS dashboard preview" />
              <h3>School CMS Platform</h3>
              <p class="meta">React, PHP</p>
              <p>A comprehensive CMS with role-based access, student records, and streamlined workflows for educational operations.</p>
            </div>
            <div class="card">
              <img src="images/porto2.png" alt="Sustainability platform preview" />
              <h3>Empowering Sustainability Platform</h3>
              <p class="meta">React, Node.js</p>
              <p>Data-driven sustainability suite that tracks impact, surfaces insights, and automates stakeholder reporting.</p>
            </div>
            <div class="card">
              <img src="images/porto3.png" alt="School oversight system preview" />
              <h3>School Oversight System</h3>
              <p class="meta">Laravel</p>
              <p>Compliance and supervision system for schools with audit trails, scheduling, and actionable dashboards.</p>
            </div>
          </div> 
        </section>

        <section id="request-form">
          <h2>Request Web Development</h2>
          <p>Looking for professional web designing services? Fill out the form below to share your project details, and I'll get back to you shortly.</p>
          <form class="contact-form" id="dev-request-form">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input type="text" id="name" name="name" placeholder="John Doe" required />
            </div>
            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" placeholder="john@example.com" required />
            </div>
            <div class="form-group">
              <label for="service">Service Needed</label>
              <select id="service" name="service" required>
                <option value="" disabled selected>Select a service</option>
                <option value="web-design">Web Designing Services</option>
                <option value="fullstack-dev">Fullstack Development</option>
                <option value="backend-api">Backend & API Integration</option>
                <option value="consulting">Technical Consulting</option>
              </select>
            </div>
            <div class="form-group">
              <label for="location">Your Location (Optional)</label>
              <select id="location" name="location">
                <option value="international">Other / International</option>
                <option value="delhi">Delhi, India</option>
                <option value="india">India (Other)</option>
                <option value="uk">United Kingdom</option>
              </select>
            </div>
            <div class="form-group">
              <label for="description">Project Description</label>
              <textarea id="description" name="description" rows="5" placeholder="Tell me about your project, goals, and any specific requirements..." required></textarea>
            </div>
            <button type="submit" class="btn primary" style="justify-content: center;">Send Request</button>
          </form>
          <div id="form-status"></div>
        </section>
      </main>

      <footer>
        © 2026 Hasan Arofid — Professional Web Designing Services in Delhi, India, UK, and Globally.
      </footer>
    </div>
  </div>

  <div class="lightbox" id="lightbox" aria-hidden="true">
    <button class="lightbox-close" id="lightbox-close" aria-label="Close full image">✕</button>
    <div class="lightbox-content">
      <img id="lightbox-image" src="" alt="Full preview" />
    </div>
  </div>

  <script>
    (function () {
      const lightbox = document.getElementById("lightbox");
      const lightboxImg = document.getElementById("lightbox-image");
      const closeBtn = document.getElementById("lightbox-close");
      const cards = document.querySelectorAll(".card img");

      function openLightbox(src, alt) {
        lightboxImg.src = src;
        lightboxImg.alt = alt || "Full preview";
        lightbox.classList.add("is-open");
        lightbox.setAttribute("aria-hidden", "false");
      }

      function closeLightbox() {
        lightbox.classList.remove("is-open");
        lightbox.setAttribute("aria-hidden", "true");
        lightboxImg.src = "";
        lightboxImg.alt = "Full preview";
      }

      cards.forEach((img) => {
        img.addEventListener("click", () => openLightbox(img.src, img.alt));
        img.addEventListener("keypress", (e) => {
          if (e.key === "Enter" || e.key === " ") {
            openLightbox(img.src, img.alt);
          }
        });
      });

      closeBtn.addEventListener("click", closeLightbox);
      lightbox.addEventListener("click", (e) => {
        if (e.target === lightbox) closeLightbox();
      });
      window.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeLightbox();
      });

      // Form Submission Logic
      const form = document.getElementById('dev-request-form');
      const status = document.getElementById('form-status');

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        status.className = '';
        status.textContent = '';
        status.style.display = 'none';

        try {
          const response = await fetch('mail.php', {
            method: 'POST',
            body: formData
          });
          const result = await response.json();

          if (result.success) {
            status.className = 'success';
            status.textContent = 'Thank you! Your request has been sent successfully.';
            form.reset();
          } else {
            status.className = 'error';
            status.textContent = 'Oops! ' + (result.message || 'Something went wrong. Please try again.');
          }
        } catch (error) {
          status.className = 'error';
          status.textContent = 'Could not connect to the server. Please check your connection.';
        } finally {
          status.style.display = 'block';
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send Request';
        }
      });
    })();
  </script>
</body>
</html>

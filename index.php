<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hasan Arofid | Senior Fullstack Engineer</title>

  <meta name="description" content="Hasan Arofid – Senior Fullstack Engineer with 10+ years experience building scalable web systems." />

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
          I turn complex business workflows into dependable software with a calm, production-first mindset.
        </p>
        <div class="actions">
          <a class="btn primary" href="mailto:hasanarofid@gmail.com">Book a call</a>
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
          </div>
        </section>

        <section>
          <h2>Portfolio Highlights</h2>
          <div class="portfolio">
            <div class="card">
              <img src="images/porto1.png" alt="School CMS dashboard preview" />
              <h3>School CMS Platform</h3>
              <p class="meta">React, PHP</p>
              <p>Clinical CMS with role-based access, patient records, and streamlined workflows for School operations.</p>
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
      </main>

      <footer>
        © 2025 Hasan Arofid — Built with intention, not hype.
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
    })();
  </script>
</body>
</html>

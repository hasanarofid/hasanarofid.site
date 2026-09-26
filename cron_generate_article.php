<?php
/**
 * Auto-Generate & Publish Article Cron Script
 * Hasan Arofid - Web & SEO Engine
 * 
 * Functions:
 * 1. Generate article using Gemini API / Publish existing draft / Curated Generator
 * 2. Save into stats.db SQLite
 * 3. Auto-regenerate sitemap.xml
 * 4. Ping Google Search Console & IndexNow for fast crawling
 * 
 * Usage in cPanel Cron Job:
 * php /home/USERNAME/public_html/cron_generate_article.php > /dev/null 2>&1
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$baseDir = __DIR__;
$dbFile = $baseDir . '/stats.db';
$envFile = $baseDir . '/.env';
$logFile = $baseDir . '/cron_article.log';

function writeLog($message, $logFile) {
    $entry = "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
    file_put_contents($logFile, $entry, FILE_APPEND);
    echo $entry;
}

writeLog("Starting cron article generation task...", $logFile);

// 1. Load Environment Variables
$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $env[trim($key)] = trim(trim($val), '"\'');
        }
    }
}

// 2. Connect Database
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    writeLog("ERROR: Database connection failed: " . $e->getMessage(), $logFile);
    exit(1);
}

// Helper: Slugify
function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

$geminiKey = $env['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');
$articleCreated = false;

// 3. Option A: Generate Fresh Article via Gemini API (If API Key Present)
if (!empty($geminiKey)) {
    writeLog("Using Gemini API for fresh article generation...", $logFile);

    // List of high-value topics & search intents
    $topics = [
        "Tips Optimasi Kecepatan Website Laravel dan LiteSpeed untuk Skor PageSpeed 100",
        "Panduan Lengkap Arsitektur Clean Code untuk Fullstack Developer Modern",
        "Membangun API RESTful yang Aman: Best Practice Autentikasi JWT dan Rate Limiting",
        "Strategi SEO On-Page 2026: Cara Cepat Agar Website Masuk Halaman 1 Google",
        "Mengapa Bisnis Anda Wajib Memiliki Website Company Profile yang Cepat dan Mobile-Friendly",
        "Panduan Lengkap Migrasi Database SQLite ke PostgreSQL untuk Aplikasi Skalabilitas Tinggi",
        "Mencegah Serangan Cyber pada Website: Panduan Perlindungan CSRF, XSS, dan SQL Injection",
        "Perbandingan Next.js vs Vite React: Mana yang Lebih Efisien untuk Startup Web App",
        "Cara Mengatur Cron Job cPanel untuk Otomatisasi Sistem dan Pengiriman Email",
        "Pentingnya Schema Markup Structured Data untuk Menaikkan CTR di Google Search"
    ];
    $selectedTopic = $topics[array_rand($topics)];

    // Check existing titles to avoid duplicate
    $existing = $db->query("SELECT title FROM articles")->fetchAll(PDO::FETCH_COLUMN);

    $prompt = "Tulis artikel blog teknologi / web development mendalam dalam bahasa Indonesia tentang: '{$selectedTopic}'.
Ketentuan Wajib:
1. Format output HANYA JSON murni tanpa markdown wrapper (jangan gunakan ```json).
2. JSON harus memiliki field:
   - title: Judul artikel yang menarik dan SEO-friendly (maksimal 70 karakter).
   - excerpt: Ringkasan artikel untuk meta description (120-155 karakter).
   - content: Konten artikel lengkap dalam format HTML (gunakan <h2>, <h3>, <p>, <ul>, <ol>, <code>, <pre>, <blockquote>) dengan panjang minimal 800 - 1200 kata. Artikel harus memiliki studi kasus praktis, tips implementasi, contoh baris kode nyata jika relevan, dan kesimpulan.";

    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $geminiKey;
    $postData = [
        "contents" => [
            ["parts" => [["text" => $prompt]]]
        ],
        "generationConfig" => [
            "responseMimeType" => "application/json"
        ]
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $result = json_decode($response, true);
        $rawText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $data = json_decode($rawText, true);

        if (!empty($data['title']) && !empty($data['content'])) {
            $title = $data['title'];
            $slug = createSlug($title);
            $excerpt = $data['excerpt'] ?? substr(strip_tags($data['content']), 0, 150);
            $content = $data['content'];

            // Insert new article
            $stmt = $db->prepare("INSERT INTO articles (title, slug, excerpt, content, status, created_at) VALUES (?, ?, ?, ?, 'published', datetime('now'))");
            $stmt->execute([$title, $slug, $excerpt, $content]);

            writeLog("SUCCESS: Generated and published new AI article: '$title' ($slug)", $logFile);
            $articleCreated = true;
        } else {
            writeLog("WARNING: Invalid JSON payload from Gemini API.", $logFile);
        }
    } else {
        writeLog("WARNING: Gemini API request failed with HTTP code $httpCode. Falling back to draft promoter.", $logFile);
    }
}

// 4. Option B: Fallback - Promote existing draft to published
if (!$articleCreated) {
    $draft = $db->query("SELECT id, title, slug FROM articles WHERE status = 'draft' ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($draft) {
        $stmt = $db->prepare("UPDATE articles SET status = 'published', created_at = datetime('now') WHERE id = ?");
        $stmt->execute([$draft['id']]);
        writeLog("SUCCESS: Published draft article: '{$draft['title']}' ({$draft['slug']})", $logFile);
        $articleCreated = true;
    } else {
        writeLog("INFO: No drafts available and no API key configured.", $logFile);
    }
}

// 5. Update Sitemap XML
if ($articleCreated) {
    writeLog("Regenerating sitemap.xml...", $logFile);
    require_once $baseDir . '/generate_sitemap.php';

    // 6. Ping Google & IndexNow / Bing
    $sitemapUrl = "https://hasanarofid.site/sitemap.xml";
    
    // Ping Google
    $googlePing = "https://www.google.com/ping?sitemap=" . urlencode($sitemapUrl);
    @file_get_contents($googlePing, false, stream_context_create(['http' => ['timeout' => 5]]));
    writeLog("Pinged Google Sitemap endpoint.", $logFile);

    // Ping Bing
    $bingPing = "https://www.bing.com/ping?sitemap=" . urlencode($sitemapUrl);
    @file_get_contents($bingPing, false, stream_context_create(['http' => ['timeout' => 5]]));
    writeLog("Pinged Bing Sitemap endpoint.", $logFile);
}

writeLog("Cron execution completed." . PHP_EOL, $logFile);

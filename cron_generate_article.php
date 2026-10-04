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

    // List of high-value, problem-solving, long-tail topics targeting search intent & real developer problems
    $topics = [
        "Cara Mengatasi Error SQLite Database is Locked pada Aplikasi PHP dan Web Cron Job",
        "Panduan Konfigurasi LiteSpeed Cache & OPcache untuk Optimasi Performa PHP Skala Tinggi",
        "Tutorial Lengkap Implementasi JWT Authentication dengan Refresh Token & Rate Limiting di Node.js",
        "Studi Kasus: Cara Menurunkan LCP dan INP (Core Web Vitals) di Bawah 1.2 Detik Tanpa Framework Berat",
        "Panduan Migrasi Database SQLite ke PostgreSQL Menggunakan pgloader Tanpa Downtime",
        "Cara Mencegah Serangan SQL Injection, XSS, dan CSRF pada Aplikasi PHP Native dan Framework",
        "Perbandingan Mendalam Next.js Server Components vs Vite SPA: Kapan Harus Menggunakan Masing-Masing?",
        "Panduan Setup Automated Backup Database SQLite dan MySQL ke Cloud Storage Menggunakan Bash Script & Cron Job",
        "Optimalisasi Query Database Relasional: Menggunakan Indexing B-Tree, Explain Analyze, dan Partitioning",
        "Cara Membangun WhatsApp Notification Gateway Menggunakan Node.js dan Midtrans Webhook",
        "Panduan Lengkap Implementasi Structured Data Schema.org (Article, FAQ, Breadcrumb) untuk Meningkatkan CTR Google",
        "Teknik Debouncing dan Throttling pada JavaScript Modern untuk Optimasi Kinerja DOM",
        "Mengamankan REST API dengan Cloudflare WAF, Rate Limiter, dan IP Whitelisting",
        "Panduan Clean Architecture pada PHP Modern: Memisahkan Domain, Repository, dan Service Layer",
        "Cara Mengurangi Ukuran Bundle JavaScript Frontend Hingga 60% dengan Dynamic Import dan Tree Shaking"
    ];

    // Filter out topics that already exist in titles
    $existing = $db->query("SELECT title FROM articles")->fetchAll(PDO::FETCH_COLUMN);
    $availableTopics = array_filter($topics, function($t) use ($existing) {
        foreach ($existing as $e) {
            if (stripos($e, substr($t, 0, 25)) !== false) return false;
        }
        return true;
    });

    if (empty($availableTopics)) {
        $selectedTopic = $topics[array_rand($topics)];
    } else {
        $selectedTopic = $availableTopics[array_rand($availableTopics)];
    }

    $prompt = "Anda adalah Senior Fullstack Software Engineer & Web Architect (Hasan Arofid) dengan pengalaman 10+ tahun. Tulis artikel panduan teknis mendalam, profesional, dan kaya solusi praktis dalam bahasa Indonesia tentang topik: '{$selectedTopic}'.

Ketentuan Kualitas Konten (Standar AdSense E-E-A-T & SEO On-Page):
1. Format output HANYA JSON murni (tanpa tanda ```json atau markdown codeblock di luar JSON).
2. JSON harus berisi field:
   - title: Judul artikel yang spesifik, solutif, dan memikat pembaca teknis (maksimal 70 karakter).
   - category: Kategori yang sesuai (pilih salah satu: 'Web Development', 'Backend & Database', 'DevOps & Cloud', 'Keamanan & SEO', 'Arsitektur Web').
   - excerpt: Ringkasan artikel padat informasi untuk meta description Google (130-155 karakter).
   - content: Isi konten artikel lengkap dalam format HTML bersih (panjang 1.200 - 1.800 kata).
     Struktur konten Wajib mencakup:
     - <h2>Pendahuluan & Konteks Masalah</h2> (Latar belakang teknis nyata mengapa masalah ini penting diatasi).
     - <h2>Penyebab Akar Masalah (Root Cause) & Analisis Teknis</h2>.
     - <h2>Solusi Praktis & Panduan Langkah Demi Langkah</h2> (Gunakan <h3>, <pre><code> untuk contoh baris kode nyata yang siap pakai, dan <ol>/<ul>).
     - <h2>Benchmark / Perbandingan / Studi Kasus Proyek</h2> (Sertakan data perbandingan atau skenario kasus nyata).
     - <h2>Best Practice & Kesalahan Umum yang Harus Dihindari</h2> (Gunakan <blockquote> untuk tips penting atau peringatan).
     - <h2>Pertanyaan yang Sering Diajukan (FAQ)</h2> (Sertakan minimal 3 pertanyaan dan jawaban menggunakan <h3> dan <p>).
     - <h2>Kesimpulan & Rekomendasi Arsitektur</h2>.";

    $candidateModels = [
        'gemini-3.1-flash-lite',
        'gemini-flash-latest',
        'gemini-3.8-flash',
        'gemini-3.7-flash'
    ];
    $postData = [
        "contents" => [
            ["parts" => [["text" => $prompt]]]
        ],
        "generationConfig" => [
            "responseMimeType" => "application/json"
        ]
    ];

    foreach ($candidateModels as $modelName) {
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . $geminiKey;

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $result = json_decode($response, true);
            $rawText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            // Sanitize markdown fences if present
            $rawText = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText)));
            $data = json_decode($rawText, true);

            if (!empty($data['title']) && !empty($data['content'])) {
                $title = $data['title'];
                $slug = createSlug($title);
                $excerpt = $data['excerpt'] ?? substr(strip_tags($data['content']), 0, 150);
                $content = $data['content'];

                // Insert new article
                $stmt = $db->prepare("INSERT INTO articles (title, slug, excerpt, content, status, created_at) VALUES (?, ?, ?, ?, 'published', datetime('now'))");
                $stmt->execute([$title, $slug, $excerpt, $content]);

                writeLog("SUCCESS: Generated and published high-value E-E-A-T article via $modelName: '$title' ($slug)", $logFile);
                $articleCreated = true;
                break;
            } else {
                writeLog("Model $modelName responded with invalid JSON structure, trying next...", $logFile);
            }
        } else {
            $errDetail = !empty($curlError) ? "cURL error: $curlError" : "HTTP $httpCode";
            writeLog("Model $modelName returned $errDetail, trying next...", $logFile);
            sleep(2);
        }
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

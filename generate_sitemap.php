<?php
$baseUrl = 'https://hasanarofid.site/';

$urls = [
    ['loc' => $baseUrl, 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '1.0'],
    ['loc' => $baseUrl . 'about', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['loc' => $baseUrl . 'articles', 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '0.9'],
    ['loc' => $baseUrl . 'portofolio', 'lastmod' => date('Y-m-d'), 'changefreq' => 'weekly', 'priority' => '0.9'],
    ['loc' => $baseUrl . 'products', 'lastmod' => date('Y-m-d'), 'changefreq' => 'weekly', 'priority' => '0.8'],
    ['loc' => $baseUrl . 'contact', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['loc' => $baseUrl . 'privacy-policy', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => $baseUrl . 'terms-of-service', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => $baseUrl . 'disclaimer', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'],
];

// Portfolio project detail clean URLs
$portfolioProjects = [
    'school-system',
    'mr-lux',
    'nitajaya',
    'mitrasyiar-baitullah',
    'afpro-aquarium',
    'nolimits-training',
    'amtech-ev',
    'gringgo'
];

foreach ($portfolioProjects as $project) {
    $urls[] = [
        'loc' => $baseUrl . 'portofolio/' . $project,
        'lastmod' => date('Y-m-d'),
        'changefreq' => 'weekly',
        'priority' => '0.8'
    ];
}

// Fetch published articles
$dbFile = __DIR__ . '/stats.db';
if (file_exists($dbFile)) {
    try {
        $db = new PDO('sqlite:' . $dbFile);
        $stmt = $db->query("SELECT slug, created_at FROM articles WHERE status = 'published' ORDER BY created_at DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $urls[] = [
                'loc' => $baseUrl . 'blog/' . $row['slug'],
                'lastmod' => date('Y-m-d', strtotime($row['created_at'])),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }
    } catch (Exception $e) {
        $output = shell_exec("sqlite3 " . escapeshellarg($dbFile) . " \"SELECT slug, created_at FROM articles WHERE status = 'published' ORDER BY created_at DESC;\"");
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (empty($line)) continue;
                $parts = explode("|", $line);
                if (count($parts) >= 2) {
                    $urls[] = [
                        'loc' => $baseUrl . 'blog/' . $parts[0],
                        'lastmod' => date('Y-m-d', strtotime($parts[1])),
                        'changefreq' => 'weekly',
                        'priority' => '0.7'
                    ];
                }
            }
        }
    }
}

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

foreach ($urls as $url) {
    $xml .= '    <url>' . PHP_EOL;
    $xml .= '        <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
    $xml .= '        <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
    $xml .= '        <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
    $xml .= '        <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
    $xml .= '    </url>' . PHP_EOL;
}

$xml .= '</urlset>';

file_put_contents(__DIR__ . '/sitemap.xml', $xml);
echo "Sitemap generated successfully!";

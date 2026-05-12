<?php
$baseUrl = 'https://hasanarofid.site/';

$urls = [
    ['loc' => $baseUrl, 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '1.0'],
    ['loc' => $baseUrl . 'articles.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'daily', 'priority' => '0.9'],
    ['loc' => $baseUrl . 'privacy-policy.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => $baseUrl . 'terms-of-service.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.5'],
    ['loc' => $baseUrl . 'contact.php', 'lastmod' => date('Y-m-d'), 'changefreq' => 'monthly', 'priority' => '0.8'],
];

// Fetch articles via CLI if PDO driver is missing
$output = shell_exec("sqlite3 stats.db \"SELECT slug, created_at FROM articles WHERE status = 'published';\"");
if ($output) {
    $lines = explode("\n", trim($output));
    foreach ($lines as $line) {
        if (empty($line)) continue;
        list($slug, $created_at) = explode("|", $line);
        $urls[] = [
            'loc' => $baseUrl . 'blog/' . $slug,
            'lastmod' => date('Y-m-d', strtotime($created_at)),
            'changefreq' => 'weekly',
            'priority' => '0.7'
        ];
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

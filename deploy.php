<?php
// Secret token untuk autentikasi webhook GitHub
$secret_token = 'hasan_deploy_secret_key_123';

$payload = file_get_contents('php://input');
$signature256 = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$tokenParam = $_GET['token'] ?? '';

$authorized = false;

// 1. Validasi via Token URL (?token=...)
if ($tokenParam && hash_equals($secret_token, $tokenParam)) {
    $authorized = true;
}

// 2. Validasi via GitHub Webhook Secret Header (X-Hub-Signature-256)
if (!$authorized && $signature256) {
    $expectedHash = 'sha256=' . hash_hmac('sha256', $payload, $secret_token);
    if (hash_equals($expectedHash, $signature256)) {
        $authorized = true;
    }
}

if (!$authorized) {
    http_response_code(403);
    exit('Unauthorized access');
}

// Path repository di server cPanel Rumahweb
$repoPath = '/home/nitw3991/public_html/hasanarofid.site';

// Eksekusi git pull (abaikan perubahan lokal sitemap agar tidak konflik)
$output = shell_exec("cd $repoPath && git checkout -- sitemap.xml 2>&1 && git pull origin main 2>&1");

header('Content-Type: text/plain');
echo "Deployment Output:\n$output";

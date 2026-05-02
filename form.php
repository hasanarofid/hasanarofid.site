<?php
session_start();
$dbFile = __DIR__ . '/stats.db';
$password = "DemiAllah@1";

// Handle Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: form.php");
    exit;
}

// Handle Login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: form.php");
        exit;
    } else {
        http_response_code(403);
        die("<h1>403 Forbidden</h1><p>Akses ditolak. Password salah.</p>");
    }
}

// Check Authentication
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>403 Forbidden</title>
        <link rel="icon" type="image/png" href="images/logohasanarofid.png" />

        <style>
            :root { --bg: #f8fafc; --surface: #ffffff; --border: rgba(0, 0, 0, 0.08); --text: #0f172a; }
            [data-theme="dark"] { --bg: #030712; --surface: #0f172a; --border: rgba(255, 255, 255, 0.08); --text: #f8fafc; }

            body { background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
            .container { text-align: center; }
            h1 { cursor: pointer; user-select: none; }
            input { padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--text); margin-top: 20px; }
            button { padding: 12px 24px; border-radius: 8px; border: none; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; cursor: pointer; }
            #login-section { display: none; margin-top: 20px; }
            .theme-toggle { position: fixed; top: 20px; right: 20px; background: var(--surface); border: 1px solid var(--border); color: var(--text); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; }
        </style>
    </head>
    <body>
        <div class="theme-toggle" id="themeToggle">🌓</div>
        <div class="container">
            <h1 onclick="document.getElementById('login-section').style.display='block'">403 Forbidden</h1>
            <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
            <div id="login-section">
                <form method="POST">
                    <input type="password" name="password" placeholder="Password" autofocus>
                    <button type="submit" name="login">Login</button>
                </form>
            </div>
        </div>
        <script>
            {
                const themeToggle = document.getElementById('themeToggle');
                const currentTheme = localStorage.getItem('theme') || 'light';
                if (currentTheme === 'dark') { document.documentElement.setAttribute('data-theme', 'dark'); themeToggle.textContent = '☀️'; }
                else { themeToggle.textContent = '🌓'; }
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

            }
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Database Connection
try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed.");
}

$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle Add or Update Product
if (isset($_POST['save_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $link = $_POST['link'];
    $image_url = $_POST['image_url'];
    $platform = $_POST['platform'];
    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, link = ?, image_url = ?, platform = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $link, $image_url, $platform, $id]);
        $msg = "updated";
    } else {
        $stmt = $db->prepare("INSERT INTO products (name, description, price, link, image_url, platform) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $link, $image_url, $platform]);
        $msg = "success";
    }
    header("Location: form.php?$msg=1");
    exit;
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: form.php?deleted=1");
    exit;
}

// Fetch Products
$products = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Digital Products | Hasan Arofid</title>
    <link rel="icon" type="image/png" href="images/logohasanarofid.png" />

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #f8fafc; --surface: #ffffff; --border: rgba(0,0,0,0.08); --text: #0f172a; --accent: #3b82f6; }
        [data-theme="dark"] { --bg: #030712; --surface: #0f172a; --border: rgba(255,255,255,0.08); --text: #f8fafc; }

        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .card { background: var(--surface); border: 1px solid var(--border); padding: 32px; border-radius: 16px; margin-bottom: 32px; }
        h1, h2 { margin: 0 0 24px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; }
        input, textarea, select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--text); font-family: inherit; box-sizing: border-box; }
        button { background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; width: 100%; }
        .product-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; border-bottom: 1px solid var(--border); }
        .product-item:last-child { border-bottom: none; }
        .actions { display: flex; gap: 16px; }
        .delete-btn { color: #f87171; text-decoration: none; font-weight: 600; }
        .edit-btn { color: #60a5fa; text-decoration: none; font-weight: 600; }
        .logout-btn { color: var(--text); text-decoration: none; opacity: 0.7; }
        .platform-tag { font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; background: rgba(255,255,255,0.1); margin-left: 8px; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 24px; text-align: center; font-weight: 600; }
        .alert-success { background: rgba(74, 222, 128, 0.1); color: #4ade80; }
        .theme-toggle { background: var(--surface); border: 1px solid var(--border); color: var(--text); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div style="display: flex; align-items: center; gap: 16px;">
                <h1 style="cursor: pointer;" onclick="window.location.href='form.php'">Kelola Produk Digital</h1>
                <div class="theme-toggle" id="themeToggle">🌓</div>
            </div>
            <a href="?logout=1" class="logout-btn">Logout</a>
        </header>

        <?php if (isset($_GET['success'])): ?> <div class="alert alert-success">Produk berhasil ditambahkan!</div> <?php endif; ?>
        <?php if (isset($_GET['updated'])): ?> <div class="alert alert-success">Produk berhasil diperbarui!</div> <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?> <div class="alert alert-success">Produk berhasil dihapus!</div> <?php endif; ?>

        <div class="card">
            <h2><?= $editProduct ? 'Edit Produk' : 'Tambah Produk Baru' ?></h2>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $editProduct['id'] ?? '' ?>">
                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" name="name" required placeholder="Contoh: Laravel Starter Kit" value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Deskripsi Singkat</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan produk Anda..."><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Harga (Tampilkan)</label>
                    <input type="text" name="price" placeholder="Contoh: $9 atau Rp 150.000" value="<?= htmlspecialchars($editProduct['price'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Link Produk (Gumroad/Lynk)</label>
                    <input type="url" name="link" required placeholder="https://gumroad.com/l/..." value="<?= htmlspecialchars($editProduct['link'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>URL Gambar Preview</label>
                    <input type="url" name="image_url" placeholder="https://hasanarofid.site/images/..." value="<?= htmlspecialchars($editProduct['image_url'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Platform</label>
                    <select name="platform">
                        <option value="gumroad" <?= (isset($editProduct['platform']) && $editProduct['platform'] === 'gumroad') ? 'selected' : '' ?>>Gumroad</option>
                        <option value="lynk" <?= (isset($editProduct['platform']) && $editProduct['platform'] === 'lynk') ? 'selected' : '' ?>>Lynk.id</option>
                    </select>
                </div>
                <button type="submit" name="save_product"><?= $editProduct ? 'Perbarui Produk' : 'Simpan Produk' ?></button>
                <?php if ($editProduct): ?>
                    <a href="form.php" style="display: block; text-align: center; margin-top: 16px; color: var(--text-muted); text-decoration: none;">Batal Edit</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h2>Daftar Produk</h2>
            <?php if (empty($products)): ?>
                <p style="opacity: 0.5;">Belum ada produk.</p>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <div class="product-item">
                        <div>
                            <strong><?= htmlspecialchars($p['name']) ?></strong>
                            <span class="platform-tag"><?= strtoupper($p['platform']) ?></span>
                            <div style="font-size: 0.8rem; opacity: 0.6;"><?= htmlspecialchars($p['price']) ?></div>
                        </div>
                        <div class="actions">
                            <a href="?edit=<?= $p['id'] ?>" class="edit-btn">Edit</a>
                            <a href="?delete=<?= $p['id'] ?>" class="delete-btn" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script>
        {
            const themeToggle = document.getElementById('themeToggle');
            const currentTheme = localStorage.getItem('theme') || 'light';
            if (currentTheme === 'dark') { document.documentElement.setAttribute('data-theme', 'dark'); themeToggle.textContent = '☀️'; }
            else { themeToggle.textContent = '🌓'; }
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

        }
    </script>
</body>
</html>

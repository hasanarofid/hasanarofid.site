<?php
session_start();
$dbFile = __DIR__ . '/stats.db';
$password = "DemiAllah@1";

// Handle Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: admin");
    exit;
}

// Handle Login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin");
        exit;
    } else {
        $error = "Password salah!";
    }
}

// Check Authentication
if (!isset($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login | Hasan Arofid</title>
        <link rel="icon" type="image/png" href="images/logohasanarofid.png" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            :root { --bg: #030712; --surface: #0f172a; --border: rgba(255,255,255,0.08); --text: #f8fafc; --accent: #3b82f6; }
            body { background: var(--bg); color: var(--text); font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
            .login-card { background: var(--surface); border: 1px solid var(--border); padding: 40px; border-radius: 24px; width: 100%; max-width: 400px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
            h1 { margin: 0 0 8px; font-size: 1.5rem; text-align: center; }
            p { text-align: center; color: #94a3b8; margin-bottom: 32px; font-size: 0.9rem; }
            input { width: 100%; padding: 14px; border-radius: 12px; border: 1px solid var(--border); background: #1e293b; color: white; margin-bottom: 20px; box-sizing: border-box; font-size: 1rem; }
            button { width: 100%; padding: 14px; border-radius: 12px; border: none; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; font-weight: 700; cursor: pointer; font-size: 1rem; transition: 0.3s; }
            button:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3); }
            .error { color: #f87171; text-align: center; margin-bottom: 16px; font-size: 0.85rem; }
        </style>
    </head>
    <body>
        <div class="login-card">
            <h1>Admin Panel</h1>
            <p>Optimization & Content Hub</p>
            <?php if (isset($error)): ?> <div class="error"><?= $error ?></div> <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Masukkan Password" autofocus required>
                <button type="submit" name="login">Masuk ke Dashboard</button>
            </form>
        </div>
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

$tab = $_GET['tab'] ?? 'dashboard';

// Handle SEO Update
if (isset($_POST['update_seo'])) {
    $stmt = $db->prepare("UPDATE seo_settings SET title = ?, description = ?, keywords = ? WHERE page_name = 'homepage'");
    $stmt->execute([$_POST['title'], $_POST['description'], $_POST['keywords']]);
    $msg = "SEO Berhasil diperbarui!";
}

// Handle Article Save
if (isset($_POST['save_article'])) {
    $title = $_POST['title'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $content = $_POST['content'];
    $excerpt = $_POST['excerpt'];
    $id = $_POST['id'] ?? null;

    if ($id) {
        $stmt = $db->prepare("UPDATE articles SET title = ?, slug = ?, content = ?, excerpt = ? WHERE id = ?");
        $stmt->execute([$title, $slug, $content, $excerpt, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO articles (title, slug, content, excerpt) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $content, $excerpt]);
    }
    header("Location: admin?tab=articles&success=1");
    exit;
}

// Handle Article Delete
if (isset($_GET['delete_article'])) {
    $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$_GET['delete_article']]);
    header("Location: admin?tab=articles&deleted=1");
    exit;
}

// Fetch Data
$seo = $db->query("SELECT * FROM seo_settings WHERE page_name = 'homepage'")->fetch(PDO::FETCH_ASSOC);
$articles = $db->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$totalVisits = $db->query("SELECT COUNT(*) FROM visits")->fetchColumn();
$todayVisits = $db->query("SELECT COUNT(*) FROM visits WHERE date(created_at) = date('now')")->fetchColumn();
$recentVisits = $db->query("SELECT * FROM visits ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

$editArticle = null;
if (isset($_GET['edit_article'])) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$_GET['edit_article']]);
    $editArticle = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Hasan Arofid</title>
    <link rel="icon" type="image/png" href="images/logohasanarofid.png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        :root { 
            --bg: #030712; 
            --sidebar: #0f172a; 
            --surface: #1e293b; 
            --border: rgba(255,255,255,0.06); 
            --text: #f8fafc; 
            --text-muted: #94a3b8;
            --accent: #3b82f6; 
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; overflow-x: hidden; }
        
        /* Layout */
        .admin-layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        aside { 
            width: 280px; 
            background: var(--sidebar); 
            border-right: 1px solid var(--border); 
            display: flex; 
            flex-direction: column; 
            padding: 32px 24px; 
            position: fixed; 
            height: 100vh; 
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .logo { font-weight: 800; font-size: 1.2rem; margin-bottom: 48px; display: flex; align-items: center; gap: 12px; }
        .logo img { height: 32px; }
        nav { flex-grow: 1; }
        nav a { display: flex; align-items: center; gap: 12px; padding: 14px 18px; color: var(--text-muted); text-decoration: none; border-radius: 12px; transition: 0.3s; margin-bottom: 6px; font-weight: 500; }
        nav a:hover, nav a.active { background: rgba(59, 130, 246, 0.1); color: var(--accent); }
        .logout { margin-top: auto; color: #f87171; }

        /* Main Content */
        main { flex-grow: 1; padding: 60px; margin-left: 280px; width: calc(100% - 280px); transition: 0.3s; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        h1 { margin: 0; font-size: 1.75rem; }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background: var(--sidebar);
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 900;
            justify-content: space-between;
            align-items: center;
        }
        .menu-btn {
            background: none;
            border: none;
            color: var(--text);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 950;
            display: none;
        }
        .overlay.active { display: block; }

        /* Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: var(--surface); padding: 24px; border-radius: 20px; border: 1px solid var(--border); }
        .stat-card h3 { margin: 0; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .stat-card p { margin: 12px 0 0; font-size: 2rem; font-weight: 700; }

        .card { background: var(--surface); padding: 32px; border-radius: 24px; border: 1px solid var(--border); margin-bottom: 32px; overflow-x: auto; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .card-header h2 { margin: 0; font-size: 1.25rem; }

        /* Form */
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-muted); font-size: 0.9rem; }
        input, textarea, select { width: 100%; padding: 14px; border-radius: 12px; border: 1px solid var(--border); background: #0f172a; color: white; font-family: inherit; box-sizing: border-box; font-size: 0.95rem; }
        input:focus, textarea:focus { outline: none; border-color: var(--accent); }
        .btn { padding: 12px 24px; border-radius: 12px; border: none; background: var(--accent); color: white; font-weight: 700; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }
        .btn-success { background: linear-gradient(135deg, #10b981, #059669); }

        /* Quill Styles */
        #editor { height: 400px; background: #0f172a; color: white; border-radius: 0 0 12px 12px; border: 1px solid var(--border); font-size: 1rem; }
        .ql-toolbar { background: #1e293b; border: 1px solid var(--border) !important; border-radius: 12px 12px 0 0; }
        .ql-container { border: 1px solid var(--border) !important; border-radius: 0 0 12px 12px; }
        .ql-stroke { stroke: #94a3b8 !important; }
        .ql-fill { fill: #94a3b8 !important; }
        .ql-picker { color: #94a3b8 !important; }

        /* Table */
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { text-align: left; padding: 16px; color: var(--text-muted); font-size: 0.85rem; border-bottom: 1px solid var(--border); }
        td { padding: 16px; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
        .badge { padding: 4px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-blue { background: rgba(59, 130, 246, 0.1); color: var(--accent); }

        .alert { padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; }
        .alert-success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }

        @media (max-width: 1100px) {
            aside { transform: translateX(-100%); }
            aside.active { transform: translateX(0); }
            main { margin-left: 0; width: 100%; padding: 32px 24px; }
            .mobile-header { display: flex; }
            header h1 { font-size: 1.5rem; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>

    <div class="mobile-header">
        <div class="logo" style="margin-bottom: 0;">
            <img src="images/logohasanarofid.png" alt="Logo">
            <span>Admin</span>
        </div>
        <button class="menu-btn" id="menuBtn">☰</button>
    </div>

    <div class="admin-layout">
        <aside id="sidebar">
            <div class="logo">
                <img src="images/logohasanarofid.png" alt="Logo">
                <span>Admin Hub</span>
            </div>
            <nav>
                <a href="admin?tab=dashboard" class="<?= $tab == 'dashboard' ? 'active' : '' ?>">
                    <span>📊</span> <span>Dashboard</span>
                </a>
                <a href="admin?tab=seo" class="<?= $tab == 'seo' ? 'active' : '' ?>">
                    <span>🔍</span> <span>SEO Settings</span>
                </a>
                <a href="admin?tab=articles" class="<?= $tab == 'articles' ? 'active' : '' ?>">
                    <span>✍️</span> <span>Articles</span>
                </a>
                <a href="form" target="_blank">
                    <span>📦</span> <span>Products</span>
                </a>
                <a href="trafik" target="_blank">
                    <span>📈</span> <span>Traffic</span>
                </a>
            </nav>
            <a href="?logout=1" class="nav-link logout">
                <span>🚪</span> <span>Logout</span>
            </a>
        </aside>

        <main>
            <?php if ($tab == 'dashboard'): ?>
                <header>
                    <h1>Dashboard Overview</h1>
                </header>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Visits</h3>
                        <p><?= number_format($totalVisits) ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Visits Today</h3>
                        <p><?= number_format($todayVisits) ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Articles</h3>
                        <p><?= count($articles) ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Recent Traffic</h2>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>IP Address</th>
                                <th>Country</th>
                                <th>Device</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentVisits as $v): ?>
                            <tr>
                                <td><?= date('d M, H:i', strtotime($v['created_at'])) ?></td>
                                <td><code><?= substr($v['ip_hash'], 0, 8) ?>...</code></td>
                                <td><?= htmlspecialchars($v['country']) ?></td>
                                <td><span class="badge badge-blue"><?= $v['device_type'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($tab == 'seo'): ?>
                <header>
                    <h1>SEO Optimization</h1>
                </header>

                <?php if (isset($msg)): ?> <div class="alert alert-success"><?= $msg ?></div> <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h2>Homepage Metadata</h2>
                    </div>
                    <form method="POST">
                        <div class="form-group">
                            <label>Meta Title</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($seo['title']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="description" rows="4" required><?= htmlspecialchars($seo['description']) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Keywords (comma separated)</label>
                            <input type="text" name="keywords" value="<?= htmlspecialchars($seo['keywords']) ?>">
                        </div>
                        <button type="submit" name="update_seo" class="btn btn-success">Update SEO Data</button>
                    </form>
                </div>

            <?php elseif ($tab == 'articles'): ?>
                <header>
                    <h1>Articles & SEO Content</h1>
                    <a href="admin?tab=articles&action=new" class="btn">+ Write Article</a>
                </header>

                <?php if (isset($_GET['success'])): ?> <div class="alert alert-success">Article saved successfully!</div> <?php endif; ?>

                <?php if (isset($_GET['action']) || isset($_GET['edit_article'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <h2><?= $editArticle ? 'Edit Article' : 'New Article' ?></h2>
                        </div>
                        <form method="POST" id="articleForm">
                            <input type="hidden" name="id" value="<?= $editArticle['id'] ?? '' ?>">
                            <input type="hidden" name="content" id="contentInput">
                            
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" value="<?= htmlspecialchars($editArticle['title'] ?? '') ?>" required placeholder="Enter catchy title...">
                            </div>
                            <div class="form-group">
                                <label>Excerpt (Short Summary for SEO)</label>
                                <textarea name="excerpt" rows="2" required placeholder="Short summary for Google search results..."><?= htmlspecialchars($editArticle['excerpt'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Content (Visual Editor)</label>
                                <div id="editor"><?= $editArticle['content'] ?? '' ?></div>
                            </div>
                            <button type="submit" name="save_article" class="btn btn-success">Publish Article</button>
                            <a href="admin?tab=articles" class="btn" style="background: transparent; color: var(--text-muted);">Cancel</a>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <table>
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articles as $a): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($a['title']) ?></strong><br><small style="color: var(--text-muted)">/blog/<?= $a['slug'] ?></small></td>
                                    <td><span class="badge badge-blue">Published</span></td>
                                    <td><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                                    <td>
                                        <a href="admin?tab=articles&edit_article=<?= $a['id'] ?>" style="color: var(--accent); text-decoration: none;">Edit</a> | 
                                        <a href="admin?tab=articles&delete_article=<?= $a['id'] ?>" style="color: #f87171; text-decoration: none;" onclick="return confirm('Delete this article?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($articles)): ?>
                                    <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No articles yet. Start writing!</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </main>
    </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        const menuBtn = document.getElementById('menuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        const toggleMenu = () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        };

        if (menuBtn) menuBtn.addEventListener('click', toggleMenu);
        if (overlay) overlay.addEventListener('click', toggleMenu);

        // Auto close sidebar on resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1100) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });

        // Initialize Quill Editor
        if (document.getElementById('editor')) {
            var quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            // Handle form submission
            var form = document.getElementById('articleForm');
            form.onsubmit = function() {
                var contentInput = document.getElementById('contentInput');
                contentInput.value = quill.root.innerHTML;
            };
        }
    </script>
</body>
</html>

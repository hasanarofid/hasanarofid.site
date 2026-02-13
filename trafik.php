<?php
$dbFile = __DIR__ . '/stats.db';
$data = [];
$labels = [];
$totalVisits = 0;
$uniqueVisitors = 0;

try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get daily stats for the last 14 days
    $stmt = $db->query("SELECT visit_date, COUNT(*) as count, COUNT(DISTINCT ip_hash) as unique_count 
                        FROM visits 
                        GROUP BY visit_date 
                        ORDER BY visit_date DESC 
                        LIMIT 14");
    $rawStats = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

    foreach ($rawStats as $row) {
        $labels[] = date('d M', strtotime($row['visit_date']));
        $data[] = $row['count'];
        $uniqueData[] = $row['unique_count'];
    }

    // Get total stats
    $totalVisits = $db->query("SELECT COUNT(*) FROM visits")->fetchColumn();
    $uniqueVisitors = $db->query("SELECT COUNT(DISTINCT ip_hash) FROM visits")->fetchColumn();

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Trafik Pengunjung | Hasan Arofid</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
      --shadow: 0 20px 70px rgba(0, 0, 0, 0.35);
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      margin: 0;
      background: var(--bg);
      color: var(--text);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    .container {
      width: 100%;
      max-width: 800px;
      background: var(--panel);
      border: 1px solid var(--stroke);
      border-radius: 18px;
      padding: 32px;
      box-shadow: var(--shadow);
      backdrop-filter: blur(12px);
    }
    h1 { margin-top: 0; color: var(--accent); }
    .stats-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 32px;
    }
    .stat-card {
      background: var(--card);
      padding: 20px;
      border-radius: 12px;
      border: 1px solid var(--stroke);
    }
    .stat-label { color: var(--muted); font-size: 0.9rem; }
    .stat-value { font-size: 1.8rem; font-weight: bold; margin-top: 4px; }
    canvas { margin-top: 20px; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Trafik Pengunjung</h1>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Total Kunjungan</div>
        <div class="stat-value"><?php echo number_format($totalVisits); ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Pengunjung Unik</div>
        <div class="stat-value"><?php echo number_format($uniqueVisitors); ?></div>
      </div>
    </div>
    <canvas id="trafficChart"></canvas>
    <div style="margin-top: 24px; text-align: right;">
        <a href="index.php" style="color: var(--muted); text-decoration: none; font-size: 0.9rem;">← Kembali ke Beranda</a>
    </div>
  </div>

  <script>
    const ctx = document.getElementById('trafficChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Total Kunjungan',
          data: <?php echo json_encode($data); ?>,
          borderColor: '#38bdf8',
          backgroundColor: 'rgba(56, 189, 248, 0.1)',
          fill: true,
          tension: 0.4
        }, {
          label: 'Pengunjung Unik',
          data: <?php echo json_encode($uniqueData); ?>,
          borderColor: '#a855f7',
          backgroundColor: 'rgba(168, 85, 247, 0.1)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            labels: { color: '#e5e7eb' }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(255, 255, 255, 0.05)' },
            ticks: { color: '#94a3b8' }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#94a3b8' }
          }
        }
      }
    });
  </script>
</body>
</html>

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

    // SEO Analysis: Bot vs Real User
    $botStats = $db->query("SELECT is_bot, COUNT(*) as count FROM visits GROUP BY is_bot")->fetchAll(PDO::FETCH_ASSOC);
    $seoData = [0, 0]; // [Users, Bots]
    foreach ($botStats as $row) {
        $seoData[$row['is_bot']] = $row['count'];
    }

    // Device Analysis: Desktop vs Mobile
    $deviceStats = $db->query("SELECT device_type, COUNT(*) as count FROM visits WHERE device_type IS NOT NULL GROUP BY device_type")->fetchAll(PDO::FETCH_ASSOC);
    $deviceLabels = [];
    $deviceValues = [];
    foreach ($deviceStats as $row) {
        $deviceLabels[] = $row['device_type'];
        $deviceValues[] = $row['count'];
    }

    // Geo Analysis: Country Breakdown
    $geoStats = $db->query("SELECT country, COUNT(*) as count FROM visits WHERE country IS NOT NULL AND country != 'Unknown' GROUP BY country ORDER BY count DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $geoLabels = [];
    $geoValues = [];
    foreach ($geoStats as $row) {
        $geoLabels[] = $row['country'];
        $geoValues[] = $row['count'];
    }

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
  <title>Analytics Dashboard | Hasan Arofid</title>
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
      --purple: #a855f7;
      --shadow: 0 20px 70px rgba(0, 0, 0, 0.35);
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      margin: 0;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      padding: 40px 20px;
    }
    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
    }
    .header { margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; }
    h1 { margin: 0; color: var(--accent); font-size: 2rem; }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
      margin-bottom: 32px;
    }
    .stat-card {
      background: var(--panel);
      padding: 24px;
      border-radius: 18px;
      border: 1px solid var(--stroke);
      box-shadow: var(--shadow);
      backdrop-filter: blur(12px);
    }
    .stat-label { color: var(--muted); font-size: 0.9rem; margin-bottom: 8px; }
    .stat-value { font-size: 2rem; font-weight: bold; }
    
    .charts-main {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 24px;
      margin-bottom: 24px;
    }
    
    .charts-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
    }
    
    .chart-container {
      background: var(--panel);
      border: 1px solid var(--stroke);
      border-radius: 18px;
      padding: 24px;
      box-shadow: var(--shadow);
      backdrop-filter: blur(12px);
    }
    .chart-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--text);
    }
    
    .footer-actions {
      margin-top: 40px;
      padding: 20px;
      text-align: center;
      border-top: 1px solid var(--stroke);
    }
    .btn-back {
      color: var(--muted);
      text-decoration: none;
      font-size: 0.95rem;
      transition: color 150ms;
    }
    .btn-back:hover { color: var(--accent); }

    @media (max-width: 900px) {
      .charts-main { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Traffic & SEO Analytics</h1>
      <a href="index.php" class="btn-back">← Back to Site</a>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Total Visits</div>
        <div class="stat-value"><?php echo number_format($totalVisits); ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Unique Visitors</div>
        <div class="stat-value"><?php echo number_format($uniqueVisitors); ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Organic Bots (SEO)</div>
        <div class="stat-value"><?php echo number_format($seoData[1]); ?></div>
      </div>
    </div>

    <div class="charts-main">
      <div class="chart-container">
        <div class="chart-title">Traffic Trend (Last 14 Days)</div>
        <canvas id="trafficChart" height="150"></canvas>
      </div>
      <div class="chart-container">
        <div class="chart-title">SEO: Humans vs Bots</div>
        <canvas id="seoChart"></canvas>
      </div>
    </div>

    <div class="charts-row">
      <div class="chart-container">
        <div class="chart-title">Geo: Top Countries</div>
        <?php if (empty($geoLabels)): ?>
            <p style="color: var(--muted); text-align: center; margin-top: 40px;">Waiting for Geo data...</p>
        <?php else: ?>
            <canvas id="geoChart"></canvas>
        <?php endif; ?>
      </div>
      <div class="chart-container">
        <div class="chart-title">Devices: Distribution</div>
        <canvas id="deviceChart"></canvas>
      </div>
    </div>

    <div class="footer-actions">
        © 2026 Hasan Arofid — Advanced Analytics for SEO and Geo Visualization.
    </div>
  </div>

  <script>
    const chartDefaults = {
      responsive: true,
      plugins: {
        legend: { labels: { color: '#e5e7eb', font: { family: 'inherit' } } }
      }
    };

    // Traffic Trend Chart
    new Chart(document.getElementById('trafficChart'), {
      type: 'line',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          label: 'Total Visits',
          data: <?php echo json_encode($data); ?>,
          borderColor: '#38bdf8',
          backgroundColor: 'rgba(56, 189, 248, 0.1)',
          fill: true,
          tension: 0.4
        }, {
          label: 'Unique Visitors',
          data: <?php echo json_encode($uniqueData); ?>,
          borderColor: '#a855f7',
          backgroundColor: 'rgba(168, 85, 247, 0.1)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        ...chartDefaults,
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
          x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
        }
      }
    });

    // SEO Chart (Pie)
    new Chart(document.getElementById('seoChart'), {
      type: 'doughnut',
      data: {
        labels: ['Humans', 'SEO Bots'],
        datasets: [{
          data: <?php echo json_encode(array_values($seoData)); ?>,
          backgroundColor: ['#38bdf8', '#a855f7'],
          borderWidth: 0
        }]
      },
      options: chartDefaults
    });

    // Device Chart
    new Chart(document.getElementById('deviceChart'), {
      type: 'pie',
      data: {
        labels: <?php echo json_encode($deviceLabels); ?>,
        datasets: [{
          data: <?php echo json_encode($deviceValues); ?>,
          backgroundColor: ['#38bdf8', '#a855f7', '#10b981'],
          borderWidth: 0
        }]
      },
      options: chartDefaults
    });

    // Geo Chart (Bar)
    <?php if (!empty($geoLabels)): ?>
    new Chart(document.getElementById('geoChart'), {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($geoLabels); ?>,
        datasets: [{
          label: 'Visits',
          data: <?php echo json_encode($geoValues); ?>,
          backgroundColor: '#38bdf8',
          borderRadius: 6
        }]
      },
      options: {
        ...chartDefaults,
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
          x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
        }
      }
    });
    <?php endif; ?>
  </script>
</body>
</html>

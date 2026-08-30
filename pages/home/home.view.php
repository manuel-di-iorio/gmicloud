<?php if ($loginError): ?>
<script>uiToastError(<?= json_encode(__("error_login")) ?>, <?= json_encode($loginError) ?>);</script>
<?php endif; ?>
<?php if ($totalGames === 0) { ?>
  <?= ui_empty_state(__('home_empty_title'), [
    'icon' => 'fas fa-chart-pie',
    'description' => __('home_empty_desc'),
    'action' => ui_button(__('home_empty_cta'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-game.php']),
    'spacious' => true,
  ]) ?>
<?php } else { ?>

<div class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(150px,1fr))] gap-4 md:grid-cols-[repeat(auto-fit,minmax(200px,1fr))]">
  <?= ui_stat_card(number_format($totalScores), __('home_stat_scores'), ['icon' => 'fas fa-star']) ?>
  <?= ui_stat_card(number_format($totalPlayers), __('home_stat_players'), ['icon' => 'fas fa-users', 'variant' => 'success']) ?>
  <?= ui_stat_card($totalGames, __('home_stat_games'), ['icon' => 'fas fa-gamepad', 'variant' => 'info']) ?>
  <?= ui_stat_card(number_format($scoresToday), __('home_stat_today'), ['icon' => 'fas fa-calendar-day', 'variant' => 'warning']) ?>
</div>

<?php
$chartDays = [];
$chartCounts = [];
$scoreDataByDay = [];
foreach ($scoresOverTime as $row) {
  $scoreDataByDay[$row["day"]] = (int)$row["count"];
}
for ($i = 29; $i >= 0; $i--) {
  $date = date('Y-m-d', strtotime("-$i days"));
  $chartDays[] = date('d/m', strtotime("-$i days"));
  $chartCounts[] = $scoreDataByDay[$date] ?? 0;
}

$gameNames = [];
$gameCounts = [];
foreach ($scoresByGame as $row) {
  $gameNames[] = $row["name"];
  $gameCounts[] = (int)$row["count"];
}

$countryLabels = [];
$countryCounts = [];
foreach ($countries as $row) {
  $c = $row["ip_country"];
  if (!$c) continue;
  $countryLabels[] = $c;
  $countryCounts[] = (int)$row["count"];
}
?>

<div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
  <div class="bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden flex flex-col h-[360px]">
    <div class="p-5 flex-1 flex flex-col">
      <div class="font-semibold text-headings mb-3">
        <i class="fas fa-chart-line text-primary-color mr-2"></i><?= __('home_chart_30days') ?>
      </div>
      <div class="relative min-h-[200px] w-full flex-1">
        <canvas id="chartScoresOverTime"></canvas>
      </div>
    </div>
  </div>
  <div class="bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden flex flex-col h-[360px]">
    <div class="p-5 flex-1 flex flex-col">
      <div class="font-semibold text-headings mb-3">
        <i class="fas fa-chart-bar text-primary-color mr-2"></i><?= __('home_chart_by_game') ?>
      </div>
      <div class="relative min-h-[200px] w-full flex-1">
        <canvas id="chartScoresByGame"></canvas>
      </div>
    </div>
  </div>
</div>

<?php if (count($countryLabels) > 0) { ?>
<div class="mt-5">
  <div class="bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden flex flex-col">
    <div class="p-5 flex-1 flex flex-col">
      <div class="font-semibold text-headings mb-3">
        <i class="fas fa-globe text-primary-color mr-2"></i><?= __('home_chart_countries') ?>
      </div>
      <div class="relative min-h-[200px] max-h-[350px] w-full flex-1">
        <canvas id="chartCountries"></canvas>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var isDark = document.body.classList.contains('dark-theme') || <?= $theme === 'dark' ? 'true' : 'false' ?>;
  var textColor = isDark ? '#cbd5e1' : '#64748b';
  var gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

  function createLineCtx(id, labels, data, label) {
    var el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: label,
          data: data,
          borderColor: '#6366f1',
          backgroundColor: 'rgba(99,102,241,0.08)',
          borderWidth: 2,
          fill: true,
          tension: 0.3,
          pointRadius: 2,
          pointHoverRadius: 5,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: textColor, maxTicksLimit: 10 }, grid: { color: gridColor } },
          y: { ticks: { color: textColor }, grid: { color: gridColor }, beginAtZero: true }
        }
      }
    });
  }

  function createBarCtx(id, labels, data, label) {
    var el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: label,
          data: data,
          backgroundColor: [
            'rgba(99,102,241,0.7)', 'rgba(16,185,129,0.7)', 'rgba(245,158,11,0.7)',
            'rgba(236,72,153,0.7)', 'rgba(59,130,246,0.7)', 'rgba(168,85,247,0.7)',
            'rgba(239,68,68,0.7)', 'rgba(34,211,238,0.7)'
          ],
          borderColor: '#6366f1',
          borderWidth: 1,
          borderRadius: 4,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { ticks: { color: textColor }, grid: { display: false } },
          y: { ticks: { color: textColor }, grid: { color: gridColor }, beginAtZero: true }
        }
      }
    });
  }

  function createDoughnutCtx(id, labels, data) {
    var el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: [
            'rgba(99,102,241,0.8)', 'rgba(16,185,129,0.8)', 'rgba(245,158,11,0.8)',
            'rgba(236,72,153,0.8)', 'rgba(59,130,246,0.8)', 'rgba(168,85,247,0.8)',
            'rgba(239,68,68,0.8)', 'rgba(34,211,238,0.8)', 'rgba(251,191,36,0.8)',
            'rgba(52,211,153,0.8)'
          ],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: { color: textColor, boxWidth: 12, padding: 12 }
          }
        }
      }
    });
  }

  createLineCtx('chartScoresOverTime', <?= json_encode($chartDays) ?>, <?= json_encode($chartCounts) ?>, '<?= __('home_chart_scores_label') ?>');
  createBarCtx('chartScoresByGame', <?= json_encode($gameNames) ?>, <?= json_encode($gameCounts) ?>, '<?= __('home_chart_scores_label') ?>');
  createDoughnutCtx('chartCountries', <?= json_encode($countryLabels) ?>, <?= json_encode($countryCounts) ?>);
});
</script>

<?php } ?>

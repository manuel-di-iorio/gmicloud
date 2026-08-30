<?php
/**
 * Renders a single admin tab's content.
 * Expects $activeTab and all required data variables to be set.
 */

switch ($activeTab) {
  case 'users':
    $searchValue = htmlspecialchars($search ?? "");

    $html = '
<div class="mb-4 flex flex-wrap items-center gap-2">
  <form method="GET" action="/admin.php" class="flex flex-1 flex-wrap items-center gap-2">
    <input type="hidden" name="tab" value="users">
    ' . ($pendingOnly ? '<input type="hidden" name="pending" value="1">' : '') . '
    <input type="text" name="search" class="h-10 w-full max-w-[220px] rounded-lg border border-solid border-[var(--border-color)] bg-input-bg px-3.5 py-2 text-[0.95rem] leading-normal text-input-text transition-colors placeholder:text-[var(--text-color-secondary)] focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)]" placeholder="' . 'Search by username...' . '" value="' . $searchValue . '">
    ' . ui_button('Apply filters', 'primary', 'md', ['icon' => 'fas fa-search', 'type' => 'submit']) . '
    ' . (($search || $pendingOnly) ? ui_button('Reset', 'secondary', 'md', ['icon' => 'fas fa-times', 'href' => '/admin.php?tab=users']) : '') . '
    <a href="/admin.php?tab=users' . ($pendingOnly ? '' : '&pending=1') . ($search ? '&search=' . urlencode($search) : '') . '" class="inline-flex h-[38px] min-w-20 items-center gap-1.5 whitespace-nowrap rounded-lg border border-solid px-3.5 text-[0.85rem] font-semibold no-underline transition-colors ' . ($pendingOnly ? 'border-amber-500/30 bg-amber-500/15 text-amber-500' : 'border-border-color bg-surface-offset text-text-secondary hover:bg-surface-card hover:text-text') . '">
      <i class="fas fa-clock"></i> ' . 'Pending only' . '
      ' . ($unapprovedCount > 0 ? ui_badge((string)$unapprovedCount, 'warning', ['pill' => true]) : '') . '
    </a>
  </form>
  <div class="whitespace-nowrap text-[0.85rem] text-text-secondary">
    ' . 'Total users' . ': ' . $totalUsers . '
    ' . ($unapprovedCount > 0 && !$pendingOnly ? '<span class="ml-2 text-red-500">(' . $unapprovedCount . ' ' . 'pending approval' . ')</span>' : '') . '
  </div>
</div>';

    if (empty($users)) {
      $html .= ui_empty_state('No data available.', ['icon' => 'fas fa-users']);
    } else {
      $html .= '<div class="ui-table-container"><table class="ui-table"><thead class="ui-table-header"><tr>
        <th class="ui-table-header-cell">ID</th>
        <th class="ui-table-header-cell">' . 'Username' . '</th>
        <th class="ui-table-header-cell">' . 'Discord ID' . '</th>
        <th class="ui-table-header-cell">' . 'Approved' . '</th>
        <th class="ui-table-header-cell">' . 'Admin' . '</th>
        <th class="ui-table-header-cell">' . 'Actions' . '</th>
      </tr></thead><tbody class="ui-table-body">';

      foreach ($users as $u) {
        $isUserApproved = (int)$u["approved"] === 1;
        $isUserAdmin = (int)($u["admin"] ?? 0) === 1;

        $togglePostBody = http_build_query(array_merge(
          ['id' => (int)$u["id"], 'csrf_token' => csrf_token()],
          $search ? ['search' => $search] : [],
          $pendingOnly ? ['pending' => '1'] : [],
          $page > 0 ? ['page' => $page] : []
        ));

        $html .= '<tr class="ui-table-row">
          <td class="ui-table-cell">' . (int)$u["id"] . '</td>
          <td class="ui-table-cell">' . htmlspecialchars($u["username"]) . '</td>
          <td class="ui-table-cell"><code class="text-[0.85rem]">' . htmlspecialchars($u["auth_discord_id"]) . '</code></td>
          <td class="ui-table-cell">' . ($isUserApproved
            ? ui_badge('Yes', 'success', ['icon' => 'fas fa-check-circle'])
            : ui_badge('No', 'danger', ['icon' => 'fas fa-times-circle'])) . '</td>
          <td class="ui-table-cell">' . ($isUserAdmin
            ? '<span class="text-indigo-500"><i class="fas fa-crown"></i></span>'
            : '') . '</td>
          <td class="ui-table-cell actions-cell">
            ' . ui_toggle($isUserApproved, '/admin-users-toggle.php', ['labelOn' => 'Disable user', 'labelOff' => 'Enable user', 'size' => 'md', 'method' => 'POST', 'postBody' => $togglePostBody]) . '
          </td>
        </tr>';
      }

      $html .= '</tbody></table></div>';

      $totalPages = ceil($totalUsers / $perPage) - 1;
      if ($totalPages > 0) {
        $urlParams = $_GET;
        unset($urlParams['page'], $urlParams['ajax']);
        $baseQuery = http_build_query($urlParams);
        $urlPattern = $baseQuery ? '/admin.php?' . $baseQuery . '&page={page}' : '/admin.php?page={page}';
        $html .= '<div class="mt-4 text-center">' .
          ui_paginator($page, $totalPages, [
            'url' => $urlPattern,
            'prevLabel' => 'Previous',
            'nextLabel' => 'Next',
          ]) . '</div>';
      }
    }

    echo $html;
    break;

  case 'players':
    $playersSearchValue = htmlspecialchars($playersSearch ?? "");
    $pCurrentSort = $playersSortBy ?? '';
    $pCurrentDir = strtoupper($playersSortDir) === 'ASC' ? 'ASC' : 'DESC';
    $pBannedOnly = $playersBannedOnly ?? false;

    $pSortUrlBase = '/admin.php?tab=players';
    if ($playersSearch) $pSortUrlBase .= '&players_search=' . urlencode($playersSearch);
    if ($pBannedOnly) $pSortUrlBase .= '&players_banned=1';

    function playerSortLink($label, $key, $pCurrentSort, $pCurrentDir, $pSortUrlBase) {
      $isActive = $pCurrentSort === $key;
      $nextDir = ($isActive && $pCurrentDir === 'DESC') ? 'ASC' : 'DESC';
      $icon = '';
      if ($isActive) {
        $icon = $pCurrentDir === 'ASC' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>';
      } else {
        $icon = ' <i class="fas fa-sort opacity-30"></i>';
      }
      $url = $pSortUrlBase . '&players_sort=' . $key . '&players_dir=' . $nextDir;
      return '<a href="' . htmlspecialchars($url) . '" class="inline-flex items-center gap-1 text-inherit no-underline">' . $label . $icon . '</a>';
    }

    $bannedFilterActive = $pBannedOnly;
    $bannedFilterUrl = '/admin.php?tab=players';
    if ($playersSearch) $bannedFilterUrl .= '&players_search=' . urlencode($playersSearch);
    if (!$bannedFilterActive) $bannedFilterUrl .= '&players_banned=1';

    $html = '
<div class="mb-4 flex flex-wrap items-center gap-2">
  <form method="GET" action="/admin.php" class="flex flex-1 flex-wrap items-center gap-2">
    <input type="hidden" name="tab" value="players">
    <input type="text" name="players_search" class="h-10 w-full max-w-[220px] rounded-lg border border-solid border-[var(--border-color)] bg-input-bg px-3.5 py-2 text-[0.95rem] leading-normal text-input-text transition-colors placeholder:text-[var(--text-color-secondary)] focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)]" placeholder="' . 'Search by username...' . '" value="' . $playersSearchValue . '">
    ' . ui_button('Apply filters', 'primary', 'md', ['icon' => 'fas fa-search', 'type' => 'submit']) . '
    ' . ($playersSearch ? ui_button('Reset', 'secondary', 'md', ['icon' => 'fas fa-times', 'href' => '/admin.php?tab=players']) : '') . '
    <a href="' . htmlspecialchars($bannedFilterUrl) . '" class="inline-flex h-[38px] min-w-20 items-center gap-1.5 whitespace-nowrap rounded-lg border border-solid px-3.5 text-[0.85rem] font-semibold no-underline transition-colors ' . ($bannedFilterActive ? 'border-amber-500/30 bg-amber-500/15 text-amber-500' : 'border-border-color bg-surface-offset text-text-secondary hover:bg-surface-card hover:text-text') . '">
      <i class="fas fa-ban"></i> ' . 'Banned' . '
    </a>
  </form>
  <div class="whitespace-nowrap text-[0.85rem] text-text-secondary">
    ' . 'Total players' . ': ' . $totalPlayers . '
  </div>
</div>';

    if (empty($players)) {
      $html .= ui_empty_state('No data available.', ['icon' => 'fas fa-user-friends']);
    } else {
      $html .= '<div class="ui-table-container"><table class="ui-table"><thead class="ui-table-header"><tr>
        <th class="ui-table-header-cell">' . playerSortLink('ID', 'id', $pCurrentSort, $pCurrentDir, $pSortUrlBase) . '</th>
        <th class="ui-table-header-cell">' . playerSortLink('Username', 'username', $pCurrentSort, $pCurrentDir, $pSortUrlBase) . '</th>
        <th class="ui-table-header-cell">' . playerSortLink('Top score', 'top_score', $pCurrentSort, $pCurrentDir, $pSortUrlBase) . '</th>
        <th class="ui-table-header-cell">' . playerSortLink('Game', 'game', $pCurrentSort, $pCurrentDir, $pSortUrlBase) . '</th>
        <th class="ui-table-header-cell">' . 'Banned' . '</th>
        <th class="ui-table-header-cell">' . 'Actions' . '</th>
      </tr></thead><tbody class="ui-table-body">';

      foreach ($players as $p) {
        $isBanned = (int)($p["has_bans"] ?? 0) === 1;
        $togglePostBody = http_build_query(array_merge(
          ['id' => (int)$p["player_id"], 'csrf_token' => csrf_token()],
          $playersSearch ? ['players_search' => $playersSearch] : [],
          $playersPage > 0 ? ['players_page' => $playersPage] : [],
          $pCurrentSort ? ['players_sort' => $pCurrentSort] : [],
          $pCurrentDir !== 'DESC' ? ['players_dir' => $pCurrentDir] : [],
          $pBannedOnly ? ['players_banned' => '1'] : []
        ));

        $html .= '<tr class="ui-table-row">
          <td class="ui-table-cell">' . (int)$p["player_id"] . '</td>
          <td class="ui-table-cell">' . htmlspecialchars($p["username"]) . '</td>
          <td class="ui-table-cell">' . (isset($p["top_score"]) ? number_format((float)$p["top_score"], 2) : '<span class="text-text-secondary">-</span>') . '</td>
          <td class="ui-table-cell">' . ($p["top_game"] ? htmlspecialchars($p["top_game"]) : '<span class="text-text-secondary">-</span>') . '</td>
          <td class="ui-table-cell">' . ($isBanned
            ? ui_badge('Yes', 'danger', ['icon' => 'fas fa-ban'])
            : ui_badge('No', 'default', ['icon' => 'fas fa-check'])) . '</td>
          <td class="ui-table-cell actions-cell">
            ' . ui_toggle($isBanned, '/admin-players-toggle.php', ['labelOn' => 'Unban', 'labelOff' => 'Ban', 'size' => 'md', 'method' => 'POST', 'postBody' => $togglePostBody]) . '
          </td>
        </tr>';
      }

      $html .= '</tbody></table></div>';

      $playersTotalPages = ceil($totalPlayers / $playersPerPage) - 1;
      if ($playersTotalPages > 0) {
        $urlParams = $_GET;
        unset($urlParams['players_page'], $urlParams['ajax']);
        $baseQuery = http_build_query($urlParams);
        $urlPattern = $baseQuery ? '/admin.php?' . $baseQuery . '&players_page={page}' : '/admin.php?players_page={page}';
        $html .= '<div class="mt-4 text-center">' .
          ui_paginator($playersPage, $playersTotalPages, [
            'url' => $urlPattern,
            'prevLabel' => 'Previous',
            'nextLabel' => 'Next',
          ]) . '</div>';
      }
    }

    echo $html;
    break;

  case 'scores':
    $scoresSearchValue = htmlspecialchars($scoresSearch ?? "");
    $currentSort = $scoresSortBy ?? 'date';
    $currentDir = strtoupper($scoresSortDir) === 'ASC' ? 'ASC' : 'DESC';

    $sortUrlBase = '/admin.php?tab=scores';
    if ($scoresSearch) $sortUrlBase .= '&scores_search=' . urlencode($scoresSearch);

    function scoreSortLink($label, $key, $currentSort, $currentDir, $sortUrlBase) {
      $isActive = $currentSort === $key;
      $nextDir = ($isActive && $currentDir === 'DESC') ? 'ASC' : 'DESC';
      $icon = '';
      if ($isActive) {
        $icon = $currentDir === 'ASC' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>';
      } else {
        $icon = ' <i class="fas fa-sort opacity-30"></i>';
      }
      $url = $sortUrlBase . '&scores_sort=' . $key . '&scores_dir=' . $nextDir;
      return '<a href="' . htmlspecialchars($url) . '" class="inline-flex items-center gap-1 text-inherit no-underline">' . $label . $icon . '</a>';
    }

    $html = '
<div class="mb-4 flex flex-wrap items-center gap-2">
  <form method="GET" action="/admin.php" class="flex flex-1 flex-wrap items-center gap-2">
    <input type="hidden" name="tab" value="scores">
    <input type="text" name="scores_search" class="h-10 w-full max-w-[220px] rounded-lg border border-solid border-[var(--border-color)] bg-input-bg px-3.5 py-2 text-[0.95rem] leading-normal text-input-text transition-colors placeholder:text-[var(--text-color-secondary)] focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)]" placeholder="' . 'Search by username...' . '" value="' . $scoresSearchValue . '">
    ' . ui_button('Apply filters', 'primary', 'md', ['icon' => 'fas fa-search', 'type' => 'submit']) . '
    ' . ($scoresSearch ? ui_button('Reset', 'secondary', 'md', ['icon' => 'fas fa-times', 'href' => '/admin.php?tab=scores']) : '') . '
  </form>
  <div class="whitespace-nowrap text-[0.85rem] text-text-secondary">
    ' . 'Total scores' . ': ' . number_format($totalScores) . '
  </div>
</div>';

    if (empty($scores)) {
      $html .= ui_empty_state('No data available.', ['icon' => 'fas fa-star']);
    } else {
      $html .= '<div class="ui-table-container"><table class="ui-table"><thead class="ui-table-header"><tr>
        <th class="ui-table-header-cell">' . scoreSortLink('Username', 'username', $currentSort, $currentDir, $sortUrlBase) . '</th>
        <th class="ui-table-header-cell">' . scoreSortLink('Score', 'score', $currentSort, $currentDir, $sortUrlBase) . '</th>
        <th class="ui-table-header-cell">' . scoreSortLink('Game', 'game', $currentSort, $currentDir, $sortUrlBase) . '</th>
        <th class="ui-table-header-cell">' . scoreSortLink('Date', 'date', $currentSort, $currentDir, $sortUrlBase) . '</th>
        <th class="ui-table-header-cell">' . 'Actions' . '</th>
      </tr></thead><tbody class="ui-table-body">';

      foreach ($scores as $score) {
        $scoreId = (int)$score["score_id"];
        $playerName = htmlspecialchars($score["username"]);
        $gameName = htmlspecialchars($score["game_name"]);
        $scoreValue = number_format((float)$score["score"], 2);
        $dateValue = htmlspecialchars($score["updated_at"]);
        $pageParam = max(0, (int)($scoresPage ?? 0));
        $deletePostBody = http_build_query(array_merge(
          ['id' => $scoreId, 'scores_page' => $pageParam, 'csrf_token' => csrf_token()],
          $scoresSearch ? ['scores_search' => $scoresSearch] : [],
          $currentSort !== 'date' ? ['scores_sort' => $currentSort] : [],
          $currentDir !== 'DESC' ? ['scores_dir' => $currentDir] : []
        ));
        $banPostBody = http_build_query(array_merge(
          ['id' => $scoreId, 'scores_page' => $pageParam, 'csrf_token' => csrf_token()],
          $scoresSearch ? ['scores_search' => $scoresSearch] : [],
          $currentSort !== 'date' ? ['scores_sort' => $currentSort] : [],
          $currentDir !== 'DESC' ? ['scores_dir' => $currentDir] : []
        ));

        $html .= '<tr class="ui-table-row">
          <td class="ui-table-cell">' . $playerName . '</td>
          <td class="ui-table-cell">' . $scoreValue . '</td>
          <td class="ui-table-cell">' . $gameName . '</td>
          <td class="ui-table-cell">' . $dateValue . '</td>
          <td class="ui-table-cell actions-cell">
            <a href="javascript:void(0)" class="admin-score-action admin-score-action--danger" data-admin-score-delete data-post-url="/admin-scores-delete.php" data-post-body="' . htmlspecialchars($deletePostBody) . '" data-player="' . $playerName . '" data-tippy-content="' . __('scores_action_delete') . '" aria-label="' . __('scores_action_delete') . '">
              <i class="fas fa-trash"></i>
            </a>
            <a href="javascript:void(0)" class="admin-score-action admin-score-action--danger" data-admin-score-ban data-post-url="/admin-scores-ban-player.php" data-post-body="' . htmlspecialchars($banPostBody) . '" data-player="' . $playerName . '" data-game="' . $gameName . '" data-tippy-content="' . __('scores_action_ban') . '" aria-label="' . __('scores_action_ban') . '">
              <i class="fas fa-user-times"></i>
            </a>
          </td>
        </tr>';
      }

      $html .= '</tbody></table></div>';

      $scoresTotalPages = ceil($totalScores / $scoresPerPage) - 1;
      if ($scoresTotalPages > 0) {
        $scoresUrlParams = ['tab' => 'scores'];
        if ($scoresSearch) $scoresUrlParams['scores_search'] = $scoresSearch;
        if ($currentSort !== 'date') $scoresUrlParams['scores_sort'] = $currentSort;
        if ($currentDir !== 'DESC') $scoresUrlParams['scores_dir'] = $currentDir;
        $scoresBaseQuery = http_build_query($scoresUrlParams);
        $scoresUrlPattern = '/admin.php?' . $scoresBaseQuery . '&scores_page={page}';
        $html .= '<div class="mt-4 text-center">' .
          ui_paginator($scoresPage, $scoresTotalPages, [
            'url' => $scoresUrlPattern,
            'prevLabel' => 'Previous',
            'nextLabel' => 'Next',
          ]) . '</div>';
      }
    }

    echo $html;
    break;

  case 'analytics':
    $chartDays = [];
    $chartCounts = [];
    $scoreDataByDay = [];
    foreach ($globalScoresOverTime as $row) {
      $scoreDataByDay[$row["day"]] = (int)$row["count"];
    }
    for ($i = 29; $i >= 0; $i--) {
      $date = date('Y-m-d', strtotime("-$i days"));
      $chartDays[] = date('d/m', strtotime("-$i days"));
      $chartCounts[] = $scoreDataByDay[$date] ?? 0;
    }

    $gameNames = [];
    $gameCounts = [];
    foreach ($globalScoresByGame as $row) {
      $gameNames[] = $row["name"];
      $gameCounts[] = (int)$row["count"];
    }

    $countryLabels = [];
    $countryCounts = [];
    $countryLabelsAll = [];
    $countryCountsAll = [];
    foreach ($globalCountriesList as $i => $row) {
      if (!$row["ip_country"]) continue;
      $countryLabelsAll[] = $row["ip_country"];
      $countryCountsAll[] = (int)$row["count"];
      if ($i < 30) {
        $countryLabels[] = $row["ip_country"];
        $countryCounts[] = (int)$row["count"];
      }
    }
    $countryCountVal = count($countryLabelsAll);

    $html = '<div class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(140px,1fr))] gap-4 md:grid-cols-[repeat(auto-fit,minmax(180px,1fr))]">' .
      ui_stat_card(number_format($globalTotalScores), 'Scores', ['icon' => 'fas fa-star']) .
      ui_stat_card($globalTotalGames, 'Games', ['icon' => 'fas fa-gamepad', 'variant' => 'success']) .
      ui_stat_card(number_format($globalTotalPlayers), 'Players', ['icon' => 'fas fa-users', 'variant' => 'info']) .
      ui_stat_card(number_format($totalUsers), 'Users', ['icon' => 'fas fa-user-friends', 'variant' => 'purple']) .
      ui_stat_card($globalActiveGames, 'Active games', ['icon' => 'fas fa-play-circle', 'variant' => 'warning']) .
      ui_stat_card($countryCountVal, 'Countries', ['icon' => 'fas fa-globe', 'variant' => 'pink']) .
    '</div>';

    if ($globalTotalScores > 0) {
      $html .= '<div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">' .
        ui_stat_card($globalTopGame["name"] ?? "N/A", 'Top game (' . ($globalTopGame["count"] ?? 0) . ' Scores)', ['icon' => 'fas fa-trophy']) .
        ui_stat_card($globalTopPlayer["username"] ?? "N/A", 'Top player (' . ($globalTopPlayer["count"] ?? 0) . ' Scores)', ['icon' => 'fas fa-crown', 'variant' => 'success']) .
      '</div>';

      $html .= '
<div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
  <div class="bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden flex flex-col h-[360px]">
    <div class="p-5 flex-1 flex flex-col">
      <div class="font-semibold text-headings mb-3">
        <i class="fas fa-chart-line text-primary-color mr-2"></i>' . 'Global scores last 30 days' . '
      </div>
      <div class="relative min-h-[200px] w-full flex-1">
        <canvas id="chartAdminScoresOverTime"></canvas>
      </div>
    </div>
  </div>
  <div class="bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden flex flex-col h-[360px]">
    <div class="p-5 flex-1 flex flex-col">
      <div class="font-semibold text-headings mb-3">
        <i class="fas fa-chart-bar text-primary-color mr-2"></i>' . 'Total scores per game' . '
      </div>
      <div class="relative min-h-[200px] w-full flex-1">
        <canvas id="chartAdminScoresByGame"></canvas>
      </div>
    </div>
  </div>
</div>';

      if (count($countryLabelsAll) > 0) {
        $moreCountries = count($countryLabelsAll) - 30;
        $html .= '
<div class="mt-5">
  <div class="bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden flex flex-col">
    <div class="p-5 flex-1 flex flex-col">
      <div class="font-semibold text-headings mb-3">
        <i class="fas fa-globe text-primary-color mr-2"></i>' . 'Countries' . '
        ' . ($moreCountries > 0 ? '<span class="ml-2 text-[0.8rem] font-normal text-text-secondary">(' . 'Top 30 - ' . $moreCountries . ' more' . ')</span>' : '') . '
      </div>
      <div class="relative max-h-[350px] w-full">
        <canvas id="chartAdminCountries"></canvas>
      </div>
    </div>
  </div>
</div>';
      }
    } else {
      $html .= ui_empty_state('No scores have been submitted yet.', ['icon' => 'fas fa-chart-bar']);
    }

    echo $html;

    // Output inline chart init script
    if ($globalTotalScores > 0) {
      echo '<script>
(function () {
  var isDark = document.body.classList.contains("dark-theme");
  var textColor = isDark ? "#cbd5e1" : "#64748b";
  var gridColor = isDark ? "rgba(255,255,255,0.06)" : "rgba(0,0,0,0.06)";

  function createLineCtx(id, labels, data, label) {
    var el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
      type: "line",
      data: {
        labels: labels,
        datasets: [{
          label: label,
          data: data,
          borderColor: "#6366f1",
          backgroundColor: "rgba(99,102,241,0.08)",
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
      type: "bar",
      data: {
        labels: labels,
        datasets: [{
          label: label,
          data: data,
          backgroundColor: [
            "rgba(99,102,241,0.7)", "rgba(16,185,129,0.7)", "rgba(245,158,11,0.7)",
            "rgba(236,72,153,0.7)", "rgba(59,130,246,0.7)", "rgba(168,85,247,0.7)",
            "rgba(239,68,68,0.7)", "rgba(34,211,238,0.7)"
          ],
          borderColor: "#6366f1",
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
      type: "doughnut",
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: [
            "rgba(99,102,241,0.8)", "rgba(16,185,129,0.8)", "rgba(245,158,11,0.8)",
            "rgba(236,72,153,0.8)", "rgba(59,130,246,0.8)", "rgba(168,85,247,0.8)",
            "rgba(239,68,68,0.8)", "rgba(34,211,238,0.8)", "rgba(251,191,36,0.8)",
            "rgba(52,211,153,0.8)"
          ],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "right",
            labels: { color: textColor, boxWidth: 12, padding: 12 }
          }
        }
      }
    });
  }

  createLineCtx("chartAdminScoresOverTime", ' . json_encode($chartDays) . ', ' . json_encode($chartCounts) . ', "' . 'Scores' . '");
  createBarCtx("chartAdminScoresByGame", ' . json_encode($gameNames) . ', ' . json_encode($gameCounts) . ', "' . 'Scores' . '");
  createDoughnutCtx("chartAdminCountries", ' . json_encode($countryLabels) . ', ' . json_encode($countryCounts) . ');
})();
</script>';
    }
    break;

  case 'api-errors':
    $csrfToken = csrf_token();
    $html = '<div id="api-errors-container">
      <div class="px-5 py-10 text-center text-text-secondary">
        <i class="fas fa-spinner fa-spin text-2xl opacity-50"></i>
      </div>
    </div>';
    echo $html;
    echo '<script>
(function() {
  var container = document.getElementById("api-errors-container");
  if (!container) return;

  var currentPage = 0;
  var csrfToken = "' . addslashes($csrfToken) . '";

  function loadPage(page) {
    container.innerHTML = "<div class=\"px-5 py-10 text-center text-text-secondary\"><i class=\"fas fa-spinner fa-spin text-2xl opacity-50\"></i></div>";

    fetch("/api/internal/admin/api-errors.php?page=" + page + "&csrf_token=" + encodeURIComponent(csrfToken), {
      credentials: "same-origin"
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.errors || data.errors.length === 0) {
        container.innerHTML = "<div class=\"px-5 py-10 text-center text-text-secondary\"><i class=\"fas fa-check-circle mb-3 block text-4xl opacity-30\"></i>No errors logged.</div>";
        return;
      }

      var html = "<div class=\"mb-3 text-[0.85rem] text-text-secondary\">Total: " + data.total + " errors</div>";
      html += "<div class=\"ui-table-container\"><table class=\"ui-table\"><thead class=\"ui-table-header\"><tr>";
      html += "<th class=\"ui-table-header-cell\">Date</th>";
      html += "<th class=\"ui-table-header-cell\">Code</th>";
      html += "<th class=\"ui-table-header-cell\">Status</th>";
      html += "<th class=\"ui-table-header-cell\">Game</th>";
      html += "<th class=\"ui-table-header-cell\">Endpoint</th>";
      html += "<th class=\"ui-table-header-cell\">Method</th>";
      html += "<th class=\"ui-table-header-cell\">IP</th>";
      html += "<th class=\"ui-table-header-cell\">Message</th>";
      html += "<th class=\"ui-table-header-cell\">Request Data</th>";
      html += "</tr></thead><tbody class=\"ui-table-body\">";

      data.errors.forEach(function(e) {
        var statusClass = e.status >= 500 ? "danger" : (e.status >= 400 ? "warning" : "default");
        html += "<tr class=\"ui-table-row\">";
        html += "<td class=\"ui-table-cell whitespace-nowrap text-[0.85rem]\">" + escapeHtml(e.created_at) + "</td>";
        html += "<td class=\"ui-table-cell\"><code class=\"text-[0.85rem]\">" + escapeHtml(e.error_code) + "</code></td>";
        html += "<td class=\"ui-table-cell\"><span class=\"ui-badge ui-badge--" + statusClass + "\">" + e.status + "</span></td>";
        html += "<td class=\"ui-table-cell\"><code class=\"text-[0.85rem]\">" + (e.game_id ? escapeHtml(e.game_id) : "-") + "</code></td>";
        html += "<td class=\"ui-table-cell\"><code class=\"text-[0.85rem]\">" + escapeHtml(e.endpoint) + "</code></td>";
        html += "<td class=\"ui-table-cell\">" + escapeHtml(e.method) + "</td>";
        html += "<td class=\"ui-table-cell\"><code class=\"text-[0.85rem]\">" + escapeHtml(e.ip || "-") + "</code></td>";
        html += "<td class=\"ui-table-cell max-w-[300px] truncate\" title=\"" + escapeHtml(e.message) + "\">" + escapeHtml(e.message) + "</td>";
        html += "<td class=\"ui-table-cell max-w-[250px] truncate text-[0.8rem] text-text-secondary\" title=\"" + escapeHtml(e.request_data || "") + "\">" + escapeHtml(e.request_data || "-") + "</td>";
        html += "</tr>";
      });

      html += "</tbody></table></div>";

      var totalPages = Math.ceil(data.total / data.perPage) - 1;
      if (totalPages > 0) {
        html += "<div class=\"mt-4 flex items-center justify-center gap-2 text-center\">";
        if (data.page > 0) {
          html += "<button class=\"inline-flex cursor-pointer items-center justify-center gap-1.5 rounded-lg border-0 bg-surface-offset px-3 py-1.5 text-sm font-semibold text-text transition-colors hover:bg-surface-offset-hover\" onclick=\"window._apiErrorsLoadPage(" + (data.page - 1) + ")\"><i class=\"fas fa-chevron-left\"></i> Previous</button>";
        }
        html += "<span class=\"text-[0.85rem] text-text-secondary\">Page " + (data.page + 1) + " of " + (totalPages + 1) + "</span>";
        if (data.page < totalPages) {
          html += "<button class=\"inline-flex cursor-pointer items-center justify-center gap-1.5 rounded-lg border-0 bg-surface-offset px-3 py-1.5 text-sm font-semibold text-text transition-colors hover:bg-surface-offset-hover\" onclick=\"window._apiErrorsLoadPage(" + (data.page + 1) + ")\">Next <i class=\"fas fa-chevron-right\"></i></button>";
        }
        html += "</div>";
      }

      container.innerHTML = html;
    })
    .catch(function(err) {
      container.innerHTML = "<div class=\"px-5 py-10 text-center text-red-600\"><i class=\"fas fa-exclamation-triangle mb-3 block text-4xl opacity-30\"></i>Error loading data: " + escapeHtml(err.message) + "</div>";
    });
  }

  function escapeHtml(str) {
    if (!str) return "";
    var div = document.createElement("div");
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  window._apiErrorsLoadPage = function(page) {
    currentPage = page;
    loadPage(page);
  };

  loadPage(0);
})();
</script>';
    break;

  case 'migrate':
    $html = '
<div class="mb-4 flex items-center justify-between gap-4">
  <p class="m-0 text-text-secondary">' . __('migrate_desc') . '</p>
  ' . ui_button(__('admin_sync_indexes'), 'primary', 'md', ['icon' => 'fas fa-sync', 'attrs' => ['onclick' => "openModal('modal-sync-indexes')"]]) . '
</div>';

    if (!empty($migrateOutput)) {
      $html .= '<div class="my-4 max-h-[400px] overflow-y-auto whitespace-pre-wrap break-all rounded-lg bg-[#1e1e2e] p-4 font-mono text-[0.85rem] text-[#cdd6f4]">';
      foreach ($migrateOutput as $line) {
        $cls = 'text-[#a6e3a1]';
        if (strpos($line, 'ERROR') === 0) $cls = 'text-[#f38ba8]';
        elseif (strpos($line, 'FAIL') === 0) $cls = 'text-[#fab387]';
        $html .= '<div class="' . $cls . '">' . htmlspecialchars($line) . '</div>';
      }
      $html .= '</div>';
    }

    if (empty($migrations)) {
      $html .= ui_empty_state('No migration files found.', ['icon' => 'fas fa-database']);
    } else {
      $html .= '
<div class="ui-table-container"><table class="ui-table"><thead class="ui-table-header"><tr>
  <th class="ui-table-header-cell">' . 'File' . '</th>
  <th class="ui-table-header-cell">' . 'Description' . '</th>
  <th class="ui-table-header-cell">' . 'Status' . '</th>
  <th class="ui-table-header-cell">' . 'Date' . '</th>
</tr></thead><tbody class="ui-table-body">';

      foreach ($migrations as $m) {
        $statusLabel = $m['is_applied'] ? 'Applied' : 'Pending';
        $statusBadge = $m['is_applied']
          ? ui_badge($statusLabel, 'success', ['icon' => 'fas fa-check'])
          : ui_badge($statusLabel, 'warning', ['icon' => 'fas fa-clock']);
        $appliedDate = $m['is_applied'] ? htmlspecialchars($applied[$m['name']]) : '-';

        $html .= '<tr class="ui-table-row">
          <td class="ui-table-cell"><code>' . htmlspecialchars($m['name']) . '</code></td>
          <td class="ui-table-cell">' . htmlspecialchars($m['description']) . '</td>
          <td class="ui-table-cell">' . $statusBadge . '</td>
          <td class="ui-table-cell">' . $appliedDate . '</td>
        </tr>';
      }

      $html .= '</tbody></table></div>';

      if ($pendingMigrateCount > 0) {
        $html .= '
        <form method="POST" action="/admin.php?tab=migrate" class="mt-4">
          ' . csrf_field() . '
          <input type="hidden" name="run" value="1">
          ' . ui_button('Run pending migrations (' . $pendingMigrateCount . ')', 'primary', 'md', ['icon' => 'fas fa-play', 'type' => 'submit']) . '
        </form>';
      } else {
        $html .= '<div class="mt-4 text-text-secondary"><i class="fas fa-check-circle mr-2 text-emerald-500"></i>' . 'All migrations have been applied.' . '</div>';
      }
    }

    $html .= '<div id="sync-indexes-output" class="my-4 hidden max-h-[400px] overflow-y-auto whitespace-pre-wrap break-all rounded-lg bg-[#1e1e2e] p-4 font-mono text-[0.85rem] text-[#cdd6f4]"></div>';

    $html .= '</div>';
    echo $html;
    break;
}
?>
<script>
document.addEventListener('click', function(e) {
  var toggle = e.target.closest('[data-ui-toggle-post]');
  if (toggle && !toggle.classList.contains('ui-toggle')) {
    e.preventDefault();
    var url = toggle.getAttribute('data-post-url');
    var body = toggle.getAttribute('data-post-body');
    if (url && body) {
      fetch(url, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: body })
        .then(function() { location.reload(); });
    }
    return;
  }
  var action = e.target.closest('[data-post-url]');
  if (action && !action.classList.contains('ui-toggle') && !action.hasAttribute('data-admin-score-delete') && !action.hasAttribute('data-admin-score-ban')) {
    e.preventDefault();
    var url = action.getAttribute('data-post-url');
    var body = action.getAttribute('data-post-body');
    if (url && body) {
      fetch(url, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: body })
        .then(function() { location.reload(); });
    }
  }
});
</script>

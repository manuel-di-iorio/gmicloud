<?php

$navbarItems = [
  ["label" => __("nav_dashboard"), "url" => isset($user) ? "/home.php" : "/", "icon" => "home", "showOnlyLogged" => false],
  ["label" => __("nav_your_games"), "url" => "/games.php", "icon" => "gamepad", "showOnlyLogged" => true],
  ["label" => __("nav_your_teams"), "url" => "/teams.php", "icon" => "users", "showOnlyLogged" => true],
  ["label" => __("nav_documentation"), "url" => "/documentation.php", "icon" => "book", "showOnlyLogged" => false]
];

$isAdminUser = isset($user) && isset($user["admin"]) && (int)$user["admin"] === 1;
if ($isAdminUser) {
  $adminLabel = __("nav_admin");
  $pendingCount = User::countUnapproved();
  if ($pendingCount > 0) {
    $adminLabel .= ' <span class="ds-sidebar__badge">' . $pendingCount . '</span>';
  }
  $navbarItems[] = ["label" => $adminLabel, "url" => "/admin.php", "icon" => "cogs", "showOnlyLogged" => true, "allowHtml" => true];
}

$userTeamsList = [];
if (isset($user)) {
  require_once __DIR__ . '/../models/Team.php';
  $utResult = Team::listByUser($user["id"]);
  while ($utRow = $utResult->fetch_assoc()) {
    $userTeamsList[] = $utRow;
  }
}

$selectedTeamId = isset($_COOKIE['selected_team_id']) && $_COOKIE['selected_team_id'] !== '' ? (int)$_COOKIE['selected_team_id'] : null;
if ($selectedTeamId !== null && isset($user)) {
  $found = false;
  foreach ($userTeamsList as $ut) {
    if ((int)$ut['team_id'] === (int)$selectedTeamId) { $found = true; break; }
  }
  if (!$found) $selectedTeamId = null;
}
?>

<div class="ds-overlay" id="overlay" onclick="w3_close()"></div>

<nav id="navbar" class="ds-sidebar">
  <div class="ds-sidebar__brand">
    <a href="./index.php" class="navbar-logo-link">
      <img src="/assets/images/logo.png" class="Logo" alt="Logo Piattaforma">
    </a>
  </div>

  <div class="ds-sidebar__menu">
    <?php if (isset($user) && count($userTeamsList) > 0) { ?>
      <div class="ds-team-select">
        <label class="ds-label" style="display:block;margin-bottom:8px"><?= __('team_selector_label') ?></label>
        <select onchange="document.cookie='selected_team_id='+(this.value==='0'?'':this.value)+';path=/;max-age=31536000;SameSite=Lax';location.reload();" class="ds-select" style="width:100%">
          <option value="0" <?= $selectedTeamId === null ? 'selected' : '' ?>><?= __('team_selector_personal') ?></option>
          <?php foreach ($userTeamsList as $ut) { ?>
            <option value="<?= $ut['team_id'] ?>" <?= (int)$selectedTeamId === (int)$ut['team_id'] ? 'selected' : '' ?>><?= htmlspecialchars($ut['name']) ?></option>
          <?php } ?>
        </select>
      </div>
    <?php } ?>

    <?php foreach ($navbarItems as $navbarItem) {
      if (!$navbarItem["showOnlyLogged"] || isset($user)) { ?>
        <a href="<?= $navbarItem["url"] ?>" class="ds-sidebar__item <?php if ($navbarItem["url"] === $pageURI) { echo "ds-sidebar__item--active"; } ?>">
          <i class="fas fa-<?= $navbarItem["icon"] ?> fa-fw"></i>
          <span class="navbar-item-label"><?= isset($navbarItem["allowHtml"]) && $navbarItem["allowHtml"] ? $navbarItem["label"] : htmlspecialchars($navbarItem["label"]) ?></span>
        </a>
      <?php } ?>
    <?php } ?>
  </div>

  <div class="ds-sidebar__user">
  <?php if (isset($user)) { ?>
    <div class="ds-sidebar__user-row">
      <span class="ds-avatar"><?= strtoupper(mb_substr($user["username"], 0, 1)) ?></span>
      <span class="username" style="font-weight:600"><?= htmlspecialchars($user["username"]) ?></span>
    </div>
    <a href="logout.php" class="ds-link" style="display:inline-block;margin-top:0.5rem" data-tippy-content="<?= __('nav_logout') ?>"><i class="fas fa-sign-out-alt"></i> <?= __('nav_logout') ?></a>
  <?php } else { ?>
    <a href="login.php" class="ds-btn ds-btn--primary ds-btn--md ds-btn--full"><?= __('nav_login') ?></a>
  <?php } ?>
  </div>
</nav>

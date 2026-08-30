<?php
// Render the active tab's content
ob_start();
$activeTab = $_GET["tab"] ?? "users";
require "pages/admin/admin-tab-render.php";
$activeContent = ob_get_clean();

// Build tab array with lazy loading for inactive tabs
$tabs = [];
$tabIds = ['users', 'players', 'scores', 'analytics', 'api-errors', 'migrate'];
$tabLabels = [
  'users' => 'Users',
  'players' => 'Players',
  'scores' => 'Scores',
  'analytics' => 'Analytics',
  'api-errors' => 'API Errors',
  'migrate' => 'Migrations',
];
$tabIcons = [
  'users' => 'fas fa-users',
  'players' => 'fas fa-user-friends',
  'scores' => 'fas fa-star',
  'analytics' => 'fas fa-chart-pie',
  'api-errors' => 'fas fa-exclamation-triangle',
  'migrate' => 'fas fa-database',
];

foreach ($tabIds as $id) {
  $tab = [
    'id' => $id,
    'label' => $tabLabels[$id],
    'icon' => $tabIcons[$id],
  ];

  if ($id === $activeTab) {
    $tab['content'] = $activeContent;
  } else {
    $tab['url'] = '/admin.php?tab=' . $id . '&ajax=1';
    $skeletonType = ($id === 'analytics' || $id === 'api-errors') ? 'chart' : 'table-row';
    $tab['content'] = ui_skeleton($skeletonType, $skeletonType === 'chart' ? 2 : 8);
  }

  $tabs[] = $tab;
}

echo ui_tabs($tabs, ["active" => $activeTab]);
?>

<?php
echo ui_modal('modal-admin-user-toggle', [
  'title' => __('admin_confirm_title'),
  'content' => '<p id="modal-admin-user-toggle__body"></p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-user-toggle', onAdminUserToggleClose)"]]) .
    ui_button(__('admin_confirm_confirm'), 'primary', 'md', ['icon' => 'fas fa-check', 'attrs' => ['onclick' => 'adminUserToggleConfirm()'], 'class' => 'ui-destructive']),
]);

echo ui_modal('modal-admin-player-ban', [
  'title' => __('admin_confirm_title'),
  'content' => '<p id="modal-admin-player-ban__body"></p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-player-ban', onAdminPlayerBanClose)"]]) .
    ui_button(__('admin_confirm_confirm'), 'danger', 'md', ['icon' => 'fas fa-ban', 'attrs' => ['onclick' => 'adminPlayerBanConfirm()'], 'class' => 'ui-destructive']),
]);

echo ui_modal('modal-admin-score-delete', [
  'title' => __('admin_confirm_deletion_title'),
  'content' => '<p id="modal-admin-score-delete__body"></p><p>' . __('scores_modal_delete_irreversible') . '</p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-score-delete', onAdminScoreDeleteClose)"]]) .
    ui_button(__('scores_modal_delete_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'adminScoreDeleteConfirm()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]);

echo ui_modal('modal-admin-score-ban', [
  'title' => __('admin_confirm_ban_title'),
  'content' => '<p id="modal-admin-score-ban__body"></p><p>' . __('admin_ban_warning') . '</p><p>' . __('admin_ban_note') . '</p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-score-ban', onAdminScoreBanClose)"]]) .
    ui_button(__('scores_modal_ban_confirm'), 'danger', 'md', ['icon' => 'fas fa-ban', 'attrs' => ['onclick' => 'adminScoreBanConfirm()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]);

echo ui_modal('modal-sync-indexes', [
  'title' => __('admin_sync_title'),
  'content' => '<p>' . __('admin_sync_desc') . '</p>',
  'footer' =>
    ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-sync-indexes')"]]) .
    ui_button(__('admin_sync_run'), 'primary', 'md', ['icon' => 'fas fa-sync', 'attrs' => ['onclick' => 'syncIndexesConfirm()']]),
  'footer_right' => true,
]);
?>

<script>
const _t = <?= json_encode([
  'scores_modal_delete_body' => __('scores_modal_delete_body'),
  'scores_modal_ban_body1' => __('scores_modal_ban_body1'),
  'admin_col_banned' => __('admin_col_banned'),
  'admin_ban' => __('admin_ban'),
  'admin_unban' => __('admin_unban'),
  'admin_ban_infinitive' => __('admin_ban_infinitive'),
  'admin_unban_infinitive' => __('admin_unban_infinitive'),
  'admin_enable_infinitive' => __('admin_enable_infinitive'),
  'admin_disable_infinitive' => __('admin_disable_infinitive'),
  'admin_confirm_player_ban_body' => __('admin_confirm_player_ban_body'),
  'admin_confirm_user_toggle_body' => __('admin_confirm_user_toggle_body'),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

let adminToggleUrl = '';
let adminToggleBody = '';
const adminUserToggleBody = document.getElementById('modal-admin-user-toggle__body');
const adminPlayerBanBody = document.getElementById('modal-admin-player-ban__body');
let adminScoreDeleteUrl = '';
let adminScoreDeleteBody = '';
let adminScoreBanUrl = '';
let adminScoreBanBody = '';
const adminScoreDeleteBodyEl = document.getElementById('modal-admin-score-delete__body');
const adminScoreBanBodyEl = document.getElementById('modal-admin-score-ban__body');

function openModal(id, onOpen, data) {
  var overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.style.display = 'block';
  overlay.removeAttribute('data-armed');
  var btn = overlay.querySelector('.ui-destructive');
  if (btn) {
    btn.innerHTML = btn.getAttribute('data-original-html') || btn.innerHTML;
    btn.classList.remove('is-armed', 'animate-confirm-pulse');
  }
  if (typeof onOpen === 'function') onOpen(data);
}

function closeModal(id, onClose) {
  var overlay = document.getElementById(id);
  if (!overlay) return;
  overlay.style.display = 'none';
  if (typeof onClose === 'function') onClose();
}

function onAdminUserToggleClose() { adminToggleUrl = ''; adminToggleBody = ''; }
function onAdminPlayerBanClose() { adminToggleUrl = ''; adminToggleBody = ''; }
function onAdminScoreDeleteClose() { adminScoreDeleteUrl = ''; adminScoreDeleteBody = ''; }
function onAdminScoreBanClose() { adminScoreBanUrl = ''; adminScoreBanBody = ''; }

function postAndReload(url, body) {
  fetch(url, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: body })
    .then(function() { location.reload(); });
}

function adminUserToggleConfirm() {
  if (adminToggleUrl) postAndReload(adminToggleUrl, adminToggleBody);
}

function adminPlayerBanConfirm() {
  if (adminToggleUrl) postAndReload(adminToggleUrl, adminToggleBody);
}

function adminScoreDeleteConfirm() {
  if (adminScoreDeleteUrl) postAndReload(adminScoreDeleteUrl, adminScoreDeleteBody);
}

function adminScoreBanConfirm() {
  if (adminScoreBanUrl) postAndReload(adminScoreBanUrl, adminScoreBanBody);
}

function syncIndexesConfirm() {
  closeModal('modal-sync-indexes');
  var output = document.getElementById('sync-indexes-output');
  if (!output) return;
  output.style.display = 'block';
  output.innerHTML = '<div class="text-[#a6e3a1]">Running...</div>';

  fetch('/sync-indexes.php', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      var html = '';
      if (data.errors && data.errors.length) {
        data.errors.forEach(function(e) { html += '<div class="text-[#f38ba8]">ERROR ' + e + '</div>'; });
      }
      if (data.created && data.created.length) {
        data.created.forEach(function(c) { html += '<div class="text-[#a6e3a1]">CREATED ' + c + '</div>'; });
      }
      if (data.skipped && data.skipped.length) {
        data.skipped.forEach(function(s) { html += '<div class="text-[#a6adc8]">SKIP ' + s + '</div>'; });
      }
      if ((!data.created || !data.created.length) && (!data.errors || !data.errors.length)) {
        html = '<div class="text-[#a6e3a1]">All indexes already exist.</div>';
      }
      output.innerHTML = html;
    })
    .catch(function(err) {
      output.innerHTML = '<div class="text-[#f38ba8]">ERROR ' + err.message + '</div>';
    });
}

document.addEventListener('click', function (e) {
  var scoreDelete = e.target.closest('[data-admin-score-delete]');
  if (scoreDelete) {
    e.preventDefault();
    adminScoreDeleteUrl = scoreDelete.getAttribute('data-post-url');
    adminScoreDeleteBody = scoreDelete.getAttribute('data-post-body');
    adminScoreDeleteBodyEl.textContent = _t.scores_modal_delete_body + ' ' + (scoreDelete.dataset.player || '') + '?';
    openModal('modal-admin-score-delete');
    return;
  }

  var scoreBan = e.target.closest('[data-admin-score-ban]');
  if (scoreBan) {
    e.preventDefault();
    adminScoreBanUrl = scoreBan.getAttribute('data-post-url');
    adminScoreBanBody = scoreBan.getAttribute('data-post-body');
    adminScoreBanBodyEl.textContent = _t.scores_modal_ban_body1 + ' ' + (scoreBan.dataset.player || '') + ' (' + (scoreBan.dataset.game || '') + ')?';
    openModal('modal-admin-score-ban');
    return;
  }

  var toggle = e.target.closest('.ui-toggle');
  if (!toggle) return;
  e.preventDefault();
  adminToggleUrl = toggle.getAttribute('data-post-url');
  adminToggleBody = toggle.getAttribute('data-post-body');

  var tableHeader = toggle.closest('.ui-table').querySelector('.ui-table-header');
  if (tableHeader && tableHeader.textContent.indexOf(_t.admin_col_banned) !== -1) {
    var row = toggle.closest('tr');
    var cells = row.querySelectorAll('.ui-table-cell');
    var playerName = cells.length > 1 ? cells[1].textContent.trim() : '';
    var gameName = cells.length > 3 ? cells[3].textContent.trim() : '';
    var isBanning = toggle.style.color === 'rgb(156, 163, 175)' || toggle.getAttribute('title') === _t.admin_ban;
    var actionLabel = isBanning ? _t.admin_ban_infinitive : _t.admin_unban_infinitive;
    adminPlayerBanBody.textContent = _t.admin_confirm_player_ban_body
      .replace('{action}', actionLabel)
      .replace('{player}', playerName)
      .replace('{game}', gameName);
    openModal('modal-admin-player-ban');
  } else {
    var row = toggle.closest('tr');
    var cells = row.querySelectorAll('.ui-table-cell');
    var userName = cells.length > 1 ? cells[1].textContent.trim() : '';
    var isEnabling = toggle.style.color === 'rgb(156, 163, 175)';
    var actionLabel = isEnabling ? _t.admin_enable_infinitive : _t.admin_disable_infinitive;
    adminUserToggleBody.textContent = _t.admin_confirm_user_toggle_body
      .replace('{action}', actionLabel)
      .replace('{user}', userName);
    openModal('modal-admin-user-toggle');
  }
});
</script>

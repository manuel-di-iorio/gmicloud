<?php
$configContent = '
  <div class="mb-5 flex flex-wrap items-center justify-end gap-2 md:gap-2.5">
    ' . ui_button(__('game_tab_delete'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => "openModal('modal-delete-game', onDeleteGameModalOpen, { gameId: $gameId, gameName: '" . escapeChars($game['name']) . "' })"]]) . '
  </div>

  <div class="flex flex-wrap gap-5">
    <div class="min-w-[300px] flex-1">
      <div class="' . ui_card_classes(['padding' => 'md']) . '">
        ' . ui_card_title(__('game_details_title'), ['icon' => 'fas fa-cog']) . '

        ' . ($gameTeam ? '<div class="mb-4 p-3 rounded-lg border border-primary-color/20 bg-primary-color/5">
          <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-text-secondary">' . __('team_selector_label') . '</label>
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-2 font-semibold text-text-headings"><i class="fas fa-users text-primary-color"></i>' . htmlspecialchars($gameTeam["name"]) . '</span>
            ' . ($isTeamAdmin ? ui_button(__('team_games_move'), 'secondary', 'sm', ['icon' => 'fas fa-exchange-alt', 'href' => 'team-move-game.php?id=' . $gameId]) : '') . '
          </div>
        </div>' : '<div class="mb-4 p-3 rounded-lg border border-border-color bg-surface-offset">
          <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-text-secondary">' . __('team_selector_label') . '</label>
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-2 text-text-secondary"><i class="fas fa-user"></i>' . __('team_selector_personal') . '</span>
            ' . ui_button(__('team_games_move'), 'secondary', 'sm', ['icon' => 'fas fa-exchange-alt', 'href' => 'team-move-game.php?id=' . $gameId]) . '
          </div>
        </div>') . '

        <label class="mb-2.5 block font-semibold text-text-headings">' . __('game_details_id') . '</label>
        <div class="relative mb-5">
          <input id="input-gameid" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:!bg-surface-sidebar disabled:text-input-text-disabled disabled:cursor-not-allowed" value="' . $gameId . '" disabled>
        </div>

        <label class="mb-2.5 mt-4 block font-semibold text-text-headings">' . __('game_details_secret') . '</label>
        <div class="mb-3 text-sm text-text-secondary">' . __('game_details_secret_help') . '</div>
        <div class="relative mb-5">
          <input id="input-secret" type="password" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" value="' . htmlspecialchars($game["client_secret"]) . '" disabled>
          <i class="fas fa-sync absolute right-16 top-1/2 -translate-y-1/2 cursor-pointer p-2 text-text-secondary transition-colors hover:text-text" onclick="openModal(\'modal-regenerate-secret\')" data-tippy-content="' . __('game_details_secret_regenerate_tooltip') . '"></i>
          <i class="fas fa-eye absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer p-2 text-text-secondary transition-colors hover:text-text" onclick="toggleSecretVisibility(this)" data-tippy-content="' . __('game_details_secret_toggle_tooltip') . '"></i>
        </div>

        <div class="mt-4 border-0 border-t border-solid border-border-color pt-4">
          <label class="mb-2.5 block font-semibold text-text-headings">' . __('game_rename_title') . '</label>
          <div class="relative mb-5">
            <input id="input-game-name" name="name" type="text" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" value="' . htmlspecialchars($game["name"]) . '" required>
          </div>

          <label class="mt-3 flex cursor-pointer items-center gap-3">
            <input type="checkbox" id="toggle-player-auth" class="w-4 h-4 rounded border-[var(--border-color)] text-[var(--primary-color)] focus:ring-[var(--primary-color)]" ' . ($game["require_player_auth"] ? 'checked' : '') . '>
            <div>
              <span class="text-sm font-semibold text-[var(--text-color)]">' . __('game_require_player_auth') . '</span>
              <p class="text-xs text-[var(--text-color-secondary)] mt-0.5">' . __('game_require_player_auth_desc') . '</p>
            </div>
          </label>
          <div class="mt-3 flex items-center gap-2">
            ' . ui_button(__('game_save'), 'primary', 'sm', ['icon' => 'fas fa-save', 'attrs' => ['id' => 'btn-save-player-auth']]) . '
            <span id="spinner-player-auth" class="hidden">' . ui_spinner('sm') . '</span>
          </div>
        </div>
      </div>
    </div>
  </div>

';


$activeTab = $_GET["tab"] ?? "config";

$tabContent = '';
if (in_array($activeTab, ['analytics', 'players', 'leaderboards'])) {
  ob_start();
  require "pages/game/game-tab-render.php";
  $tabContent = ob_get_clean();
}

echo ui_tabs([
  ["id" => "config", "label" => __('game_tab_config'), "icon" => "fas fa-cog", "content" => $configContent],
  ["id" => "players", "label" => __('game_tab_players'), "icon" => "fas fa-users", "content" => $activeTab === 'players' ? $tabContent : ui_skeleton('chart', 2), "url" => $activeTab !== 'players' ? "/game.php?id=$gameId&tab=players&ajax=1" : null],
  ["id" => "leaderboards", "label" => __('game_tab_leaderboards_tab'), "icon" => "fas fa-trophy", "content" => $activeTab === 'leaderboards' ? $tabContent : ui_skeleton('chart', 2), "url" => $activeTab !== 'leaderboards' ? "/game.php?id=$gameId&tab=leaderboards&ajax=1" : null],
  ["id" => "analytics", "label" => __('game_tab_analytics'), "icon" => "fas fa-chart-pie", "content" => $activeTab === 'analytics' ? $tabContent : ui_skeleton('chart', 2), "url" => $activeTab !== 'analytics' ? "/game.php?id=$gameId&tab=analytics&ajax=1" : null],
], ["active" => $activeTab]);
?>

<?= ui_modal('modal-regenerate-secret', [
  'title' => __('game_modal_secret_title'),
  'content' => '<p>' . __('game_modal_secret_body') . '</p>
    <div class="mt-4 rounded-lg border-0 border-l-4 border-solid border-amber-500 bg-amber-500/10 p-4 text-amber-800 dark:text-amber-200">
      <p><i class="fas fa-exclamation-triangle mr-2"></i><strong>' . __('game_modal_secret_warning_label') . '</strong> ' . __('game_modal_secret_warning') . '</p>
      <p>' . __('game_modal_secret_irreversible') . '</p>
    </div>',
  'footer' =>
    ui_button(__('game_modal_secret_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-regenerate-secret')"]]) .
    ui_button(__('game_modal_secret_confirm'), 'danger', 'md', ['icon' => 'fas fa-sync', 'attrs' => ['onclick' => 'regenerateSecret()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<?= ui_modal('modal-delete-game', [
  'title' => __('game_modal_delete_title'),
  'content' => '<p>' . __('game_modal_delete_body') . ' <strong><span id="modal-game-name"></span></strong> ?</p><p>' . __('game_modal_delete_irreversible') . '</p>',
  'footer' =>
    ui_button(__('game_modal_delete_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-game', onDeleteGameModalClose)"]]) .
    ui_button(__('game_modal_delete_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteGame()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>

<script>
var csrfToken = '<?= csrf_token() ?>';
const modalGameDiv = document.getElementById('modal-game-name');
let modalSelectedGameId;

function onDeleteGameModalOpen({ gameId, gameName }) {
  modalSelectedGameId = gameId;
  modalGameDiv.innerHTML = gameName;
}

function onDeleteGameModalClose() {
  modalGameDiv.innerHTML = "";
}

function deleteGame() {
  fetch("delete-game.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "id=" + encodeURIComponent(modalSelectedGameId) + "&csrf_token=" + encodeURIComponent(csrfToken)
  }).then(function() { location.href = "games.php"; });
}

function saveRequirePlayerAuth() {
  var btn = document.getElementById("btn-save-player-auth");
  var spinner = document.getElementById("spinner-player-auth");
  var checkbox = document.getElementById("toggle-player-auth");
  var nameInput = document.getElementById("input-game-name");
  var enabled = checkbox.checked ? "1" : "0";

  var body = "id=" + encodeURIComponent(<?= $gameId ?>) + "&enabled=" + enabled + "&name=" + encodeURIComponent(nameInput.value) + "&csrf_token=" + encodeURIComponent(csrfToken);

  fetchWithSpinner({
    button: btn,
    spinner: spinner,
    fetch: {
      url: "/api/internal/games/toggle-player-auth.php",
      options: {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: body
      }
    },
    onSuccess: function(data) {
      if (data.status === 200) {
        uiToastSuccess(<?= json_encode(__("toast_settings_saved")) ?>);
      } else {
        uiToastError(<?= json_encode(__("toast_error_update")) ?>);
      }
    },
    onError: function() {
      uiToastError(<?= json_encode(__("toast_network_error")) ?>);
    }
  });
}

document.addEventListener("click", function(e) {
  if (e.target && e.target.id === "btn-save-player-auth") {
    saveRequirePlayerAuth();
  }
});
</script>

<?php require_once("game.view.script.php"); ?>

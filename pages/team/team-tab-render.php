<?php
switch ($activeTab) {
  case 'config':
    $html = '';

    if ($isTeamAdmin) {
      $html .= '<div class="mb-5 flex flex-wrap items-center justify-end gap-2 md:gap-2.5">
        ' . ui_button(__('team_settings_delete'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => "openModal('modal-delete-team')"]]) . '
      </div>';
    }

    $html .= '<div class="max-w-[500px]">
      <div class="' . ui_card_classes(['padding' => 'md']) . '">
        ' . ui_card_title(__('team_settings_title'), ['icon' => 'fas fa-edit']) . '
        <form method="POST" action="/team-settings.php?id=' . $teamId . '">
          ' . csrf_field() . '
          <label class="mb-2 block font-semibold text-text-headings">' . __('team_settings_name') . '</label>
          <div class="mb-5">
            <input name="name" type="text" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" value="' . htmlspecialchars($team['name']) . '" required>
          </div>
          ' . ui_button(__('team_settings_save'), 'primary', 'md', ['icon' => 'fa fa-edit', 'type' => 'submit', 'class' => 'mt-2']) . '
        </form>
      </div>
    </div>';
    echo $html;
    break;

  case 'members':
    $html = '';

    if ($isTeamAdmin) {
      $html .= '<div class="' . ui_card_classes(['padding' => 'md', 'class' => 'mb-5']) . '">
        ' . ui_card_title(__('team_members_add_title'), ['icon' => 'fas fa-user-plus']) . '
        <p class="mb-4 mt-0 text-sm text-text-secondary">' . __('team_members_add_note', ['site' => $config['platformTitle']]) . '</p>
        <form method="POST" action="/team-members.php?id=' . $teamId . '" class="flex flex-wrap items-end gap-3">
          ' . csrf_field() . '
          <div class="min-w-[200px] flex-1">
            <label class="mb-2 block font-semibold text-text-headings">' . __('team_members_add_discord_id') . '</label>
            <input name="discord_id" type="text" required class="h-[42px] w-full px-3.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" placeholder="' . __('team_members_add_discord_id') . '">
          </div>
          <div class="min-w-[150px]">
            <label class="mb-2 block font-semibold text-text-headings">' . __('team_members_add_role') . '</label>
            <select name="role" class="h-[42px] w-full px-3.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)]">
              <option value="member">' . __('team_members_role_member') . '</option>
              <option value="admin">' . __('team_members_role_admin') . '</option>
            </select>
          </div>
          <div>
            <label class="invisible mb-2 block">-</label>
            ' . ui_button(__('team_members_add_submit'), 'primary', 'md', ['icon' => 'fas fa-user-plus', 'type' => 'submit', 'class' => '!h-[42px]']) . '
          </div>
        </form>
      </div>';
    }

    if (!empty($members)) {
      $html .= '<div class="ui-table-container"><table class="ui-table"><thead class="ui-table-header"><tr>
        <th class="ui-table-header-cell">' . __('admin_col_username') . '</th>
        <th class="ui-table-header-cell">' . __('teams_col_role') . '</th>';

      if ($isTeamAdmin) {
        $html .= '<th class="ui-table-header-cell">' . __('table_actions') . '</th>';
      }

      $html .= '</tr></thead><tbody class="ui-table-body">';

      foreach ($members as $m) {
        $isSelf = (int)$m['user_id'] === $userId;
        $html .= '<tr class="ui-table-row">
          <td class="ui-table-cell">' . htmlspecialchars($m['username']) . ($isSelf ? ' <span class="text-text-secondary">(tu)</span>' : '') . '</td>
          <td class="ui-table-cell">';

        if ($m['role'] === 'admin') {
          $html .= ui_badge(__('team_members_role_admin'), 'primary', ['icon' => 'fas fa-crown']);
        } else {
          $html .= ui_badge(__('team_members_role_member'), 'default');
        }

        $html .= '</td>';

        if ($isTeamAdmin) {
          $html .= '<td class="ui-table-cell actions-cell">';
          if (!$isSelf) {
            $postBody = http_build_query(['id' => $teamId, 'user_id' => $m['user_id'], 'csrf_token' => csrf_token()]);
            $html .= '<a href="javascript:void(0)" class="admin-score-action admin-score-action--danger" data-tippy-content="' . __('team_members_remove') . '" onclick="if(confirm(\'' . __('team_members_remove') . '?\'))fetch(\'/team-members-remove.php\',{method:\'POST\',headers:{\'Content-Type\':\'application/x-www-form-urlencoded\'},body:\'' . addslashes($postBody) . '\'}).then(function(){location.reload();})">
              <i class="fas fa-times-circle"></i>
            </a>';
          }
          $html .= '</td>';
        }

        $html .= '</tr>';
      }

      $html .= '</tbody></table></div>';
    } else {
      $html .= ui_empty_state(__('table_empty'), ['icon' => 'fas fa-users']);
    }

    echo $html;
    break;

  case 'games':
    $html = '';
    $nameValue = htmlspecialchars($_GET['name'] ?? '');

    if ($isTeamAdmin) {
      $html .= '<div class="mb-5 flex flex-wrap items-center justify-end gap-2 md:gap-2.5">
        ' . ui_button(__('add_game_submit'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-game.php']) . '
      </div>';
    }

    $html .= '<form method="GET" action="/team.php" class="bg-surface-card border border-solid border-border-color rounded-lg p-3 shadow-sm mb-4">
      <input type="hidden" name="id" value="' . $teamId . '">
      <input type="hidden" name="tab" value="games">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
          <label class="font-semibold text-sm text-[var(--text-color)] block mb-1.5">' . __('games_filter_name') . '</label>
          <input type="text" name="name" value="' . $nameValue . '" placeholder="' . __('games_filter_placeholder') . '" class="w-full px-3.5 py-2 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] h-10">
        </div>
        <div class="flex items-end gap-5">
          ' . ui_button(__('filter_apply'), 'primary', 'md', ['type' => 'submit']) . '
          ' . ($nameValue ? ui_button(__('filter_reset'), 'secondary', 'md', ['icon' => 'fas fa-times', 'href' => '/team.php?id=' . $teamId . '&tab=games']) : '') . '
        </div>
      </div>
    </form>';

    if (!empty($games)) {
      $tableColumns = [
        [
          "label" => __('games_col_name'),
          "key" => "name",
          "sortable" => true,
          "format_callback" => function ($value, $row) {
            return '<a href="game.php?id=' . $row["game_id"] . '" class="link" data-tippy-content="' . __('games_row_tooltip') . '">' . htmlspecialchars($value) . '</a>';
          }
        ],
        ["label" => __('games_col_scores'), "key" => "_scoresCount", "sortable" => true],
        ["label" => __('games_col_players'), "key" => "_playersCount", "sortable" => true],
      ];

      $tableActions = [
        [
          "label" => __('games_action_leaderboards'),
          "icon" => "fas fa-trophy",
          "url" => function ($data) {
            return "leaderboards.php?game_id={$data['game_id']}";
          },
          "class" => "btn-link"
        ],
      ];

      if ($isTeamAdmin) {
        $tableActions[] = [
          "label" => __('team_games_move'),
          "icon" => "fas fa-exchange-alt",
          "url" => function ($data) {
            return "team-move-game.php?id={$data['game_id']}";
          },
          "class" => "btn-link"
        ];
      }

      $tableOptions = [
        "table_class" => "ui-table",
        "base_url" => "team.php?id=$teamId&tab=games&",
        "primary_key" => "game_id"
      ];

      ob_start();
      render_table($games, $tableColumns, $tableActions, $tableOptions);
      $html .= ob_get_clean();
    } else {
      $hasFilter = isset($_GET['name']) && trim($_GET['name']) !== '';
      if ($hasFilter) {
        $html .= ui_empty_state(__('games_empty_filter_title'), [
          'icon' => 'fas fa-search',
          'description' => __('games_empty_filter_desc'),
          'action' => ui_button(__('games_empty_filter_btn'), 'primary', 'md', ['href' => '/team.php?id=' . $teamId . '&tab=games']),
        ]);
      } else {
        $html .= ui_empty_state(__('team_games_empty'), ['icon' => 'fas fa-gamepad']);
      }
    }

    echo $html;
    break;
}

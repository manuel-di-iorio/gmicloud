<div class="internal-page">
  <?php if (!empty($teams)) { ?>
    <div class="mb-6 flex flex-wrap items-center justify-end gap-2 md:gap-2.5">
      <?= ui_button(__('teams_create_button'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-team.php']) ?>
    </div>
    <div class="ui-table-container">
      <table class="ui-table">
        <thead class="ui-table-header">
          <tr>
            <th class="ui-table-header-cell"><?= __('teams_col_name') ?></th>
            <th class="ui-table-header-cell"><?= __('teams_col_role') ?></th>
            <th class="ui-table-header-cell"><?= __('teams_col_members') ?></th>
            <th class="ui-table-header-cell"><?= __('table_actions') ?></th>
          </tr>
        </thead>
        <tbody class="ui-table-body">
          <?php foreach ($teams as $t) { ?>
            <tr class="ui-table-row">
              <td class="ui-table-cell">
                <a href="team.php?id=<?= $t['team_id'] ?>" class="link" data-tippy-content="<?= __('teams_action_view') ?>">
                  <?= htmlspecialchars($t['name']) ?>
                </a>
              </td>
              <td class="ui-table-cell">
                <?php if ($t['role'] === 'admin') { ?>
                  <?= ui_badge(__('team_members_role_admin'), 'primary', ['icon' => 'fas fa-crown']) ?>
                <?php } else { ?>
                  <?= ui_badge(__('team_members_role_member'), 'default') ?>
                <?php } ?>
              </td>
              <td class="ui-table-cell"><?= (int)$t['member_count'] ?></td>
              <td class="ui-table-cell actions-cell">
                <a href="team.php?id=<?= $t['team_id'] ?>" class="admin-score-action" data-tippy-content="<?= __('teams_action_view') ?>">
                  <i class="fas fa-cog"></i>
                </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  <?php } else { ?>
    <?= ui_empty_state(__('teams_empty_title'), [
      'icon' => 'fas fa-users',
      'description' => __('teams_empty_desc'),
      'action' => ui_button(__('teams_empty_btn'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-team.php']),
    ]) ?>
  <?php } ?>
</div>

<div class="internal-page">
  <?php if (!empty($teams)) { ?>
    <div class="mb-6 flex flex-wrap items-center justify-end gap-2 md:gap-2.5">
      <?= ui_button(__('teams_create_button'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-team.php']) ?>
    </div>
    <div class="ds-table-wrap">
      <table class="ds-table">
        <thead class="ds-table__head">
          <tr>
            <th class="ds-table__th"><?= __('teams_col_name') ?></th>
            <th class="ds-table__th"><?= __('teams_col_role') ?></th>
            <th class="ds-table__th"><?= __('teams_col_members') ?></th>
            <th class="ds-table__th ds-table__th--actions"><?= __('table_actions') ?></th>
          </tr>
        </thead>
        <tbody class="ds-table__body">
          <?php foreach ($teams as $t) { ?>
            <tr class="ds-table__row">
              <td class="ds-table__cell">
                <a href="team.php?id=<?= $t['team_id'] ?>" class="link" data-tippy-content="<?= __('teams_action_view') ?>">
                  <?= htmlspecialchars($t['name']) ?>
                </a>
              </td>
              <td class="ds-table__cell">
                <?php if ($t['role'] === 'admin') { ?>
                  <?= ui_badge(__('team_members_role_admin'), 'primary', ['icon' => 'fas fa-crown']) ?>
                <?php } else { ?>
                  <?= ui_badge(__('team_members_role_member'), 'default') ?>
                <?php } ?>
              </td>
              <td class="ds-table__cell"><?= (int)$t['member_count'] ?></td>
              <td class="ds-table__cell ds-table__cell--actions">
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

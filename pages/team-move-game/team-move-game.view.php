<div class="internal-page">
  <div class="<?= ui_card_classes(['padding' => 'md', 'class' => 'max-w-[500px]']) ?>">
    <?= ui_card_title(__('team_games_move_title') . ': ' . $game['name'], ['icon' => 'fas fa-exchange-alt']) ?>
    <form method="POST" action="/team-move-game.php?id=<?= $gameId ?>">
      <?= csrf_field() ?>
      <label class="mb-2 block font-semibold text-text-headings"><?= __('team_games_move_to') ?></label>
      <div class="mb-5">
        <select name="target_team_id" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)]">
          <option value="0"><?= __('team_games_move_personal') ?></option>
          <?php foreach ($userTeams as $ut) { ?>
            <option value="<?= $ut['team_id'] ?>"><?= htmlspecialchars($ut['name']) ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="mt-4">
        <?= ui_button(__('team_games_move_confirm'), 'primary', 'md', ['icon' => 'fas fa-exchange-alt', 'type' => 'submit']) ?>
      </div>
    </form>
  </div>
</div>

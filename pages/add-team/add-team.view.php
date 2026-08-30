<div class="internal-page">
  <div class="<?= ui_card_classes(['padding' => 'md', 'class' => 'max-w-[500px]']) ?>">
    <?= ui_card_title(__('teams_create_title'), ['icon' => 'fas fa-users']) ?>
    <form method="POST" action="/add-team.php">
      <?= csrf_field() ?>
      <label class="mb-2 block font-semibold text-text-headings"><?= __('teams_create_name') ?></label>
      <div class="mb-5">
        <input name="name" type="text" class="w-full px-3.5 py-2.5 border border-solid border-[var(--border-color)] rounded-lg text-[0.95rem] leading-normal bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)] transition-colors duration-200 box-border focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)] disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed" placeholder="<?= __('teams_create_name') ?>" required>
      </div>
      <div class="mt-4">
        <?= ui_button(__('teams_create_submit'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'type' => 'submit']) ?>
      </div>
    </form>
  </div>
</div>

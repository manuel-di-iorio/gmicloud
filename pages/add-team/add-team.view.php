<div class="internal-page">
  <div class="<?= ui_card_classes(['padding' => 'md', 'class' => 'max-w-[500px]']) ?>">
    <?= ui_card_title(__('teams_create_title'), ['icon' => 'fas fa-users']) ?>
    <form method="POST" action="/add-team.php">
      <?= csrf_field() ?>
      <label class="mb-2 block font-semibold text-content"><?= __('teams_create_name') ?></label>
      <div class="mb-5">
        <input name="name" type="text" class="w-full px-3.5 py-2.5 border border-solid border-default rounded-sm text-[0.95rem] leading-normal bg-surface-solid text-content placeholder:text-content-muted transition-colors duration-200 box-border focus:border-content/40 focus:outline-none disabled:bg-surface-muted disabled:text-content-muted disabled:cursor-not-allowed" placeholder="<?= __('teams_create_name') ?>" required>
      </div>
      <div class="mt-4">
        <?= ui_button(__('teams_create_submit'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'type' => 'submit']) ?>
      </div>
    </form>
  </div>
</div>

<div class="internal-page">
    <form method="POST" class="<?= ui_card_classes(['padding' => 'md', 'class' => 'max-w-2xl']) ?>">
        <?= csrf_field() ?>
        <?php if (isset($error)) { ?>
            <?= ui_alert($error, 'danger') ?>
        <?php } ?>

        <?= ui_card_title(__('add_lb_title'), ['icon' => 'fas fa-trophy']) ?>

        <div class="mb-4">
            <label class="block font-semibold mb-1.5 text-sm text-[var(--ds-color-content)]" for="name"><?= __('add_lb_name') ?></label>
            <input type="text" name="name" class="w-full px-3.5 py-2.5 border border-solid border-default rounded-sm text-[0.95rem] leading-normal bg-surface-solid text-content placeholder:text-content-muted transition-colors duration-200 box-border focus:border-content/40 focus:outline-none disabled:bg-surface-muted disabled:text-content-muted disabled:cursor-not-allowed" required
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1.5 text-sm text-[var(--ds-color-content)]" for="description"><?= __('add_lb_description') ?></label>
            <textarea name="description" class="w-full px-3.5 py-2.5 border border-solid border-default rounded-sm text-[0.95rem] leading-normal bg-surface-solid text-content placeholder:text-content-muted transition-colors duration-200 box-border focus:border-content/40 focus:outline-none disabled:bg-surface-muted disabled:text-content-muted disabled:cursor-not-allowed min-h-[80px] resize-y"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <?= ui_checkbox('is_private', !isset($_POST['is_private']) || $_POST['is_private'] === '1', [
            'label' => __("add_lb_protected"),
            'description' => __("add_lb_protected_hint"),
            'icon' => 'fas fa-lock'
        ]) ?>

        <div class="mt-5 flex gap-2.5">
            <?= ui_button(__('add_lb_submit'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'type' => 'submit']) ?>
            <?= ui_button(__('add_lb_cancel'), 'secondary', 'md', ['href' => 'leaderboards.php?game_id=' . $game_id]) ?>
        </div>
    </form>
</div>

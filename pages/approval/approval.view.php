<div class="internal-page">
  <div class="<?= ui_card_classes(['class' => 'px-8 py-10 text-center']) ?>">
    <i class="fab fa-discord mb-4 text-5xl text-[#5865f2]" aria-hidden="true"></i>
    <h4 class="mb-2 mt-0 font-semibold text-text-headings"><?= __('approval_title') ?></h4>
    <p class="mx-auto mb-6 mt-0 max-w-[480px] text-text-secondary">
      <?= __('approval_desc1') ?>
      <strong><?= $config["platformTitle"] ?></strong> <?= __('approval_desc2') ?>
    </p>
    <?= ui_button(__('approval_button'), 'primary', 'md', ['icon' => 'fab fa-discord', 'href' => 'https://discord.gg/XfMfpNA', 'class' => '!bg-[#5865f2] hover:!bg-[#4752c4]']) ?>
  </div>
</div>

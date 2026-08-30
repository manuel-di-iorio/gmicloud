<div class="internal-page pt-14 text-center">
  <div class="<?= ui_card_classes(['class' => 'mx-auto max-w-[500px] px-10 py-14']) ?>">
    <div class="bg-gradient-to-br from-indigo-500 to-pink-500 bg-clip-text text-7xl font-extrabold leading-none text-transparent">404</div>
    <h3 class="mb-2 mt-4 font-semibold text-text-headings"><?= __('not_found_title') ?></h3>
    <p class="mb-6 mt-0 text-text-secondary"><?= __('not_found_desc') ?></p>
    <?= ui_button(__('not_found_button'), 'primary', 'md', ['icon' => 'fas fa-home', 'href' => '/']) ?>
  </div>
</div>

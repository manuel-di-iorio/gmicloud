<div class="internal-page">
  <div class="ds-toolbar">
    <?= ui_breadcrumbs([['label' => 'Home', 'href' => '/home.php'], ['label' => 'UI Kit']]) ?>
  </div>

  <h2>Buttons</h2>
  <div class="ds-panel" style="display:flex;flex-wrap:wrap;gap:0.5rem">
    <?= ui_button('Primary', 'primary', 'md') ?>
    <?= ui_button('Secondary', 'secondary', 'md') ?>
    <?= ui_button('Accent', 'accent', 'md') ?>
    <?= ui_button('Ghost', 'ghost', 'md') ?>
    <?= ui_button('Danger', 'danger', 'md') ?>
    <?= ui_button('Success', 'success', 'md') ?>
    <?= ui_button('Small', 'primary', 'sm') ?>
    <?= ui_button('Large', 'secondary', 'lg') ?>
    <?= ui_button('Link', 'primary', 'md', ['href' => '/documentation.php']) ?>
  </div>

  <h2>Alerts</h2>
  <?= ui_alert('Informational message', 'info', ['title' => 'Info']) ?>
  <?= ui_alert('Operation completed', 'success', ['title' => 'Success']) ?>
  <?= ui_alert('Pay attention', 'warning', ['title' => 'Warning']) ?>
  <?= ui_alert('Something went wrong', 'danger', ['title' => 'Error']) ?>

  <h2>Badges</h2>
  <div class="ds-panel" style="display:flex;flex-wrap:wrap;gap:0.5rem">
    <?= ui_badge('Neutral') ?>
    <?= ui_badge('Accent', 'accent') ?>
    <?= ui_badge('Success', 'success') ?>
    <?= ui_badge('Warning', 'warning') ?>
    <?= ui_badge('Danger', 'danger') ?>
    <?= ui_category_label('Tutorial', 'tutorial') ?>
    <?= ui_category_label('News', 'news') ?>
  </div>

  <h2>Cards</h2>
  <?= ui_card('<p class="ds-muted">Card body content.</p>', ['title' => 'Card title', 'footer' => ui_button('Action', 'secondary', 'sm')]) ?>

  <h2>Inputs</h2>
  <div class="ds-panel">
    <?= ui_input('demo_text', ['label' => 'Text', 'placeholder' => 'Type here']) ?>
    <?= ui_input('demo_select', ['label' => 'Select', 'type' => 'select', 'options' => ['a' => 'Option A', 'b' => 'Option B'], 'value' => 'a']) ?>
    <?= ui_input('demo_area', ['label' => 'Textarea', 'type' => 'textarea', 'value' => 'Hello']) ?>
    <?= ui_checkbox('demo_check', true, ['label' => 'Accept', 'description' => 'Optional description']) ?>
    <?= ui_search_bar('', ['placeholder' => 'Cerca...']) ?>
  </div>

  <h2>Empty state</h2>
  <?= ui_empty_state('Nothing here yet', ['description' => 'Try adjusting your filters.', 'action' => ui_button('Create', 'primary', 'md')]) ?>

  <h2>Stat cards</h2>
  <div class="ds-grid-4">
    <?= ui_stat_card('1,234', 'Scores', ['icon' => 'fas fa-star']) ?>
    <?= ui_stat_card('56', 'Players', ['icon' => 'fas fa-users']) ?>
  </div>

  <h2>Tabs</h2>
  <div class="ds-panel">
    <?= ui_tabs([
      ['id' => 'one', 'label' => 'First', 'content' => '<p class="ds-muted">First panel.</p>'],
      ['id' => 'two', 'label' => 'Second', 'content' => '<p class="ds-muted">Second panel.</p>'],
    ]) ?>
  </div>

  <h2>Table</h2>
  <?php render_table(
    [['id' => 1, 'name' => 'Alpha'], ['id' => 2, 'name' => 'Beta']],
    [['label' => 'ID', 'key' => 'id', 'sortable' => true], ['label' => 'Name', 'key' => 'name']],
    [], ['pagination' => ['current_page' => 0, 'items_per_page' => 10, 'total_items' => 2]]
  ); ?>

  <h2>Paginator</h2>
  <div class="ds-panel"><?= ui_paginator(1, 5, ['url' => '/ui-kit.php?page={page}']) ?></div>

  <h2>Modal</h2>
  <div class="ds-panel">
    <?= ui_button('Open modal', 'secondary', 'md', ['attrs' => ['onclick' => "openModal('ds-demo-modal')"]]) ?>
    <?= ui_modal('ds-demo-modal', ['title' => 'Demo modal', 'content' => '<p class="ds-muted">Modal body.</p>', 'footer' => ui_button('Close', 'secondary', 'sm', ['attrs' => ['onclick' => "closeModal('ds-demo-modal')"]]), 'footer_right' => true]) ?>
  </div>

  <h2>Spinner / Skeleton / Toggle / Actions</h2>
  <div class="ds-panel" style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
    <?= ui_spinner('md') ?>
    <?= ui_toggle(true, '#', ['labelOn' => 'On', 'labelOff' => 'Off']) ?>
    <?= ui_actions_menu([['label' => 'Edit', 'icon' => 'fas fa-edit', 'href' => '#'], ['divider' => true], ['label' => 'Delete', 'icon' => 'fas fa-trash', 'variant' => 'danger', 'href' => '#']]) ?>
  </div>
  <?= ui_skeleton('text', 2) ?>

  <h2>Code</h2>
  <?= ui_code(htmlspecialchars('gmi_init(GAME_ID, "SECRET");')) ?>

  <h2>Toast (static render)</h2>
  <?= ui_toast('Saved successfully', ['variant' => 'success', 'duration' => 0]) ?>
</div>

<div class="internal-page">
  <div class="ds-toolbar">
    <?= ui_breadcrumbs([['label' => 'Home', 'href' => '/home.php'], ['label' => 'UI Kit']]) ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Buttons</h2>
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

  <h2 class="mt-8 border-b border-default pb-2">Alerts</h2>
  <?= ui_alert('Informational message', 'info', ['title' => 'Info']) ?>
  <?= ui_alert('Operation completed', 'success', ['title' => 'Success']) ?>
  <?= ui_alert('Pay attention', 'warning', ['title' => 'Warning']) ?>
  <?= ui_alert('Something went wrong', 'danger', ['title' => 'Error']) ?>

  <h2 class="mt-8 border-b border-default pb-2">Badges</h2>
  <div class="ds-panel" style="display:flex;flex-wrap:wrap;gap:0.5rem">
    <?= ui_badge('Neutral') ?>
    <?= ui_badge('Accent', 'accent') ?>
    <?= ui_badge('Success', 'success') ?>
    <?= ui_badge('Warning', 'warning') ?>
    <?= ui_badge('Danger', 'danger') ?>
    <?= ui_category_label('Tutorial', 'tutorial') ?>
    <?= ui_category_label('News', 'news') ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Cards</h2>
  <?= ui_card('<p class="ds-muted">Card body content.</p>', ['title' => 'Card title', 'footer' => ui_button('Action', 'secondary', 'sm')]) ?>

  <h2 class="mt-8 border-b border-default pb-2">Inputs</h2>
  <div class="ds-panel">
    <?= ui_input('demo_text', ['label' => 'Text', 'placeholder' => 'Type here']) ?>
    <?= ui_input('demo_select', ['label' => 'Select', 'type' => 'select', 'options' => ['a' => 'Option A', 'b' => 'Option B'], 'value' => 'a']) ?>
    <?= ui_input('demo_area', ['label' => 'Textarea', 'type' => 'textarea', 'value' => 'Hello']) ?>
    <?= ui_checkbox('demo_check', true, ['label' => 'Accept', 'description' => 'Optional description']) ?>
    <?= ui_search_bar('', ['placeholder' => 'Cerca...']) ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Empty state</h2>
  <?= ui_empty_state('Nothing here yet', ['description' => 'Try adjusting your filters.', 'action' => ui_button('Create', 'primary', 'md')]) ?>

  <h2 class="mt-8 border-b border-default pb-2">Stat cards</h2>
  <div class="ds-grid-4">
    <?= ui_stat_card('1,234', 'Scores', ['icon' => 'fas fa-star']) ?>
    <?= ui_stat_card('56', 'Players', ['icon' => 'fas fa-users']) ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Tabs</h2>
  <div class="ds-panel">
    <?= ui_tabs([
      ['id' => 'one', 'label' => 'First', 'content' => '<p class="ds-muted">First panel.</p>'],
      ['id' => 'two', 'label' => 'Second', 'content' => '<p class="ds-muted">Second panel.</p>'],
    ]) ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Accordion</h2>
  <div class="ds-panel">
    <?= ui_accordion([
      ['title' => 'First item', 'content' => '<p class="ds-muted">First panel content.</p>'],
      ['title' => 'Second item', 'content' => '<p class="ds-muted">Second panel content.</p>'],
    ]) ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Table</h2>
  <?php render_table(
    [['id' => 1, 'name' => 'Alpha'], ['id' => 2, 'name' => 'Beta']],
    [['label' => 'ID', 'key' => 'id', 'sortable' => true], ['label' => 'Name', 'key' => 'name']],
    [], ['pagination' => ['current_page' => 0, 'items_per_page' => 10, 'total_items' => 2]]
  ); ?>

  <h2 class="mt-8 border-b border-default pb-2">Paginator</h2>
  <div class="ds-panel"><?= ui_paginator(1, 5, ['url' => '/ui-kit.php?page={page}']) ?></div>

  <h2 class="mt-8 border-b border-default pb-2">Modal</h2>
  <div class="ds-panel">
    <?= ui_button('Open modal', 'secondary', 'md', ['attrs' => ['onclick' => "openModal('ds-demo-modal')"]]) ?>
    <?= ui_modal('ds-demo-modal', ['title' => 'Demo modal', 'content' => '<p class="ds-muted">Modal body.</p>', 'footer' => ui_button('Close', 'secondary', 'sm', ['attrs' => ['onclick' => "closeModal('ds-demo-modal')"]]), 'footer_right' => true]) ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Spinner / Skeleton / Toggle / Actions</h2>
  <div class="ds-panel" style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
    <?= ui_spinner('md') ?>
    <?= ui_toggle(true, '#', ['labelOn' => 'On', 'labelOff' => 'Off']) ?>
    <?= ui_actions_menu([['label' => 'Edit', 'icon' => 'fas fa-edit', 'href' => '#'], ['divider' => true], ['label' => 'Delete', 'icon' => 'fas fa-trash', 'variant' => 'danger', 'href' => '#']]) ?>
  </div>
  <?= ui_skeleton('text', 2) ?>

  <h2 class="mt-8 border-b border-default pb-2">Code</h2>
  <?= ui_code(htmlspecialchars('gmi_init(GAME_ID, "SECRET");')) ?>

  <h2 class="mt-8 border-b border-default pb-2">Page header</h2>
  <?= ui_page_header('Page title', ['desc' => 'Short description', 'badge' => 'New', 'back_url' => '/home.php']) ?>

  <h2 class="mt-8 border-b border-default pb-2">Breadcrumbs</h2>
  <div class="ds-panel"><?= ui_breadcrumbs([['label' => 'Home', 'href' => '/'], ['label' => 'Section', 'href' => '#'], ['label' => 'Current']]) ?></div>

  <h2 class="mt-8 border-b border-default pb-2">Area title</h2>
  <div class="ds-panel">
    <?= ui_area_title('Area title') ?>
    <?= ui_area_title('Inverted area title', ['inverted' => true]) ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Social icons</h2>
  <div class="ds-panel" style="display:flex;gap:0.5rem">
    <?= ui_social_icon('https://discord.gg/', 'fab fa-discord') ?>
    <?= ui_social_icon('https://twitter.com/', 'fab fa-twitter') ?>
    <?= ui_social_icon('https://github.com/', 'fab fa-github') ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Logo</h2>
  <div class="ds-panel"><?= ui_logo() ?></div>

  <h2 class="mt-8 border-b border-default pb-2">Prose</h2>
  <?= ui_prose('<p>Prose content with <strong>bold</strong> and <a href="#">links</a>.</p>') ?>

  <h2 class="mt-8 border-b border-default pb-2">File upload</h2>
  <div class="ds-panel"><?= ui_file_upload('demo_file', ['label' => 'Upload a file']) ?></div>

  <h2 class="mt-8 border-b border-default pb-2">Icons</h2>
  <div class="ds-panel" style="display:flex;gap:1rem;font-size:1.5rem">
    <?= ui_icon('fas fa-gamepad') ?>
    <?= ui_icon('fas fa-trophy') ?>
    <?= ui_icon('fas fa-users') ?>
  </div>

  <h2 class="mt-8 border-b border-default pb-2">Sidebar (static)</h2>
  <div class="ds-panel" style="padding:0;overflow:hidden">
    <div class="ds-sidebar" style="position:static;transform:none;width:100%;height:auto;max-width:260px">
      <div class="ds-sidebar__brand">
        <a href="#" class="navbar-logo-link"><img src="/assets/images/logo.png" class="Logo" alt="Logo" style="max-width:120px;height:auto"></a>
      </div>
      <div class="ds-sidebar__menu">
        <a href="#" class="ds-sidebar__item ds-sidebar__item--active"><span class="navbar-item-label">Dashboard</span></a>
        <a href="#" class="ds-sidebar__item"><span class="navbar-item-label">Your games</span></a>
        <a href="#" class="ds-sidebar__item"><span class="navbar-item-label">Your teams</span></a>
        <a href="#" class="ds-sidebar__item"><span class="navbar-item-label">Documentation</span></a>
      </div>
      <div class="ds-sidebar__user">
        <div class="ds-sidebar__user-row">
          <span class="ds-avatar"><i class="fab fa-discord"></i></span>
          <div class="ds-sidebar__user-meta">
            <span class="ds-sidebar__user-name">ethmanu</span>
          </div>
        </div>
        <a href="#" class="ds-sidebar__logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  </div>
</div>

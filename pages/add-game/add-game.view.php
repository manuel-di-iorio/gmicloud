<?php if ($_SERVER['REQUEST_METHOD'] === "GET") { ?>
  <!-- Load the ReCaptcha script -->
  <!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
  
  <!-- Add game form -->
  <form class="<?= ui_card_classes(['padding' => 'md', 'class' => 'max-w-2xl']) ?>" id="form-add-game" method="POST" onsubmit="return onSubmit()">
    <?= csrf_field() ?>
    <?= ui_card_title(__('add_game_title'), ['icon' => 'fas fa-gamepad']) ?>
    <div class="mb-4">
      <label class="block font-semibold mb-1.5 text-sm text-[var(--ds-color-content)]" for="name"><?= __('add_game_name_label') ?></label>
      <input class="w-full px-3.5 py-2.5 border border-solid border-default rounded-sm text-[0.95rem] leading-normal bg-surface-solid text-content placeholder:text-content-muted transition-colors duration-200 box-border focus:border-content/40 focus:outline-none disabled:bg-surface-muted disabled:text-content-muted disabled:cursor-not-allowed" name="name" id="name" type="text" maxlength="64" required placeholder="<?= __('add_game_name_placeholder') ?>">
    </div>

    <div class="mb-4">
      <label class="flex items-center gap-2 cursor-pointer">
        <input class="w-4 h-4 rounded border-default text-[var(--ds-color-secondary)] focus:ring-[var(--ds-color-secondary)]" type="checkbox" name="require_player_auth" id="require_player_auth" value="1">
        <span class="text-sm text-[var(--ds-color-content)]"><?= __('add_game_require_player_auth') ?></span>
      </label>
      <p class="mt-1 text-xs text-[var(--ds-color-content-muted)]"><?= __('add_game_require_player_auth_desc') ?></p>
    </div>

    <!-- ReCaptcha div -->
    <div id='recaptcha' 
         class="g-recaptcha"
         data-sitekey="<?= $config["recaptchaKey"] ?>"
         data-callback="onCompleted"
         data-size="invisible"></div>

    <!-- Submit button -->
    <?= ui_button(__('add_game_submit'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'type' => 'submit', 'attrs' => ['id' => 'form-add-game__submit-btn']]) ?>
  </form>

  <!-- Validate the form submit -->
  <script>
  function onSubmit() {
    document.getElementById("form-add-game__submit-btn").setAttribute("disabled", "");
    //grecaptcha.execute();
    onCompleted();
    return false;
  }

  function onCompleted() {
    document.getElementById("form-add-game").submit();
  }
  </script>


<?php } else { ?>
<div class="internal-page">
  <div class="<?= ui_card_classes(['padding' => 'md']) ?>">
    <?= ui_alert((string) $formError, 'danger') ?>
    <?= ui_button(__('add_game_back'), 'primary', 'md', ['href' => '']) ?>
  </div>
</div>

<?php } ?>

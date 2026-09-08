<?php
define('CONSENT_COOKIE_NAME', 'cookie_consent');
define('CONSENT_COOKIE_DAYS', 182);

function consent_get(): array {
  if (!isset($_COOKIE[CONSENT_COOKIE_NAME])) return ['necessary' => true, 'analytics' => false, 'decided' => false];
  $d = json_decode($_COOKIE[CONSENT_COOKIE_NAME], true);
  if (!is_array($d)) return ['necessary' => true, 'analytics' => false, 'decided' => false];
  return ['necessary' => true, 'analytics' => !empty($d['analytics']), 'decided' => true];
}

function consent_has_analytics(): bool {
  return consent_get()['analytics'] === true;
}

function consent_has_decided(): bool {
  return consent_get()['decided'] === true;
}

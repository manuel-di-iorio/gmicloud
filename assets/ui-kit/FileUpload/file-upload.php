<?php

function ui_file_upload($name, $options = []) {
  $label = $options['label'] ?? '';
  $accept = $options['accept'] ?? '.csv';
  $required = $options['required'] ?? false;
  $disabled = $options['disabled'] ?? false;
  $class = $options['class'] ?? '';
  $hint = $options['hint'] ?? '';
  $error = $options['error'] ?? '';
  $id = $options['id'] ?? $name;
  $placeholder = $options['placeholder'] ?? 'Clicca per selezionare il file';
  $formats = $options['formats'] ?? '';
  $info = $options['info'] ?? '';
  $maxSize = $options['max_size'] ?? '';

  $requiredMark = $required ? ' ds-field__label--required' : '';
  $disabledAttr = $disabled ? ' disabled' : '';
  $idAttr = htmlspecialchars($id);
  $nameAttr = htmlspecialchars($name);

  $html = '<div class="ds-field ' . htmlspecialchars($class) . '">';
  if ($label) $html .= '<label class="ds-field__label' . $requiredMark . '" for="' . $idAttr . '-input">' . htmlspecialchars($label) . '</label>';
  $html .= '<div id="' . $idAttr . '-dropzone" class="ds-file' . ($error ? ' ds-file--error' : '') . '" data-file-upload="' . $idAttr . '">';
  $html .= '<i class="fas fa-cloud-upload-alt" style="font-size:1.875rem;color:var(--ds-color-content-muted);margin-bottom:0.75rem"></i>';
  $html .= '<p style="color:var(--ds-color-content);font-weight:500">' . htmlspecialchars($placeholder) . '</p>';
  $metaParts = [];
  if ($formats) $metaParts[] = htmlspecialchars($formats);
  if ($maxSize) $metaParts[] = htmlspecialchars($maxSize);
  if (count($metaParts) > 0) $html .= '<p class="ds-hint">' . implode(' · ', $metaParts) . '</p>';
  $html .= '</div>';
  $html .= '<input type="file" id="' . $idAttr . '-input" name="' . $nameAttr . '" accept="' . htmlspecialchars($accept) . '" class="hidden" style="display:none"' . $disabledAttr . '>';
  $html .= '<div id="' . $idAttr . '-file-info" class="ds-panel ds-panel--muted hidden" style="display:none;align-items:center;gap:0.75rem;margin-top:0.5rem">';
  $html .= '<i class="fas fa-file" style="color:var(--ds-color-secondary);font-size:1.25rem"></i>';
  $html .= '<div style="flex:1;min-width:0"><p id="' . $idAttr . '-file-name" style="font-weight:500"></p><p id="' . $idAttr . '-file-size" class="ds-hint"></p></div>';
  $html .= '<button type="button" class="ds-muted" style="background:none;border:0;cursor:pointer" onclick="uiFileUploadClear(\'' . $idAttr . '\')"><i class="fas fa-times"></i></button>';
  $html .= '</div>';
  if ($info) $html .= '<div class="ds-hint"><i class="fas fa-info-circle"></i> ' . htmlspecialchars($info) . '</div>';
  if ($hint) $html .= '<div class="ds-hint">' . htmlspecialchars($hint) . '</div>';
  if ($error) $html .= '<div class="ds-error">' . htmlspecialchars($error) . '</div>';
  $html .= '</div>';
  return $html;
}

function ui_file_upload_js() {
  static $loaded = false;
  if ($loaded) return '';
  $loaded = true;
  return '<script>
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll("[data-file-upload]").forEach(function(dropzone) {
    var id = dropzone.getAttribute("data-file-upload");
    var input = document.getElementById(id + "-input");
    if (!input) return;
    ["dragenter", "dragover"].forEach(function(evt) {
      dropzone.addEventListener(evt, function(e) { e.preventDefault(); e.stopPropagation(); dropzone.style.borderColor = "var(--ds-color-secondary)"; });
    });
    ["dragleave", "drop"].forEach(function(evt) {
      dropzone.addEventListener(evt, function(e) { e.preventDefault(); e.stopPropagation(); dropzone.style.borderColor = ""; });
    });
    dropzone.addEventListener("drop", function(e) {
      var files = e.dataTransfer.files;
      if (files && files[0]) { var dt = new DataTransfer(); dt.items.add(files[0]); input.files = dt.files; uiFileUploadShowInfo(id, files[0]); input.dispatchEvent(new Event("change")); }
    });
    dropzone.addEventListener("click", function() { input.click(); });
    input.addEventListener("change", function() { if (input.files && input.files[0]) uiFileUploadShowInfo(id, input.files[0]); });
  });
});
function uiFileUploadShowInfo(id, file) {
  document.getElementById(id + "-dropzone").style.display = "none";
  var info = document.getElementById(id + "-file-info"); info.style.display = "flex";
  document.getElementById(id + "-file-name").textContent = file.name;
  document.getElementById(id + "-file-size").textContent = uiFileUploadFormatSize(file.size);
}
function uiFileUploadClear(id) {
  var input = document.getElementById(id + "-input"); input.value = "";
  document.getElementById(id + "-file-info").style.display = "none";
  document.getElementById(id + "-dropzone").style.display = "block";
  input.dispatchEvent(new Event("change"));
}
function uiFileUploadFormatSize(bytes) {
  if (bytes < 1024) return bytes + " B";
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
  return (bytes / (1024 * 1024)).toFixed(1) + " MB";
}
</script>';
}

<?php
require_once("lib/db.php");

if (!isset($user)) {
  echo "ERROR: Not logged in";
  exit;
}

if (!isset($user["admin"]) || (int)$user["admin"] !== 1) {
  echo "ERROR: Not an admin";
  exit;
}

$localesDir = __DIR__ . '/locales';
$defaultLang = 'en';
$otherLangs = ['it', 'es', 'fr', 'de'];

$enData = json_decode(file_get_contents("$localesDir/$defaultLang.json"), true);
$enKeys = array_keys($enData);
sort($enKeys);

$localeData = [];
foreach ($otherLangs as $lang) {
  $path = "$localesDir/$lang.json";
  if (file_exists($path)) {
    $localeData[$lang] = json_decode(file_get_contents($path), true);
  } else {
    $localeData[$lang] = [];
  }
}

function findUsedKeys($dir) {
  $used = [];
  $extensions = ['php', 'js', 'html'];

  $iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
  );

  foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $ext = $file->getExtension();
    if (!in_array($ext, $extensions)) continue;

    $relativePath = str_replace($dir . DIRECTORY_SEPARATOR, '', $file->getPathname());
    if (strpos($relativePath, '.git') === 0) continue;
    if (strpos($relativePath, 'node_modules') === 0) continue;
    if (strpos($relativePath, 'locales') === 0) continue;

    $content = file_get_contents($file->getPathname());
    if ($content === false) continue;

    if (preg_match_all('/__\([\'"]([a-zA-Z0-9_]+)[\'"]/', $content, $matches)) {
      foreach ($matches[1] as $key) {
        $used[$key] = $relativePath;
      }
    }

    if ($ext === 'js' || $ext === 'php') {
      if (preg_match_all('/_t\.([a-zA-Z0-9_]+)/', $content, $matches)) {
        foreach ($matches[1] as $key) {
          $used[$key] = $relativePath;
        }
      }
    }

    if ($ext === 'php' && preg_match_all('/var\s+_t\s*=\s*\{((?:[^}])*)\}/s', $content, $blockMatches)) {
      foreach ($blockMatches[1] as $block) {
        if (preg_match_all('/([a-zA-Z0-9_]+)\s*:/', $block, $propMatches)) {
          foreach ($propMatches[1] as $key) {
            $used[$key] = $relativePath;
          }
        }
      }
    }

    if ($ext === 'js' && preg_match_all('/(?:var|let|const|,)\s+_t\s*=\s*\{((?:[^}])*)\}/s', $content, $blockMatches)) {
        foreach ($blockMatches[1] as $block) {
          if (preg_match_all('/([a-zA-Z0-9_]+)\s*:/', $block, $propMatches)) {
            foreach ($propMatches[1] as $key) {
              $used[$key] = $relativePath;
            }
          }
        }
      }
  }
  return $used;
}

$usedKeys = findUsedKeys(__DIR__);
$unusedKeys = array_diff($enKeys, array_keys($usedKeys));
sort($unusedKeys);

$results = [];
foreach ($enKeys as $key) {
  $row = ['key' => $key, 'locales' => [], 'unused' => in_array($key, $unusedKeys)];
  foreach ($otherLangs as $lang) {
    $row['locales'][$lang] = isset($localeData[$lang][$key]);
  }
  $results[] = $row;
}

$orphanKeys = [];
foreach ($otherLangs as $lang) {
  foreach (array_keys($localeData[$lang] ?? []) as $key) {
    if (!in_array($key, $enKeys)) {
      $orphanKeys[$lang][] = $key;
    }
  }
}
foreach ($orphanKeys as &$keys) { sort($keys); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Controllo Locales - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="m-0 bg-neutral-100 p-5 text-neutral-800">
  <div class="mx-auto max-w-[1400px]">
    <h1 class="mb-5 text-3xl font-bold text-neutral-900">Controllo Locales</h1>

    <div class="mb-5 flex flex-wrap gap-5">
      <div class="rounded-lg bg-white px-6 py-4 shadow-sm">
        <h3 class="mb-1 text-sm text-neutral-500">Totale chiavi (EN)</h3>
        <div class="text-[28px] font-bold"><?= count($enKeys) ?></div>
      </div>
      <div class="rounded-lg bg-white px-6 py-4 shadow-sm">
        <h3 class="mb-1 text-sm text-neutral-500">Chiavi mancanti</h3>
        <?php
        $totalMissing = 0;
        foreach ($otherLangs as $lang) {
          $totalMissing += count(array_diff($enKeys, array_keys($localeData[$lang] ?? [])));
        }
        ?>
        <div class="text-[28px] font-bold <?= $totalMissing > 0 ? 'text-red-600' : 'text-green-600' ?>"><?= $totalMissing ?></div>
      </div>
      <div class="rounded-lg bg-white px-6 py-4 shadow-sm">
        <h3 class="mb-1 text-sm text-neutral-500">Chiavi orfane</h3>
        <?php $totalOrphan = array_sum(array_map('count', $orphanKeys)); ?>
        <div class="text-[28px] font-bold <?= $totalOrphan > 0 ? 'text-orange-600' : 'text-green-600' ?>"><?= $totalOrphan ?></div>
      </div>
      <div class="rounded-lg bg-white px-6 py-4 shadow-sm">
        <h3 class="mb-1 text-sm text-neutral-500">Chiavi inutilizzate</h3>
        <div class="text-[28px] font-bold <?= count($unusedKeys) > 0 ? 'text-gray-500' : 'text-green-600' ?>"><?= count($unusedKeys) ?></div>
      </div>
    </div>

    <div class="mb-5 flex flex-wrap gap-6 rounded-lg bg-white p-4 text-sm shadow-sm">
      <div class="flex items-center gap-1.5"><span class="w-5 text-center font-bold text-green-600">✓</span> Presente</div>
      <div class="flex items-center gap-1.5"><span class="status-missing w-5 text-center font-bold text-red-600">✗</span> Mancante (in EN ma non nel locale)</div>
      <div class="flex items-center gap-1.5"><span class="w-5 text-center font-bold text-orange-600">⚠</span> Orfana (nel locale ma non in EN)</div>
      <div class="flex items-center gap-1.5"><span class="status-unused w-5 text-center italic text-gray-400">●</span> Inutilizzata (non usata in codice)</div>
    </div>

    <div class="mb-5 flex flex-wrap items-center gap-3 rounded-lg bg-white p-4 shadow-sm">
      <label class="text-sm text-neutral-500">Filtro:</label>
      <select id="filterType" class="rounded-md border border-solid border-neutral-300 px-3 py-2 text-sm">
        <option value="all">Tutte</option>
        <option value="missing">Mancanti</option>
        <option value="orphan">Orfane</option>
        <option value="unused">Inutilizzate</option>
      </select>
      <input type="text" id="filterSearch" placeholder="Cerca chiave..." class="w-[250px] rounded-md border border-solid border-neutral-300 px-3 py-2 text-sm">
      <button id="exportCsv" class="ml-auto cursor-pointer rounded-md border-0 bg-blue-500 px-4 py-2 text-sm text-white hover:bg-blue-600">Esporta CSV</button>
    </div>

    <table class="w-full overflow-hidden rounded-lg bg-white shadow-sm">
      <thead>
        <tr>
          <th class="sticky top-0 border-b-2 border-slate-200 bg-slate-50 px-4 py-3 text-left text-[13px] font-semibold text-slate-600">Chiave</th>
          <?php foreach ($otherLangs as $lang): ?>
            <th class="sticky top-0 border-b-2 border-slate-200 bg-slate-50 px-4 py-3 text-left text-[13px] font-semibold text-slate-600"><?= strtoupper($lang) ?></th>
          <?php endforeach; ?>
          <th class="sticky top-0 border-b-2 border-slate-200 bg-slate-50 px-4 py-3 text-left text-[13px] font-semibold text-slate-600">Stato</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $row):
          $hasMissing = in_array(false, $row['locales']);
          if (!$row['unused'] && !$hasMissing) continue;
          $rowClass = '';
          if ($row['unused']) $rowClass = 'bg-gray-50';
          elseif ($hasMissing) $rowClass = 'bg-red-50';
        ?>
        <tr class="<?= $rowClass ?> hover:bg-slate-50" data-key="<?= htmlspecialchars($row['key']) ?>">
          <td class="border-b border-slate-100 px-4 py-2.5 text-sm"><code><?= htmlspecialchars($row['key']) ?></code></td>
          <?php foreach ($otherLangs as $lang): ?>
            <td class="border-b border-slate-100 px-4 py-2.5 text-sm">
              <?php if ($row['locales'][$lang]): ?>
                <span class="font-semibold text-green-600">✓</span>
              <?php else: ?>
                <span class="status-missing font-semibold text-red-600">✗</span>
              <?php endif; ?>
            </td>
          <?php endforeach; ?>
          <td class="border-b border-slate-100 px-4 py-2.5 text-sm">
            <?php if ($row['unused']): ?>
              <span class="status-unused italic text-gray-400">● Inutilizzata</span>
            <?php else: ?>
              <span class="font-semibold text-green-600">✓ Usata</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?php
    $problemCount = 0;
    foreach ($results as $row) {
      $hasMissing = in_array(false, $row['locales']);
      if ($row['unused'] || $hasMissing) $problemCount++;
    }
    ?>
    <?php if ($problemCount === 0): ?>
    <p class="py-8 text-center italic text-gray-500">Nessun problema trovato. Tutte le chiavi sono complete e utilizzate.</p>
    <?php endif; ?>

    <?php if (!empty($orphanKeys)): ?>
    <div class="mt-[30px]">
      <h2 class="mb-4 text-xl font-bold text-neutral-900">Chiavi orfane (presenti nei locale ma non in EN)</h2>
      <?php foreach ($orphanKeys as $lang => $keys): ?>
        <div class="mb-5">
          <h3 class="mb-2 text-base font-semibold text-slate-600"><?= strtoupper($lang) ?> (<?= count($keys) ?> chiavi)</h3>
          <div class="rounded-lg bg-white p-4 shadow-sm">
            <?php foreach ($keys as $key): ?>
              <code class="mb-0.5 mr-1 inline-block rounded bg-red-50 px-2 py-0.5 text-[13px] text-red-600"><?= htmlspecialchars($key) ?></code>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($unusedKeys)): ?>
    <div class="mt-[30px]">
      <h2 class="mb-4 text-xl font-bold text-neutral-900">Chiavi inutilizzate (<?= count($unusedKeys) ?>)</h2>
      <div class="rounded-lg bg-white p-4 shadow-sm">
        <?php foreach ($unusedKeys as $key): ?>
          <code class="mb-0.5 mr-1 inline-block rounded bg-red-50 px-2 py-0.5 text-[13px] text-red-600"><?= htmlspecialchars($key) ?></code>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <script>
    const filterType = document.getElementById('filterType');
    const filterSearch = document.getElementById('filterSearch');
    const rows = document.querySelectorAll('tbody tr');

    function applyFilters() {
      const type = filterType.value;
      const search = filterSearch.value.toLowerCase();

      rows.forEach(row => {
        const key = row.dataset.key;
        const hasMissing = row.querySelector('.status-missing') !== null;
        const isUnused = row.querySelector('.status-unused') !== null;

        let showByType = true;
        if (type === 'missing') showByType = hasMissing;
        else if (type === 'orphan') showByType = false;
        else if (type === 'unused') showByType = isUnused;
        else if (type === 'ok') showByType = !hasMissing && !isUnused;

        const showBySearch = !search || key.toLowerCase().includes(search);

        row.style.display = (showByType && showBySearch) ? '' : 'none';
      });
    }

    filterType.addEventListener('change', applyFilters);
    filterSearch.addEventListener('input', applyFilters);

    document.getElementById('exportCsv').addEventListener('click', function() {
      const rows = document.querySelectorAll('tbody tr');
      const csv = [];
      csv.push(['Chiave', 'IT', 'ES', 'FR', 'DE', 'Problema'].join(','));

      rows.forEach(row => {
        if (row.style.display === 'none') return;
        const hasMissing = row.querySelector('.status-missing') !== null;
        const isUnused = row.querySelector('.status-unused') !== null;
        if (!hasMissing && !isUnused) return;

        const key = row.dataset.key;
        const cells = row.querySelectorAll('td');
        const problems = [];
        if (hasMissing) {
          for (let i = 1; i < cells.length - 1; i++) {
            if (cells[i].querySelector('.status-missing')) {
              problems.push('mancante in ' + ['IT','ES','FR','DE'][i-1]);
            }
          }
        }
        if (isUnused) problems.push('inutilizzata');

        const vals = [];
        for (let i = 1; i < cells.length - 1; i++) {
          vals.push(cells[i].querySelector('.status-ok') ? 'SI' : 'NO');
        }
        csv.push(['"' + key.replace(/"/g, '""') + '"', ...vals, '"' + problems.join('; ').replace(/"/g, '""') + '"'].join(','));
      });

      const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = 'locales-report.csv';
      link.click();
    });
  </script>
</body>
</html>

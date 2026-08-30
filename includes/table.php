<?php

/**
 * Generates a generic HTML table with sorting, pagination, and actions.
 *
 * @param array $data An array of associative arrays representing the table rows.
 * @param array $columns An array of associative arrays defining the table columns.
 *                      Each column array should have:
 *                      - "label": The display name for the column header.
 *                      - "key": The key to access the data in each row.
 *                      - "sortable": (Optional) Boolean, true if the column is sortable. Defaults to false.
 *                      - "format_callback": (Optional) A callback function to format the cell value.
 *                                           It receives the value and the full row data as arguments.
 * @param array $actions An array of associative arrays defining actions for each row.
 *                       Each action array should have:
 *                       - "label": The tooltip or accessible label for the action.
 *                       - "icon": The Font Awesome icon class (e.g., "fas fa-edit").
 *                       - "url": The base URL for the action. Row ID will be appended as a query parameter `id`.
 *                                Or, can contain placeholders like {id} to be replaced with row's primary key.
 *                       - "class": (Optional) CSS class for the action button.
 *                       - "condition_callback": (Optional) A callback function that receives the row data
 *                                               and returns true if the action should be displayed for that row.
 * @param array $options An associative array for table options:
 *                       - "table_id": (Optional) The ID attribute for the table element.
 *                       - "table_class": (Optional) CSS classes for the table element. Defaults to "ui-table".
 *                       - "default_sort_column": (Optional) The key of the column to sort by default.
 *                       - "default_sort_direction": (Optional) "asc" or "desc". Defaults to "asc".
 *                       - "pagination": (Optional) An associative array for pagination settings:
 *                         - "current_page": The current page number.
 *                         - "items_per_page": Number of items to display per page.
 *                         - "total_items": The total number of items.
 *                       - "base_url": (Optional) The base URL for sorting and pagination links. Defaults to current page.
 *                       - "primary_key": (Optional) The key in the $data array that serves as the primary key for rows (e.g., "id"). Defaults to "id".
 *
 * @return void Outputs the HTML for the table directly.
 */
function render_table(array $data, array $columns, array $actions = [], array $options = []): void
{
    // Default options
    $tableId = $options["table_id"] ?? 'genericTable' . rand(1000, 9999);
     $tableClass = $options["table_class"] ?? 'ui-table';
    $defaultSortColumn = $options["default_sort_column"] ?? null;
    $defaultSortDirection = $options["default_sort_direction"] ?? 'asc';
    $paginationSettings = $options["pagination"] ?? null;
    $baseUrl = $options["base_url"] ?? '';
    $primaryKey = $options["primary_key"] ?? 'id';
    $selectable = $options["selectable"] ?? false;

    // Pagination is always 0-indexed

    // Sorting parameters from GET request or defaults
    $sortColumn = $_GET['sort'] ?? $defaultSortColumn;
    $sortDirection = $_GET['dir'] ?? $defaultSortDirection;

    // --- Data Sorting (if a sort column is specified) ---
    if ($sortColumn !== null && !empty($data)) {
        usort($data, function ($a, $b) use ($sortColumn, $sortDirection) {
            $valA = $a[$sortColumn] ?? null;
            $valB = $b[$sortColumn] ?? null;

            if ($valA === $valB) {
                return 0; // Se i valori sono identici, non c'è bisogno di ordinare
            }

            // Gestione specifica per valori null: i null vanno alla fine per ascendente, all'inizio per discendente
            if ($valA === null) {
                return ($sortDirection === 'asc') ? 1 : -1;
            }
            if ($valB === null) {
                return ($sortDirection === 'asc') ? -1 : 1;
            }

            if (is_numeric($valA) && is_numeric($valB)) {
                // Confronto numerico
                return ($sortDirection === 'asc') ? ($valA - $valB) : ($valB - $valA);
            } else {
                // Confronto come stringhe (case-sensitive, standard per la maggior parte dei casi)
                $sValA = strval($valA);
                $sValB = strval($valB);
                return ($sortDirection === 'asc') ? strcmp($sValA, $sValB) : strcmp($sValB, $sValA);
            }
        });
    }

    // --- Pagination Logic ---
    $pageData = $data;
    $totalPages = 0; // Default for 0-indexed
    $currentPage = 0; // Default for 0-indexed

    if ($paginationSettings && !empty($data)) {
        $itemsPerPage = (int) ($paginationSettings["items_per_page"] ?? 10);
        $totalItems = (int) ($paginationSettings["total_items"] ?? count($data)); 
        
        // Adjust current page from GET, defaulting to 0 for 0-indexed pagination
        $currentPage = (int) ($_GET['page'] ?? $paginationSettings["current_page"] ?? 0);

        // 0-indexed pagination logic
        if ($currentPage < 0) $currentPage = 0;
        $totalPages = $itemsPerPage > 0 ? ceil($totalItems / $itemsPerPage) -1 : 0;
        if ($totalPages < 0) $totalPages = 0; // Ensure totalPages is not negative
        if ($currentPage > $totalPages && $totalItems > 0) $currentPage = $totalPages;
        $offset = $currentPage * $itemsPerPage;
        

        if (!isset($paginationSettings["total_items"])) {
            $pageData = array_slice($data, $offset, $itemsPerPage);
        } else {
            // If total_items is set, it implies $data is already the correct slice for the current page.
            // However, we still need to respect items_per_page for display consistency if $data has more items.
            if (count($data) > $itemsPerPage) {
                $pageData = array_slice($data, 0, $itemsPerPage);
            } else {
                $pageData = $data;
            }
        }
    }

    // --- Start Table Output ---
    echo '<div class="ui-table-container">';
    echo '<table id="' . $tableId . '" class="' . $tableClass . '">';

    // --- Table Header ---
    echo '<thead class="ui-table-header"><tr>';
    if ($selectable) {
        echo '<th class="ui-table-header-cell w-10 align-middle"><input type="checkbox" class="align-middle" onclick="toggleSelectAll(this, \'selected_ids[]\')"></th>';
    }
    foreach ($columns as $column) {
        echo '<th class="ui-table-header-cell">';
        if ($column["sortable"] ?? false) {
            $newDirection = ($sortColumn === $column["key"] && $sortDirection === 'asc') ? 'desc' : 'asc';
            
            $urlParts = parse_url($baseUrl);
            $path = $urlParts['path'] ?? '';
            $queryParams = [];
            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $queryParams);
            }

            $mergedQueryParams = array_merge($_GET, $queryParams); 
            $mergedQueryParams['sort'] = $column["key"];
            $mergedQueryParams['dir'] = $newDirection;
            // Reset to page 0 when sorting for 0-indexed pagination
            $mergedQueryParams['page'] = 0; 

            if (isset($queryParams['id']) && !empty($queryParams['id'])) {
                 $mergedQueryParams['id'] = $queryParams['id']; 
            }

            $sortUrl = $path . '?' . http_build_query($mergedQueryParams);
            
            echo '<a href="' . htmlspecialchars($sortUrl) . '">';
            echo htmlspecialchars($column["label"]); // Ensure label is also escaped
            if ($sortColumn === $column["key"]) {
                echo ' <i class="fas fa-sort-' . ($sortDirection === 'asc' ? 'up' : 'down') . '"></i>';
            }
            echo '</a>';
        } else {
            echo htmlspecialchars($column["label"]);
        }
        echo '</th>';
    }
    if (!empty($actions)) {
        echo '<th class="ui-table-header-cell actions-header-cell">' . __('table_actions') . '</th>';
    }
    echo '</tr></thead>';

    // --- Table Body ---
    echo '<tbody class="ui-table-body">';
    if (empty($pageData)) {
        $colspan = count($columns) + (!empty($actions) ? 1 : 0) + ($selectable ? 1 : 0);
        echo '<tr class="ui-table-empty-row"><td colspan="' . $colspan . '" class="text-center">' . __('table_empty') . '</td></tr>';
    } else {
        foreach ($pageData as $row) {
            echo '<tr class="ui-table-row">';
            if ($selectable) {
                $pkValue = $row[$primaryKey] ?? '';
                echo '<td class="ui-table-cell w-10 align-middle"><input type="checkbox" name="selected_ids[]" value="' . htmlspecialchars($pkValue) . '" class="align-middle"></td>';
            }
            foreach ($columns as $column) {
                $cellValue = $row[$column["key"]] ?? '';
                if (isset($column["format_callback"]) && is_callable($column["format_callback"])) {
                    $cellValue = call_user_func($column["format_callback"], $cellValue, $row);
                }
                echo '<td class="ui-table-cell">' . $cellValue . '</td>';
            }

            // Actions column
            if (!empty($actions)) {
                echo '<td class="ui-table-cell actions-cell">';
                foreach ($actions as $action) {
                    $showAction = true;
                    if (isset($action["condition_callback"]) && is_callable($action["condition_callback"])) {
                        $showAction = call_user_func($action["condition_callback"], $row);
                    }

                    if ($showAction) {
                        $actionClass = $action["class"] ?? 'table-action-icon';
                        if ($actionClass === 'btn-link') {
                            $actionClass = 'mr-1 inline-block rounded px-1.5 py-1 text-sm text-table-action-icon no-underline transition hover:scale-110 hover:bg-table-action-icon-hover-bg hover:text-primary-color';
                        }

                        // Handle URL with placeholders
                        if (is_callable($action["url"])) {
                            $actionUrl = call_user_func($action["url"], $row);
                        } else {
                            $actionUrl = $action["url"];
                        }

                        // Replace placeholders in the onClick action
                        $linkAttributes = '';
                        if (isset($action["onclick"])) {
                            $onclickValue = call_user_func($action["onclick"], $row);
                            $linkAttributes .= ' onclick="' . $onclickValue . '"';
                        }

                        // Render the action button
                        echo '<a href="' . htmlspecialchars($actionUrl) . '" data-tippy-content="' . htmlspecialchars($action["label"]) . '" class="' . htmlspecialchars($actionClass) . '"' . $linkAttributes . '>';
                        echo '<i class="' . htmlspecialchars($action["icon"]) . '"></i>';
                        echo '</a> ';
                    }
                }
                echo '</td>';
            }
            echo '</tr>';
        }
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>'; // End ui-table-container

    // --- Pagination Controls ---
    // Show pagination controls only if there is more than one page (totalPages > 0 for 0-indexed)
    if ($paginationSettings && $totalPages > 0) { 
        $paginationButtonClasses = 'inline-flex min-w-9 select-none items-center justify-center rounded-md border border-solid border-[var(--border-color)] bg-input-bg px-3 py-2 text-[0.9rem] text-text no-underline transition-colors hover:border-primary-color hover:bg-pagination-hover-bg';
        $paginationActiveClasses = 'cursor-default !border-primary-color !bg-pagination-active-bg font-semibold !text-pagination-active-text';
        $paginationDisabledClasses = 'cursor-not-allowed !border-transparent !bg-transparent !text-pagination-disabled-text opacity-60';

        echo '<div class="ui-table-pagination mt-4 text-center">';
        echo '<div class="inline-flex flex-wrap items-center gap-1">';

        // Previous button (for 0-indexed)
        if ($currentPage > 0) {
            $prevPageParams = http_build_query(array_merge($_GET, ['page' => $currentPage - 1]));
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $prevPageParams) . '" class="' . $paginationButtonClasses . '">&laquo; ' . __('table_prev') . '</a>';
        }

        // Page numbers (for 0-indexed)
        $numPageLinksToShow = 5; 
        $startPage = 0;
        $endPage = $totalPages;

        if ($totalPages + 1 > $numPageLinksToShow) { // +1 because totalPages is 0-indexed
            $halfLinks = floor($numPageLinksToShow / 2);
            $startPage = $currentPage - $halfLinks;
            $endPage = $currentPage + $halfLinks;

            if ($startPage < 0) {
                $endPage -= $startPage; 
                $startPage = 0;
            }
            if ($endPage > $totalPages) {
                $startPage -= ($endPage - $totalPages); 
                $endPage = $totalPages;
            }
            if ($startPage < 0) $startPage = 0; 
        }
        
        // Ellipsis for first page if needed (for 0-indexed)
        if ($startPage > 0) {
            $firstPageParams = http_build_query(array_merge($_GET, ['page' => 0]));
              echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $firstPageParams) . '" class="' . $paginationButtonClasses . '">0</a>';
            if ($startPage > 1) { 
                  echo '<span class="' . $paginationButtonClasses . ' ' . $paginationDisabledClasses . '">...</span>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $pageParams = http_build_query(array_merge($_GET, ['page' => $i]));
            if ($i == $currentPage) {
                echo '<button class="' . $paginationButtonClasses . ' ' . $paginationActiveClasses . '"> ' . $i . '</button>';
            } else {
                echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $pageParams) . '" class="' . $paginationButtonClasses . '">' . $i . '</a>';
            }
        }
        
        // Ellipsis for last page if needed (for 0-indexed)
        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1 ) { 
                echo '<span class="' . $paginationButtonClasses . ' ' . $paginationDisabledClasses . '">...</span>';
            }
            $lastPageParams = http_build_query(array_merge($_GET, ['page' => $totalPages]));
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $lastPageParams) . '" class="' . $paginationButtonClasses . '">' . $totalPages . '</a>';
        }

        // Next button (for 0-indexed)
        if ($currentPage < $totalPages) {
            $nextPageParams = http_build_query(array_merge($_GET, ['page' => $currentPage + 1]));
            echo '<a href="' . htmlspecialchars($baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $nextPageParams) . '" class="' . $paginationButtonClasses . '">' . __('table_next') . ' &raquo;</a>';
        }

        echo '</div>';
        echo '</div>';
    }
    echo '</div>'; // Close ui-table-container
}

?>
<script>
function toggleSelectAll(source, name) {
  var checkboxes = document.getElementsByName(name);
  for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].checked = source.checked;
  }
  if (typeof updateDeleteSelectedButton === 'function') {
    updateDeleteSelectedButton();
  }
}
</script>

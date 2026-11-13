<?php

function ui_accordion(array $items, array $options = []): string {
    $class = $options['class'] ?? '';
    $openIndex = $options['open'] ?? null;

    $html = '<div class="' . htmlspecialchars(trim('ds-faq ' . $class), ENT_QUOTES, 'UTF-8') . '">';

    foreach ($items as $i => $item) {
        $title = $item['title'] ?? $item['label'] ?? null;
        $header = $item['header'] ?? null;
        $content = $item['content'] ?? '';
        $isOpen = ($openIndex !== null && (int) $openIndex === $i);

        $html .= '<div class="ds-faq__item' . ($isOpen ? ' is-open' : '') . '">';
        $html .= '<button class="ds-faq__q" type="button" aria-expanded="' . ($isOpen ? 'true' : 'false') . '">';
        if ($header !== null) {
            $html .= $header;
        } elseif ($title !== null) {
            $html .= '<span>' . htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') . '</span>';
        }
        $html .= '<span class="ds-faq__icon"><i class="fas fa-chevron-down"></i></span>';
        $html .= '</button>';
        $html .= '<div class="ds-faq__a">';
        $html .= '<div class="ds-faq__a-inner">' . $content . '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}

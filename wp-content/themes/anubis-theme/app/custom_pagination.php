<?php

use Roots\asset;

function custom_pagination($query = null) {

    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }

    $total_pages = $query->max_num_pages;
    if ($total_pages <= 1) return;

    $current = max(1, get_query_var('paged'));
    $range   = 2;
    $step    = 20;

    $arrow = '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M10.6667 2.66666L24 16L10.6667 29.3333L8.3 26.9667L19.2667 16L8.3 5.03333L10.6667 2.66666Z" fill="#EBEBEB"/>
</svg>';


    echo '<nav class="custom-pagination"><ul>';

    /** ◀️ flèche gauche */
    if ($current > 1) {
        $prev = max(1, $current - $step);
        echo '<li class="arrow prev">
                <a href="' . esc_url(get_pagenum_link($prev)) . '" aria-label="Pages précédentes">' .
                    $arrow
                . '
                </a>
              </li>';
    }

    /** Page 1 */
    echo '<li' . ($current === 1 ? ' class="active"' : '') . '>
            <a href="' . esc_url(get_pagenum_link(1)) . '">1</a>
          </li>';

    if ($current > ($range + 2)) {
        echo '<li class="dots">…</li>';
    }

    for ($i = max(2, $current - $range); $i <= min($total_pages - 1, $current + $range); $i++) {
        echo '<li' . ($current === $i ? ' class="active"' : '') . '>
                <a href="' . esc_url(get_pagenum_link($i)) . '">' . $i . '</a>
              </li>';
    }

    if ($current < ($total_pages - $range - 1)) {
        echo '<li class="dots">…</li>';
    }

    echo '<li' . ($current === $total_pages ? ' class="active"' : '') . '>
            <a href="' . esc_url(get_pagenum_link($total_pages)) . '">' . $total_pages . '</a>
          </li>';

    /** ▶️ flèche droite */
    if ($current < $total_pages) {
        $next = min($total_pages, $current + $step);
        echo '<li class="arrow next">
                <a href="' . esc_url(get_pagenum_link($next)) . '" aria-label="Pages suivantes">' .
                    $arrow
                . '</a>
              </li>';
    }

    echo '</ul></nav>';
}

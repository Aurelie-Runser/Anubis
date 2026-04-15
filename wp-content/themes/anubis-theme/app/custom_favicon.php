<?php

add_action('wp_head', function () {
    echo '<script>
        window.__isLoggedIn = ' . (is_user_logged_in() ? 'true' : 'false') . ';
    </script>';
});
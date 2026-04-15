<?php

add_action('wp_head', function () {

    if (!is_user_logged_in()) return;

    echo '<script>
        window.__isLoggedIn = true;
    </script>';
});
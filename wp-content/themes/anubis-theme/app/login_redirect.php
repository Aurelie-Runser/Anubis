<?php

add_filter('login_redirect', function ($redirect_to, $requested_redirect_to, $user) {
    if ($user instanceof WP_User) {
        return home_url('/profil');
    }
    return $redirect_to;
}, 10, 3);

add_action('template_redirect', function () {

    // Si l'utilisateur est connecté avec le compte du directeur
    $current_user = wp_get_current_user();
    if (is_user_logged_in() && in_array('directeur', (array) $current_user->roles)) {

        if (!is_page('fais-attention')) {
            wp_safe_redirect(site_url('/fais-attention'));
            exit;
        }

        return;
    }

    // Si l'utilisateur est connecté → on laisse tout passer
    if (is_user_logged_in()) {
        return;
    }

    // Autoriser uniquement les pages WordPress classiques
    if (is_page() && is_page_template('default')) {
        return;
    }

    // Autoriser la page de connexion
    if (is_admin() || strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        return;
    }

    // Sinon → redirection vers l'accueil
    wp_redirect(home_url());
    exit;
});


// Si connecter en tant que directeur, suppression rapide des cookies pour se deconnecté si quitter le site
add_filter('auth_cookie_expiration', function ($expire, $user_id, $remember) {

    $user = get_userdata($user_id);

    if (in_array('directeur', (array) $user->roles)) {
        return 5; // 5 secondes
    }

    return $expire;

}, 10, 3);
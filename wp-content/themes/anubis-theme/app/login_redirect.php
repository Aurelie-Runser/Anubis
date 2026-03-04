<?php

add_filter('login_redirect', function ($redirect_to, $requested_redirect_to, $user) {
    if ($user instanceof WP_User) {
        return home_url('/profil');
    }
    return $redirect_to;
}, 10, 3);

add_action('template_redirect', function () {

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
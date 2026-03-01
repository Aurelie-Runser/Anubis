<?php
/**
 * Gestion centralisée des rôles passifs
 * Tous les rôles définis ici n'ont AUCUN droit backoffice
 */

/**
 * 🔐 LISTE GLOBALE DES RÔLES PASSIFS
 * ➜ Ajouter ici tous les rôles custom
 */
define('ROLES_DEFAULT', [
    'administrator',
    'editor',
]);

define('ROLES_PASSIVE', [
    'agent',
    'directeur'
]);

define('ROLES_DELETE', [
    'subscriber', 'contributor', 'author'
]);

/**
 * 1️⃣ Création des rôles personnalisés
 * - Tous les rôles sont volontairement "vides"
 * - Seule la capability `read` est nécessaire pour permettre la connexion
 */
function custom_roles_init() {

    add_role(
        'agent',
        'Agent',
        [
            'read' => true, // obligatoire pour être connecté
        ]
    );

}
add_action('init', 'custom_roles_init');

/**
 * 2️⃣ Désactivation des capacités des rôles WordPress par défaut
 * - On ne supprime PAS les rôles (compatibilité plugins)
 * - On enlève simplement toutes leurs permissions
 */
function disable_default_roles_capabilities() {

    foreach (ROLES_DELETE as $role_slug) {

        $role = get_role($role_slug);

        if (!$role) {
            continue;
        }

        foreach ($role->capabilities as $cap => $value) {
            $role->remove_cap($cap);
        }
    }
}
add_action('init', 'disable_default_roles_capabilities');

/**
 * 3️⃣ Masquer les rôles par défaut dans l’admin
 * - Empêche leur sélection lors de la création d’utilisateurs
 * - UX propre et sans ambiguïté
 */
function hide_default_roles_from_dropdown($roles) {

    foreach (ROLES_DELETE as $role) {
        unset($roles[$role]);
    }

    return $roles;
}
add_filter('editable_roles', 'hide_default_roles_from_dropdown');

/**
 * 4️⃣ Blocage total de l’accès au backoffice (/wp-admin)
 * - Sauf AJAX (nécessaire pour certaines features front)
 */
function roles_block_admin_for_passive() {

    if (!is_admin() || defined('DOING_AJAX')) {
        return;
    }

    $user = wp_get_current_user();

    if (array_intersect(ROLES_PASSIVE, $user->roles)) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('admin_init', 'roles_block_admin_for_passive');

/**
 * 5️⃣ Désactivation de l’API REST pour les rôles passifs
 * - Empêche toute interaction via JS / apps / endpoints
 */
add_filter('rest_authentication_errors', function ($result) {

    if (!is_user_logged_in()) {
        return $result;
    }

    $user = wp_get_current_user();

    if (array_intersect(ROLES_PASSIVE, $user->roles)) {
        return new WP_Error(
            'rest_forbidden',
            'REST API disabled for this role',
            ['status' => 403]
        );
    }

    return $result;
});

/**
 * 6️⃣ Hardening basique (optionnel mais recommandé)
 */
remove_action('wp_head', 'wp_generator');
add_filter('xmlrpc_enabled', '__return_false');


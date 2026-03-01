<?php

function is_user_allowed_for_content($post_id) {

    // Admins / Editors passent toujours
    if (current_user_can('edit_others_posts') || current_user_can('manage_options')) {
        return true;
    }

    // Utilisateur non connecté est refusé
    if (!is_user_logged_in()) {
        return false;
    }

    $user = wp_get_current_user();
    $user_roles = (array) $user->roles;

    // 3. Rôles WordPress natifs → accès autorisé
    if (!empty(array_intersect($user_roles, ROLES_DEFAULT))) {
        return true;
    }

    // 4. Rôles définis dans le CPT
    $roles_allowed = get_post_meta($post_id, '_roles_allowed', true);

    // Aucun rôle défini → accès libre
    if (empty($roles_allowed) || !is_array($roles_allowed)) {
        return true;
    }

    // Sécurité : seulement des rôles autorisés globalement
    if (defined('ROLES_PASSIVE')) {
        $roles_allowed = array_intersect($roles_allowed, ROLES_PASSIVE);
    }

    // 5. Match entre rôles utilisateur et rôles CPT
    return !empty(array_intersect($user_roles, $roles_allowed));
}
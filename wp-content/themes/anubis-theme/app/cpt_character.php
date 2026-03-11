<?php

function cpt__character()
{

    $labels = array(
        'name'                => _x('Personnages', 'Post Type General Name'),
        'singular_name'       => _x('Personnage', 'Post Type Singular Name'),
        'menu_name'           => __('Personnages'),
        'all_items'           => __('Tous les Personnages'),
        'view_item'           => __('Voir les Personnages'),
        'add_new_item'        => __('Ajouter un nouveau Personnage'),
        'add_new'             => __('Ajouter'),
        'edit_item'           => __('Editer le Personnage'),
        'update_item'         => __('Modifier le Personnage'),
        'search_items'        => __('Rechercher un Personnage'),
        'not_found'           => __('Non trouvé'),
        'not_found_in_trash'  => __('Non trouvé dans la corbeille'),
        'view_items'          => __('Voir la liste des Personnage'),
        'featured_image'        => 'Photo d\'identité',
        'set_featured_image'    => 'Définir la photo d\'identité',
        'remove_featured_image' => 'Supprimer la photo d\'identité',
        'use_featured_image'    => 'Utiliser comme photo d\'identité',
    );

    $args = array(
        'label'               => __('Personnage'),
        'description'         => __('Tous sur Personnage'),
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail'),
        'show_in_rest'        => true,
        'hierarchical'        => false,
        'public'              => true,
        'has_archive'         => false,
        'rewrite'              => array('slug' => 'membre'),
        'menu_icon'           => 'dashicons-groups',
        'capability_type'     => 'character',
        'map_meta_cap'        => true,
    );

    register_post_type('character', $args);
}
add_action('init', 'cpt__character', 0);

add_filter('enter_title_here', 'change_character_title_placeholder');

function change_character_title_placeholder($title)
{
    $screen = get_current_screen();

    if ($screen->post_type === 'character') {
        $title = 'Prénom Nom';
    }

    return $title;
}

/**
 * Metabox complète pour le CPT "Personnage"
 */

function add_metabox_character()
{
    add_meta_box('character_meta', 'Informations sur le Personnage.', 'show_metabox_character', 'character');
}
add_action('add_meta_boxes', 'add_metabox_character');

function show_metabox_character($post)
{

    $linked_user = get_post_meta($post->ID, '_linked_user', true);

    // Récupérer tous les user IDs déjà associés à un character
    $assigned_users = get_posts([
        'post_type'      => 'character',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'meta_key'       => '_linked_user',
        'meta_value'     => '',      // On ne veut pas filtrer ici
        'fields'         => 'ids',   // on récupère juste les IDs des posts
    ]);

    $used_user_ids = [];
    if (!empty($assigned_users)) {
        foreach ($assigned_users as $post_id) {
            $uid = get_post_meta($post_id, '_linked_user', true);
            if ($uid && $uid != $linked_user) { // on exclut le user déjà assigné
                $used_user_ids[] = $uid;
            }
        }
    }

    // Récupérer les utilisateurs non utilisés
    $users = get_users([
        'orderby'        => 'display_name',
        'order'          => 'ASC',
        'role__not_in'   => ['administrator', 'editor'],
        'exclude'        => $used_user_ids,
    ]);

    $id = get_post_meta($post->ID, '_id', true);
    $role   = get_post_meta($post->ID, '_role', true);

    $roles_allowed = get_post_meta($post->ID, '_roles_allowed', true);
    $roles_allowed = is_array($roles_allowed) ? $roles_allowed : [];

    $date_birthday    = get_post_meta($post->ID, '_date_birthday', true);
    $residence_primary    = get_post_meta($post->ID, '_residence_primary', true);
    $residence_secondary    = get_post_meta($post->ID, '_residence_secondary', true);
    $gender   = get_post_meta($post->ID, '_gender', true);
    $nationality   = get_post_meta($post->ID, '_nationality', true);

    $date_recruitment    = get_post_meta($post->ID, '_date_recruitment', true);
    $affiliated_ubsidiary = get_post_meta($post->ID, '_affiliated_ubsidiary', true);

    $pathologies = get_post_meta($post->ID, '_pathologies', true);
    $relatives = get_post_meta($post->ID, '_relatives', true);

    wp_nonce_field('save_character_metabox', 'character_metabox_nonce');
?>

    <p>
        <label for="linked_user"><strong>Compte lié</strong></label><br>
        <select name="linked_user" id="linked_user">
            <option value="">— Aucun compte —</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user->ID; ?>"
                    <?php selected($linked_user, $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label for="id"><strong>Identidiant (SI PAS RELIÉ À UN COMPTE)</strong></label><br>
        <input
            type="number"
            id="id"
            name="id"
            min="0"
            max="9999"
            value="<?php echo esc_attr($id); ?>">
    </p>

    <p>
        <label for="role"><strong>Rôle (SI PAS RELIÉ À UN COMPTE)</strong></label><br>
        <?php foreach (ROLES_PASSIVE as $r): ?>
            <label style="display:block;">
                <input
                    type="radio"
                    name="role"
                    value="<?php echo esc_attr($r); ?>"
                    <?php checked($role, $r); ?>>
                <?php echo esc_html(ucfirst($r)); ?>
            </label>
        <?php endforeach; ?>
    </p>

    <p>
        <label for="date_birthday"><strong>Date de naissance</strong></label><br>
        <input
            type="date"
            id="date_birthday"
            name="date_birthday"
            value="<?php echo esc_attr($date_birthday); ?>">
    </p>

    <p><label for="residence_primary">Résidence Principale</label><br />
        <input id="residence_primary" type="text" name="residence_primary" value="<?php echo esc_attr($residence_primary); ?>" />
    </p>

    <p><label for="residence_secondary">Résidence Actuelle</label><br />
        <input id="residence_secondary" type="text" name="residence_secondary" value="<?php echo esc_attr($residence_secondary); ?>" />
    </p>

    <p>
        <strong>Genre</strong>

    <div>
        <label>
            <input type="radio" name="gender" value="male" <?php checked($gender, 'male'); ?>>
            Homme
        </label>

        <label style="margin-left:20px;">
            <input type="radio" name="gender" value="female" <?php checked($gender, 'female'); ?>>
            Femme
        </label>
    </div>
    </p>

    <p><label for="nationality">Nationalité</label><br />
        <input id="nationality" type="text" name="nationality" value="<?php echo esc_attr($nationality); ?>" />
    </p>

    <p>
        <label for="date_recruitment"><strong>Date de recrutement</strong></label><br>
        <input
            type="date"
            id="date_recruitment"
            name="date_recruitment"
            value="<?php echo esc_attr($date_recruitment); ?>">
    </p>

    <p><label for="affiliated_ubsidiary">Filiale Affiliée</label><br />
        <input id="affiliated_ubsidiary" type="text" name="affiliated_ubsidiary" value="<?php echo esc_attr($affiliated_ubsidiary); ?>" placeholder="France, Italie, Angleterre..." />
    </p>

    <p>
        <label for="pathologies"><strong>Pathologies</strong></label><br>
        <textarea
            name="pathologies"
            id="pathologies"
            style="width:100%; height:200px"><?php echo esc_textarea($pathologies); ?></textarea>
    </p>

    <p>
        <label for="relatives"><strong>Proches</strong></label><br>
        <textarea
            name="relatives"
            id="relatives"
            style="width:100%; height:200px"><?php echo esc_textarea($relatives); ?></textarea>
    </p>

<?php
}


function save_metabox_character($post_id)
{
    // Sécurité
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['character_metabox_nonce']) || !wp_verify_nonce($_POST['character_metabox_nonce'], 'save_character_metabox')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'character') return;

    // --- linked_user ---
    $linked_user = intval($_POST['linked_user'] ?? 0);
    update_post_meta($post_id, '_linked_user', $linked_user);

    // --- id et role ---
    if (!$linked_user) {
        $id   = sanitize_text_field($_POST['id'] ?? '');
        $role = sanitize_text_field($_POST['role'] ?? '');
        update_post_meta($post_id, '_id', $id);
        update_post_meta($post_id, '_role', $role);
    } else {
        update_post_meta($post_id, '_id', null);
        update_post_meta($post_id, '_role', null);
    }

    // --- autres champs ---
    $fields = [
        'date_birthday'        => '_date_birthday',
        'residence_primary'    => '_residence_primary',
        'residence_secondary'  => '_residence_secondary',
        'gender'               => '_gender',
        'nationality'          => '_nationality',
        'date_recruitment'     => '_date_recruitment',
        'affiliated_ubsidiary' => '_affiliated_ubsidiary',
        'pathologies'          => '_pathologies',
        'relatives'            => '_relatives'
    ];

    foreach ($fields as $input => $meta_key) {
        if (isset($_POST[$input])) {
            $value = in_array($input, ['pathologies', 'relatives'])
                ? sanitize_textarea_field($_POST[$input])
                : sanitize_text_field($_POST[$input]);
            update_post_meta($post_id, $meta_key, $value);
        }
    }

    // ⚠️ Ne plus faire wp_update_post ici ! Laisse wp_insert_post_data gérer le slug
}
add_action('save_post_character', 'save_metabox_character');


// ré-écriture de l'url avec l'id et non le titre
add_filter('wp_insert_post_data', function ($data, $postarr) {

    if ($data['post_type'] === 'character') {

        // Récupérer linked_user
        $linked_user = get_post_meta($postarr['ID'], '_linked_user', true);

        // Récupérer ID manuel
        $manual_id = get_post_meta($postarr['ID'], '_id', true);

        if ($linked_user) {
            $user = get_userdata($linked_user);
            $slug = sanitize_title($user->display_name ?? $user->ID);
        } elseif ($manual_id) {
            $slug = sanitize_title($manual_id);
        } else {
            // fallback sur le titre si rien d'autre
            $slug = sanitize_title($data['post_title']);
        }

        $data['post_name'] = $slug;
    }

    return $data;
}, 10, 2);


// Masquer le champ slug dans l'éditeur classique
add_action('admin_head', function () {
    $screen = get_current_screen();
    if ($screen->post_type === 'character') {
        echo '<style>
            #edit-slug-buttons { display: none; }
        </style>';
    }
});

// Colonnes tableau backoffice
function character_admin_columns($columns) {

    $new = [];

    foreach ($columns as $key => $label) {

        $new[$key] = $label;

        if ($key === 'title') {
            $new['linked_user'] = 'Lier à un Compte';
            $new['identifier'] = 'Identifiant';
            $new['role'] = 'Rôle';
        }
    }

    return $new;
}

add_filter('manage_character_posts_columns', 'character_admin_columns');

// Contenu des colonnes du tableau en backoffice
function character_admin_column_content($column, $post_id) {

    if ($column === 'linked_user') {

        $user_id = get_post_meta($post_id, '_linked_user', true);

        if ($user_id) {
            echo '<span class="char-yes">Oui</span>';
        } else {
            echo '<span class="char-no">Non</span>';
        }
    }

    if ($column === 'identifier') {

        $user_id = get_post_meta($post_id, '_linked_user', true);

        if ($user_id) {

            $user = get_userdata($user_id);

            if ($user) {
                echo esc_html($user->user_login);
            }

        } else {

            $id = get_post_meta($post_id, '_id', true);

            if ($id) {
                echo esc_html($id);
            } else {
                echo '—';
            }

        }
    }

    if ($column === 'role') {

        $user_id = get_post_meta($post_id, '_linked_user', true);

        if ($user_id) {

            $user = get_userdata($user_id);

            if ($user && !empty($user->roles)) {
                echo esc_html(implode(', ', $user->roles));
            }

        } else {

            $role = get_post_meta($post_id, '_role', true);

            if ($role) {
                echo esc_html($role);
            } else {
                echo '—';
            }

        }
    }

}
add_action('manage_character_posts_custom_column', 'character_admin_column_content', 10, 2);

add_action('admin_head', function () {

    $screen = get_current_screen();

    if ($screen->post_type === 'character') {

        echo '<style>

        .char-yes{
            background:#d4edda;
            color:#155724;
            padding:4px 8px;
            border-radius:4px;
            font-weight:600;
        }

        .char-no{
            background:#f8d7da;
            color:#721c24;
            padding:4px 8px;
            border-radius:4px;
            font-weight:600;
        }

        </style>';
    }

});

// function add_character_caps() {
//     // $role = get_role('administrator');
//     $role = get_role('editor');

//     $caps = [
//         'edit_character',
//         'read_character',
//         'delete_character',
//         'edit_characters',
//         'edit_others_characters',
//         'publish_characters',
//         'read_private_characters',
//         'delete_characters',
//         'delete_private_characters',
//         'delete_published_characters',
//         'delete_others_characters',
//         'edit_private_characters',
//         'edit_published_characters',
//     ];

//     foreach ($caps as $cap) {
//         $role->add_cap($cap);
//     }
// }
// add_action('admin_init', 'add_character_caps');

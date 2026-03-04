<?php

function cpt__character() {

	$labels = array(
		'name'                => _x( 'Personnages', 'Post Type General Name'),
		'singular_name'       => _x( 'Personnage', 'Post Type Singular Name'),
		'menu_name'           => __( 'Personnages'),
		'all_items'           => __( 'Tous les Personnages'),
		'view_item'           => __( 'Voir les Personnages'),
		'add_new_item'        => __( 'Ajouter un nouveau Personnage'),
		'add_new'             => __( 'Ajouter'),
		'edit_item'           => __( 'Editer le Personnage'),
		'update_item'         => __( 'Modifier le Personnage'),
		'search_items'        => __( 'Rechercher un Personnage'),
		'not_found'           => __( 'Non trouvé'),
		'not_found_in_trash'  => __( 'Non trouvé dans la corbeille'),
        'view_items'          => __( 'Voir la liste des Personnage'),
        'featured_image'        => 'Photo d\'identité',
        'set_featured_image'    => 'Définir la photo d\'identité',
        'remove_featured_image' => 'Supprimer la photo d\'identité',
        'use_featured_image'    => 'Utiliser comme photo d\'identité',
    );
		
	$args = array(
        'label'               => __( 'Personnage'),
		'description'         => __( 'Tous sur Personnage'),
		'labels'              => $labels,
		'supports'            => array( 'title', 'thumbnail'),
		'show_in_rest'        => true,
		'hierarchical'        => false,
		'public'              => true,
		'has_archive'         => true,
		'rewrite'			  => array( 'slug' => 'membre'),
        'menu_icon'           => 'dashicons-groups',
        'capability_type'     => 'character',
        'map_meta_cap'        => true,
	);
	
	register_post_type( 'character', $args );
}
add_action( 'init', 'cpt__character', 0 );

add_filter('enter_title_here', 'change_character_title_placeholder');

function change_character_title_placeholder($title) {
    $screen = get_current_screen();

    if ($screen->post_type === 'character') {
        $title = 'Prénom Nom';
    }

    return $title;
}

/**
 * Metabox complète pour le CPT "Personnage"
 */

function add_metabox_character() {
    add_meta_box('character_meta', 'Informations sur le Personnage.', 'show_metabox_character', 'character');
}
add_action('add_meta_boxes', 'add_metabox_character');

function show_metabox_character($post) {

    // Récupération des metas
    $linked_user = get_post_meta($post->ID, '_linked_user', true);

    $users = get_users([
        'orderby' => 'display_name',
        'order'   => 'ASC',
        'role__not_in' => ['administrator', 'editor'],
    ]);

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
        <label for="date_birthday"><strong>Date de naissance</strong></label><br>
        <input
            type="date"
            id="date_birthday"
            name="date_birthday"
            value="<?php echo esc_attr($date_birthday); ?>"
        >
    </p>

    <p><label for="residence_primary">Résidence Principale</label><br />
    <input id="residence_primary" type="text" name="residence_primary" value="<?php echo esc_attr($residence_primary); ?>" /></p>

    <p><label for="residence_secondary">Résidence Actuelle</label><br />
    <input id="residence_secondary" type="text" name="residence_secondary" value="<?php echo esc_attr($residence_secondary); ?>" /></p>
    
    <p>
        <strong>Genre</strong>
        <label>
            <input type="radio" name="gender" value="male" <?php checked($gender, 'male'); ?>>
            Homme
        </label><br>
        
        <label>
            <input type="radio" name="gender" value="female" <?php checked($gender, 'female'); ?>>
            Femme
        </label><br>
    </p>
    
    <p><label for="nationality">Nationalité</label><br />
    <input id="nationality" type="text" name="nationality" value="<?php echo esc_attr($nationality); ?>" /></p>

    <p>
        <label for="date_recruitment"><strong>Date de recrutement</strong></label><br>
        <input
            type="date"
            id="date_recruitment"
            name="date_recruitment"
            value="<?php echo esc_attr($date_recruitment); ?>"
        >
    </p>

    <p><label for="affiliated_ubsidiary">Filiale Affiliée</label><br />
    <input id="affiliated_ubsidiary" type="text" name="affiliated_ubsidiary" value="<?php echo esc_attr($affiliated_ubsidiary); ?>" placeholder="France, Italie, Angleterre..." /></p>

    <p>
        <label for="pathologies"><strong>Pathologies</strong></label><br>
        <textarea
            name="pathologies"
            id="pathologies"
            style="width:100%; height:200px"
        ><?php echo esc_textarea($pathologies); ?></textarea>
    </p>

    <p>
        <label for="relatives"><strong>Proches</strong></label><br>
        <textarea
            name="relatives"
            id="relatives"
            style="width:100%; height:200px"
        ><?php echo esc_textarea($relatives); ?></textarea>
    </p>

    <?php
}


function save_metabox_character($post_id) {

    // Sécurité
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['character_metabox_nonce']) ||
        !wp_verify_nonce($_POST['character_metabox_nonce'], 'save_character_metabox')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'character') return;

    
    if (isset($_POST['linked_user'])) {
        update_post_meta($post_id, '_linked_user', sanitize_text_field($_POST['linked_user']));
    }

    if (isset($_POST['date_birthday'])) {
        update_post_meta($post_id, '_date_birthday', sanitize_text_field($_POST['date_birthday']));
    }

    if (isset($_POST['residence_primary'])) {
        update_post_meta($post_id, '_residence_primary', sanitize_text_field($_POST['residence_primary']));
    }

    if (isset($_POST['residence_secondary'])) {
        update_post_meta($post_id, '_residence_secondary', sanitize_text_field($_POST['residence_secondary']));
    }

    if (isset($_POST['gender'])) {
        update_post_meta($post_id, '_gender', sanitize_text_field($_POST['gender']));
    }

    if (isset($_POST['nationality'])) {
        update_post_meta($post_id, '_nationality', sanitize_text_field($_POST['nationality']));
    }

    if (isset($_POST['date_recruitment'])) {
        update_post_meta($post_id, '_date_recruitment', sanitize_text_field($_POST['date_recruitment']));
    }

    if (isset($_POST['affiliated_ubsidiary'])) {
        update_post_meta($post_id, '_affiliated_ubsidiary', sanitize_text_field($_POST['affiliated_ubsidiary']));
    }

    if (isset($_POST['pathologies'])) {
        update_post_meta($post_id, '_pathologies', sanitize_textarea_field($_POST['pathologies']));
    }

    if (isset($_POST['relatives'])) {
        update_post_meta($post_id, '_relatives', sanitize_textarea_field($_POST['relatives']));
    }

}
add_action('save_post_character', 'save_metabox_character');


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

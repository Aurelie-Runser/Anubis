<?php

function cpt__is() {

	$labels = array(
		'name'                => _x( 'I.S.', 'Post Type General Name'),
		'singular_name'       => _x( 'I.S.', 'Post Type Singular Name'),
		'menu_name'           => __( 'I.S.'),
		'all_items'           => __( 'Tous les I.S.'),
		'view_item'           => __( 'Voir les I.S.'),
		'add_new_item'        => __( 'Ajouter un nouvel I.S.'),
		'add_new'             => __( 'Ajouter'),
		'edit_item'           => __( 'Editer l\'I.S.'),
		'update_item'         => __( 'Modifier l\'I.S.'),
		'search_items'        => __( 'Rechercher un I.S.'),
		'not_found'           => __( 'Non trouvé'),
		'not_found_in_trash'  => __( 'Non trouvé dans la corbeille'),
        'view_items'          => __( 'Voir la liste des I.S.'),
    );
		
	$args = array(
        'label'               => __( 'I.S.'),
		'description'         => __( 'Tous sur I.S.'),
		'labels'              => $labels,
		'supports'            => ['title'],
		'show_in_rest'        => true,
		'hierarchical'        => false,
		'public'              => true,
		'has_archive'         => true,
        'show_in_menu'        => true,
        'show_ui'             => true,
		'rewrite'			  => ['slug' => 'is', 'with_front' => false],
        'menu_icon'           => 'dashicons-pets',
        'capability_type'     => 'is',
        'map_meta_cap'        => true,
	);
	
	register_post_type( 'is', $args );
}
add_action( 'init', 'cpt__is', 0 );

function taxonomy__is() {

    $labels = array(
        'name'              => 'Niveaux de dangerosités',
        'singular_name'     => 'Niveau de dangerosité',
        'search_items'      => 'Rechercher un niveau',
        'all_items'         => 'Tous les Niveaux',
        'edit_item'         => 'Éditer le Niveau',
        'update_item'       => 'Mettre à jour',
        'add_new_item'      => 'Ajouter un Niveau',
        'new_item_name'     => 'Nouveau Niveau',
        'menu_name'         => 'Niveaux de dangerosités',
    );

    register_taxonomy('is_category', ['is'], array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite' => array(
            'slug' => 'is-danger', // <-- base complète pour URL
            'with_front' => false,
            'hierarchical' => true, // permet les sous-termes
        ),
    ));
}
add_action('init', 'taxonomy__is');



/**
 * Metabox complète pour le CPT "is"
 */

function add_metabox_is() {
    add_meta_box('is_meta', 'Informations sur l\'I.S.', 'show_metabox_is', 'is');
}
add_action('add_meta_boxes', 'add_metabox_is');

function show_metabox_is($post) {

    // Récupération des metas
    $id            = get_post_meta($post->ID, '_id', true);
    $name          = get_post_meta($post->ID, '_name', true);
    $etat          = get_post_meta($post->ID, '_etat', true);

    $roles_allowed = get_post_meta($post->ID, '_roles_allowed', true);
    $roles_allowed = is_array($roles_allowed) ? $roles_allowed : [];

    $date_discover = get_post_meta($post->ID, '_date_discover', true);
    $date_catch    = get_post_meta($post->ID, '_date_catch', true);
    $capacities    = get_post_meta($post->ID, '_capacities', true);
    $description   = get_post_meta($post->ID, '_description', true);
    $galerie       = get_post_meta($post->ID, '_galerie', true);

    $galerie_ids = $galerie ? explode(',', $galerie) : [];

    wp_nonce_field('save_is_metabox', 'is_metabox_nonce');
    ?>

    <!-- Numéro -->
    <p><label for="id">Numéro</label><br />
    <input id="id" type="number" name="id" value="<?php echo esc_attr($id); ?>" /></p>

    <!-- Nom vernaculaire -->
    <p><label for="name">Nom vernaculaire</label><br />
    <input id="name" type="text" name="name" value="<?php echo esc_attr($name); ?>" /></p>

    <!-- ROLES AUTORISÉS -->
    <p>
        <strong>Rôles autorisés à consulter</strong><br>

        <?php foreach (ROLES_PASSIVE as $role): ?>
            <label style="display:block;">
                <input
                    type="checkbox"
                    name="roles_allowed[]"
                    value="<?php echo esc_attr($role); ?>"
                    <?php checked(in_array($role, $roles_allowed)); ?>
                >
                <?php echo esc_html(ucfirst($role)); ?>
            </label>
        <?php endforeach; ?>
    </p>

    <!-- ÉTAT -->
    <p>
        <strong>État</strong><br>
        <label>
            <input type="radio" name="etat" value="capture" <?php checked($etat, 'capture'); ?>>
            Capturé
        </label><br>
        <label>
            <input type="radio" name="etat" value="under_control" <?php checked($etat, 'under_control'); ?>>
            Sous contol
        </label><br>
        <label>
            <input type="radio" name="etat" value="liberte" <?php checked($etat, 'liberte'); ?>>
            En liberté
        </label><br>
        <label>
            <input type="radio" name="etat" value="echappe" <?php checked($etat, 'echappe'); ?>>
            Échappé
        </label>
    </p>

    <!-- DATE DE DÉCOUVERTE -->
    <p>
        <label for="date_discover"><strong>Date de découverte</strong></label><br>
        <input
            type="date"
            id="date_discover"
            name="date_discover"
            value="<?php echo esc_attr($date_discover); ?>"
        >
    </p>

    <!-- DATE DE CAPTURE -->
    <p>
        <label for="date_catch"><strong>Date de capture</strong></label><br>
        <input
            type="date"
            id="date_catch"
            name="date_catch"
            value="<?php echo esc_attr($date_catch); ?>"
        >
    </p>

    <!-- CAPACITIES -->
    <p>
        <label for="capacities"><strong>Capacités</strong></label><br>
        <span class="description">
            Saisissez une capacité par ligne. Chaque ligne sera affichée comme un élément de liste.
        </span>
        <textarea
            name="capacities"
            id="capacities"
            style="width:100%; height:200px"
        ><?php echo esc_textarea($capacities); ?></textarea>
    </p>


    <!-- description -->
    <p>
        <label for="description"><strong>Description</strong></label><br>
        <textarea
            name="description"
            id="description"
            style="width:100%; height:200px"
        ><?php echo esc_textarea($description); ?></textarea>
    </p>

    <!-- GALERIE -->
    <p>
        <label><strong>Galerie</strong></label><br>
        <input type="hidden" name="galerie" id="galerie_input" value="<?php echo esc_attr($galerie); ?>">
        <button type="button" class="button" id="upload_gallery_button">
            Sélectionner des images
        </button>

        <div id="gallery_preview" style="margin-top:10px;">
            <?php
            foreach ($galerie_ids as $img_id) {
                $img = wp_get_attachment_image($img_id, 'thumbnail');
                if ($img) {
                    echo '<span style="margin-right:5px; display:inline-block;">' . $img . '</span>';
                }
            }
            ?>
        </div>
    </p>

    <?php
}


function save_metabox_is($post_id) {

    // Sécurité
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['is_metabox_nonce']) ||
        !wp_verify_nonce($_POST['is_metabox_nonce'], 'save_is_metabox')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'is') return;

    // ID
    if (isset($_POST['id'])) {
        update_post_meta($post_id, '_id', sanitize_text_field($_POST['id']));
    }

    // NOM
    if (isset($_POST['name'])) {
        update_post_meta($post_id, '_name', sanitize_text_field($_POST['name']));
    }

    // ÉTAT
    if (isset($_POST['etat'])) {
        update_post_meta($post_id, '_etat', sanitize_text_field($_POST['etat']));
    }

    // ROLES AUTORISÉS
    if (isset($_POST['roles_allowed']) && is_array($_POST['roles_allowed'])) {
        $roles = array_map('sanitize_text_field', $_POST['roles_allowed']);
        update_post_meta($post_id, '_roles_allowed', $roles);
    } else {
        delete_post_meta($post_id, '_roles_allowed');
    }

    // DATE DÉCOUVERTE
    if (isset($_POST['date_discover'])) {
        update_post_meta($post_id, '_date_discover', sanitize_text_field($_POST['date_discover']));
    }

    // DATE CAPTURE
    if (isset($_POST['date_catch'])) {
        update_post_meta($post_id, '_date_catch', sanitize_text_field($_POST['date_catch']));
    }

    // CAPACITIES
    if (isset($_POST['capacities'])) {
        update_post_meta($post_id, '_capacities', sanitize_textarea_field($_POST['capacities']));
    }

    // TEXTE
    if (isset($_POST['description'])) {
        update_post_meta($post_id, '_description', sanitize_textarea_field($_POST['description']));
    }

    // GALERIE
    if (isset($_POST['galerie'])) {
        update_post_meta($post_id, '_galerie', sanitize_text_field($_POST['galerie']));
    }
}
add_action('save_post_is', 'save_metabox_is');


function is_enqueue_admin_scripts($hook) {
    if ($hook === 'post-new.php' || $hook === 'post.php') {
        global $post;
        if ('is' === $post->post_type) {
            wp_enqueue_media();
            wp_enqueue_script('is-metabox', get_template_directory_uri() . '/app/cpt_gallery.js', ['jquery'], null, true);
        }
    }
}
add_action('admin_enqueue_scripts', 'is_enqueue_admin_scripts');

// function add_is_caps() {
//     $role = get_role('administrator');
//     $role = get_role('editor');

//     $caps = [
//         'edit_is',
//         'read_is',
//         'delete_is',
//         'edit_iss',
//         'edit_others_iss',
//         'publish_iss',
//         'read_private_iss',
//         'delete_iss',
//         'delete_private_iss',
//         'delete_published_iss',
//         'delete_others_iss',
//         'edit_private_iss',
//         'edit_published_iss',
//     ];

//     foreach ($caps as $cap) {
//         $role->add_cap($cap);
//     }
// }
// add_action('admin_init', 'add_is_caps');

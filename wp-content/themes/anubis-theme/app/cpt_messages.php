<?php

function cpt__message()
{

    $labels = array(
        'name'                => _x('Messagerie', 'Post Type General Name'),
        'singular_name'       => _x('Message', 'Post Type Singular Name'),
        'menu_name'           => __('Messages'),
        'all_items'           => __('Tous les Messages'),
        'view_item'           => __('Voir les Messages'),
        'add_new_item'        => __('Ajouter un nouveau Message'),
        'add_new'             => __('Ajouter'),
        'edit_item'           => __('Editer le Message'),
        'update_item'         => __('Modifier le Message'),
        'search_items'        => __('Rechercher un Message'),
        'not_found'           => __('Non trouvé'),
        'not_found_in_trash'  => __('Non trouvé dans la corbeille'),
        'view_items'          => __('Voir la liste des Message'),
    );

    $args = array(
        'label'               => __('Message'),
        'description'         => __('Tous sur Message'),
        'labels'              => $labels,
        'supports'            => array('title'),
        'show_in_rest'        => true,
        'hierarchical'        => false,
        'public'              => true,
        'has_archive'         => true,
        'rewrite'              => array('slug' => 'messagerie'),
        'menu_icon'           => 'dashicons-admin-comments',
        'capability_type'     => 'message',
        'map_meta_cap'        => true,
    );

    register_post_type('message', $args);
}
add_action('init', 'cpt__message', 0);

/**
 * Metabox complète pour le CPT "Message"
 */

function add_metabox_message()
{
    add_meta_box('message_meta', 'Historique de messagerie', 'show_metabox_message', 'message');
}
add_action('add_meta_boxes', 'add_metabox_message');

function show_metabox_message($post)
{

    // Récupération des metas
    $messages = get_post_meta($post->ID, '_messages', true);
    $messages = is_array($messages) ? $messages : [];

    $character_1 = get_post_meta($post->ID, '_character_1', true);
    $character_2 = get_post_meta($post->ID, '_character_2', true);
    $character_posts = get_posts([
        'post_type' => 'character',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    wp_nonce_field('save_message_metabox', 'message_metabox_nonce');
?>

    <p>
        <strong>Personnage 1</strong>
    </p>
    <div style="max-height:200px; overflow:auto; border:1px solid #ddd; padding:10px; display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
        <?php foreach ($character_posts as $character): ?>
            <label style="display:block;">
                <input
                    type="radio"
                    name="character_1"
                    value="<?php echo esc_attr($character->ID); ?>"
                    <?php checked($character_1, $character->ID); ?>>
                <?php echo esc_html($character->post_title); ?>
            </label>
        <?php endforeach; ?>
    </div>

    <p>
        <strong>Personnage 2</strong>
    </p>
    <div style="max-height:200px; overflow:auto; border:1px solid #ddd; padding:10px; display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
        <?php foreach ($character_posts as $character): ?>
            <label style="display:block;">
                <input
                    type="radio"
                    name="character_2"
                    value="<?php echo esc_attr($character->ID); ?>"
                    <?php checked($character_2, $character->ID); ?>>
                <?php echo esc_html($character->post_title); ?>
            </label>
        <?php endforeach; ?>
    </div>

<?php
}

function save_metabox_message($post_id)
{

    // Sécurité
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (
        !isset($_POST['message_metabox_nonce']) ||
        !wp_verify_nonce($_POST['message_metabox_nonce'], 'save_message_metabox')
    ) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'message') return;


    if (isset($_POST['messages'])) {
        update_post_meta($post_id, '_messages', sanitize_textarea_field($_POST['messages']));
    }

    if (isset($_POST['character_1'])) {
        update_post_meta($post_id, '_character_1', sanitize_textarea_field($_POST['character_1']));
    }

    if (isset($_POST['character_2'])) {
        update_post_meta($post_id, '_character_2', sanitize_textarea_field($_POST['character_2']));
    }

}
add_action('save_post_message', 'save_metabox_message');


// function add_message_caps() {
//     // $role = get_role('administrator');
//     $role = get_role('editor');

//     $caps = [
//         'edit_message',
//         'read_message',
//         'delete_message',
//         'edit_messages',
//         'edit_others_messages',
//         'publish_messages',
//         'read_private_messages',
//         'delete_messages',
//         'delete_private_messages',
//         'delete_published_messages',
//         'delete_others_messages',
//         'edit_private_messages',
//         'edit_published_messages',
//     ];

//     foreach ($caps as $cap) {
//         $role->add_cap($cap);
//     }
// }
// add_action('admin_init', 'add_message_caps');

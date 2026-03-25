<?php

function cpt__rapport()
{

    $labels = array(
        'name'                => _x('Rapports', 'Post Type General Name'),
        'singular_name'       => _x('Rapport', 'Post Type Singular Name'),
        'menu_name'           => __('Rapports'),
        'all_items'           => __('Tous les Rapports'),
        'view_item'           => __('Voir les Rapports'),
        'add_new_item'        => __('Ajouter un nouveau Rapport'),
        'add_new'             => __('Ajouter'),
        'edit_item'           => __('Editer le Rapport'),
        'update_item'         => __('Modifier le Rapport'),
        'search_items'        => __('Rechercher un Rapport'),
        'not_found'           => __('Non trouvé'),
        'not_found_in_trash'  => __('Non trouvé dans la corbeille'),
        'view_items'          => __('Voir la liste des Rapport'),
    );

    $args = array(
        'label'               => __('Rapport'),
        'description'         => __('Tous sur Rapport'),
        'labels'              => $labels,
        'supports'            => array(''),
        'show_in_rest'        => true,
        'hierarchical'        => false,
        'public'              => true,
        'has_archive'         => false,
        'rewrite'              => false,
        'menu_icon'           => 'dashicons-media-document',
        'capability_type'     => 'rapport',
        'map_meta_cap'        => true,
    );

    register_post_type('rapport', $args);
}
add_action('init', 'cpt__rapport', 0);

// Réécritur d'url
add_filter('post_type_link', function ($permalink, $post) {
    if ($post->post_type !== 'rapport') return $permalink;
    $folder_id = get_post_meta($post->ID, '_linked_folder', true);
    if (!$folder_id) return $permalink;
    $folder = get_post($folder_id);
    if (!$folder) return $permalink;
    return home_url("/dossier/{$folder->post_name}/rapport/{$post->post_name}");
}, 10, 2);
add_action('init', function () {
    add_rewrite_rule(
        '^dossier/([^/]+)/rapport/([^/]+)/?$',
        'index.php?post_type=rapport&name=$matches[2]&folder_slug=$matches[1]',
        'top'
    );
});


/**
 * Metabox complète pour le CPT "Rapport"
 */

function add_metabox_rapport()
{
    add_meta_box('rapport_meta', 'Informations sur le Rapport', 'show_metabox_rapport', 'rapport');
}
add_action('add_meta_boxes', 'add_metabox_rapport');

function show_metabox_rapport($post)
{

    $date_rapport = get_post_meta($post->ID, '_date_rapport', true);

    $linked_folder = get_post_meta($post->ID, '_linked_folder', true);
    $folder_posts = get_posts([
        'post_type' => 'folder',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $rapport_author = get_post_meta($post->ID, '_rapport_author', true);
    $character_posts = get_posts([
        'post_type' => 'character',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);


    $galerie = get_post_meta($post->ID, '_galerie', true);
    $galerie_ids = $galerie ? explode(',', $galerie) : [];

    $steps = get_post_meta($post->ID, '_rapport_steps', true);
    $steps = is_array($steps) ? $steps : [];

    wp_nonce_field('save_rapport_metabox', 'rapport_metabox_nonce');
?>

    <p>
        <label for="date_rapport"><strong>Jour des événements</strong></label><br>
        <input
            type="date"
            id="date_rapport"
            name="date_rapport"
            value="<?php echo esc_attr($date_rapport); ?>">
    </p>


    <p>
        <strong>Auteur de ce rapport</strong>
    </p>
    <div style="max-height:200px; overflow:auto; border:1px solid #ddd; padding:10px; display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
        <?php foreach ($character_posts as $character): ?>
            <label style="display:block;">
                <input
                    type="radio"
                    name="rapport_author"
                    value="<?php echo esc_attr($character->ID); ?>"
                    <?php checked($rapport_author, $character->ID); ?>>
                <?php echo esc_html($character->post_title); ?>
            </label>
        <?php endforeach; ?>
    </div>

    <p>
        <strong>Dossier associé</strong>
    </p>
    <div style="max-height:200px; overflow:auto; border:1px solid #ddd; padding:10px; display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
        <?php foreach ($folder_posts as $folder): ?>
            <label style="display:block;">
                <input
                    type="radio"
                    name="linked_folder"
                    value="<?php echo esc_attr($folder->ID); ?>"
                    <?php checked($linked_folder, $folder->ID); ?>>
                <?php echo esc_html($folder->post_title); ?>
            </label>
        <?php endforeach; ?>
    </div>

     <!-- GALERIE -->
    <p>
        <label><strong>Galerie</strong></label><br>
        <input type="hidden" name="galerie" id="galerie_input" value="<?php echo esc_attr($galerie); ?>">
        <button type="button" class="button" id="upload_gallery_button">
            Sélectionner des images
        </button>

    <div id="gallery_preview" style="margin-top:10px;">
        <?php
        foreach ($galerie_ids as $media_id) {
            $mime_type = get_post_mime_type($media_id);

            if (str_starts_with($mime_type, 'image/')) {
                $preview = wp_get_attachment_image($media_id, 'thumbnail');
            } elseif (str_starts_with($mime_type, 'video/')) {
                $video_url = wp_get_attachment_url($media_id);
                $preview = '<video src="' . esc_url($video_url) . '" height="150" controls muted></video>';
            } else {
                continue; // ignorer les autres types
            }

            echo '<span style="margin-right:5px; display:inline-block;">' . $preview . '</span>';
        }
        ?>
    </div>
    </p>

    <p>
        <strong>Etapes</strong>
        <br/><span class="description">(Heure / Situation)</span>
    </p>

    <div id="steps-container">
        <?php foreach ($steps as $index => $step): ?>
            <div class="step-item">
                <input type="time" name="steps[<?php echo $index; ?>][time]" value="<?php echo esc_attr($step['time']); ?>" />
                <input type="text" name="steps[<?php echo $index; ?>][content]" value="<?php echo esc_attr($step['content']); ?>" placeholder="Situation..." />
                <button type="button" class="button remove-step">Supprimer</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" class="button button-primary" id="add-step">Ajouter une étape</button>

    <style>
        #steps-container {
            padding: 5px;
            border: 1px solid grey;
        }

        .step-item {
            display: flex;
            margin-bottom: 10px;
        }

        .step-item input[type=text] {
            width: 100%;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const container = document.getElementById('steps-container');
            const addBtn = document.getElementById('add-step');

            let index = <?php echo count($steps); ?>;

            addBtn.addEventListener('click', function() {
                const div = document.createElement('div');
                div.classList.add('step-item');

                div.innerHTML = `
            <input type="time" name="steps[${index}][time]" />
            <input type="text" name="steps[${index}][content]" placeholder="Description..." />
            <button type="button" class="button remove-step">Supprimer</button>
        `;

                container.appendChild(div);
                index++;
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-step')) {
                    e.target.closest('.step-item').remove();
                }
            });

        });
    </script>

<?php
}


function save_metabox_rapport($post_id)
{

    // Sécurité
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (
        !isset($_POST['rapport_metabox_nonce']) ||
        !wp_verify_nonce($_POST['rapport_metabox_nonce'], 'save_rapport_metabox')
    ) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'rapport') return;

    if (isset($_POST['date_rapport'])) {
        update_post_meta($post_id, '_date_rapport', sanitize_text_field($_POST['date_rapport']));
    }

    if (isset($_POST['rapport_author'])) {
        update_post_meta($post_id, '_rapport_author', sanitize_text_field($_POST['rapport_author']));
    }

    if (isset($_POST['linked_folder'])) {
        update_post_meta($post_id, '_linked_folder', sanitize_text_field($_POST['linked_folder']));
    }

    if (isset($_POST['galerie'])) {
        update_post_meta($post_id, '_galerie', sanitize_text_field($_POST['galerie']));
    }

    if (isset($_POST['steps']) && is_array($_POST['steps'])) {

        $clean_steps = [];

        foreach ($_POST['steps'] as $step) {

            if (empty($step['time']) && empty($step['content'])) continue;

            $clean_steps[] = [
                'time' => sanitize_text_field($step['time']),
                'content' => sanitize_text_field($step['content']),
            ];
        }

        update_post_meta($post_id, '_rapport_steps', $clean_steps);

    } else {
        delete_post_meta($post_id, '_rapport_steps');
    }
}
add_action('save_post_rapport', 'save_metabox_rapport');

// update du title
add_filter('wp_insert_post_data', function ($data, $postarr) {

    if ($data['post_type'] === 'rapport') {

        // metas
        $linked_folder = get_post_meta($postarr['ID'], '_linked_folder', true);
        $date_rapport  = get_post_meta($postarr['ID'], '_date_rapport', true);
        $date_rapport  = str_replace('-', '', $date_rapport);

        $author_character_id = get_post_meta($postarr['ID'], '_rapport_author', true);

        // 🔥 récupérer le BON identifiant auteur
        $author_identifier = '';

        if ($author_character_id) {

            $manual_id   = get_post_meta($author_character_id, '_id', true);
            $linked_user = get_post_meta($author_character_id, '_linked_user', true);

            if (!empty($manual_id)) {
                $author_identifier = $manual_id;
            } elseif (!empty($linked_user)) {
                $user = get_userdata($linked_user);
                $author_identifier = $user ? $user->user_login : '';
            }
        }

        // construire le titre
        $title_parts = [];

        if ($linked_folder)      $title_parts[] = get_post_meta($linked_folder, '_id', true);
        if ($date_rapport)       $title_parts[] = $date_rapport;
        if ($author_identifier)  $title_parts[] = $author_identifier;

        $data['post_title'] = implode('-', $title_parts);
        $data['post_name']  = sanitize_title($data['post_title']);
    }

    return $data;
}, 10, 2);

// Colonnes tableau backoffice
function rapport_admin_columns($columns)
{

    $new_columns = [];

    foreach ($columns as $key => $label) {

        $new_columns[$key] = $label;

        if ($key === 'title') {
            $new_columns['linked_folder'] = 'Dossié associé';
            $new_columns['date_rapport'] = 'Jour';
            $new_columns['rapport_author'] = 'Auteur';
        }
    }

    return $new_columns;
}
add_filter('manage_rapport_posts_columns', 'rapport_admin_columns');

// Contenu des colonnes du tableau en backoffice
function rapport_admin_column_content($column, $post_id)
{


    if ($column === 'linked_folder') {

        $linked_folder = get_post_meta($post_id, '_linked_folder', true);

        if (!empty($linked_folder)) {

            $title = get_the_title($linked_folder);
            $url   = get_edit_post_link($linked_folder);

            echo '<a href="' . esc_url($url) . '">' . esc_html($title) . '</a>';
        } else {
            echo '—';
        }
    }

    if ($column === 'date_rapport') {

        $date_rapport = get_post_meta($post_id, '_date_rapport', true);

        echo '<span>' . esc_html($date_rapport) . '</span>';
    }

    if ($column === 'rapport_author') {

        $rapport_author = get_post_meta($post_id, '_rapport_author', true);

        if (!empty($rapport_author)) {

            $title = get_the_title($rapport_author);
            $url   = get_edit_post_link($rapport_author);

            echo '<a href="' . esc_url($url) . '">' . esc_html($title) . '</a>';
        } else {
            echo '—';
        }
    }
}
add_action('manage_rapport_posts_custom_column', 'rapport_admin_column_content', 10, 2);

// Ajouter le filtre dropdown dans l'admin
add_action('restrict_manage_posts', function ($post_type) {
    if ($post_type !== 'rapport') return;

    // Récupérer tous les dossiers
    $folders = get_posts([
        'post_type' => 'folder',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $selected = $_GET['_linked_folder'] ?? '';
    echo '<select name="_linked_folder" style="margin-left:10px;">';
    echo '<option value="">Tous les dossiers</option>';
    foreach ($folders as $folder) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr($folder->ID),
            selected($selected, $folder->ID, false),
            esc_html($folder->post_title)
        );
    }
    echo '</select>';
});

// Filtrer la requête selon le dossier sélectionné
add_action('pre_get_posts', function ($query) {
    global $pagenow;
    if (!is_admin() || $pagenow !== 'edit.php' || $query->get('post_type') !== 'rapport') return;

    $folder_id = $_GET['_linked_folder'] ?? '';
    if ($folder_id) {
        $meta_query = [
            [
                'key' => '_linked_folder',
                'value' => $folder_id,
                'compare' => '='
            ]
        ];
        $query->set('meta_query', $meta_query);
    }
});

// function add_rapport_caps() {
//     // $role = get_role('administrator');
//     $role = get_role('editor');

//     $caps = [
//         'edit_rapport',
//         'read_rapport',
//         'delete_rapport',
//         'edit_rapports',
//         'edit_others_rapports',
//         'publish_rapports',
//         'read_private_rapports',
//         'delete_rapports',
//         'delete_private_rapports',
//         'delete_published_rapports',
//         'delete_others_rapports',
//         'edit_private_rapports',
//         'edit_published_rapports',
//     ];

//     foreach ($caps as $cap) {
//         $role->add_cap($cap);
//     }
// }
// add_action('admin_init', 'add_rapport_caps');

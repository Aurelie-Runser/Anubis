<?php

function cpt__folder()
{

    $labels = array(
        'name'                => _x('Dossiers', 'Post Type General Name'),
        'singular_name'       => _x('Dossier', 'Post Type Singular Name'),
        'menu_name'           => __('Dossiers'),
        'all_items'           => __('Tous les Dossiers'),
        'view_item'           => __('Voir les Dossiers'),
        'add_new_item'        => __('Ajouter un nouveau Dossier'),
        'add_new'             => __('Ajouter'),
        'edit_item'           => __('Editer le Dossier'),
        'update_item'         => __('Modifier le Dossier'),
        'search_items'        => __('Rechercher un Dossier'),
        'not_found'           => __('Non trouvé'),
        'not_found_in_trash'  => __('Non trouvé dans la corbeille'),
        'view_items'          => __('Voir la liste des Dossier'),
    );

    $args = array(
        'label'               => __('Dossier'),
        'description'         => __('Tous sur Dossier'),
        'labels'              => $labels,
        'supports'            => array('title'),
        'show_in_rest'        => true,
        'hierarchical'        => false,
        'public'              => true,
        'has_archive'         => true,
        'rewrite'              => array('slug' => 'dossiers'),
        'menu_icon'           => 'dashicons-category',
        'capability_type'     => 'folder',
        'map_meta_cap'        => true,
    );

    register_post_type('folder', $args);
}
add_action('init', 'cpt__folder', 0);

function taxonomy__folder()
{

    $labels = array(
        'name'              => 'Années Ouverts',
        'singular_name'     => 'Année Ouvert',
        'search_items'      => 'Rechercher une Année',
        'all_items'         => 'Toutes les Années',
        'edit_item'         => 'Éditer l\'Année',
        'update_item'       => 'Mettre à jour',
        'add_new_item'      => 'Ajouter une Année',
        'new_item_name'     => 'Nouvelle Année',
        'menu_name'         => 'Années Ouvert',
    );

    register_taxonomy('folder_category', ['folder'], array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite' => array(
            'slug' => 'dossiers-annee', // <-- base complète pour URL
            'with_front' => false,
            'hierarchical' => true, // permet les sous-termes
        ),
    ));
}
add_action('init', 'taxonomy__folder');


/**
 * Metabox complète pour le CPT "Dossier"
 */

function add_metabox_folder()
{
    add_meta_box('folder_meta', 'Informations sur le Dossier', 'show_metabox_folder', 'folder');
}
add_action('add_meta_boxes', 'add_metabox_folder');

function show_metabox_folder($post)
{

    // Récupération des metas
    $roles_allowed = get_post_meta($post->ID, '_roles_allowed', true);
    $roles_allowed = is_array($roles_allowed) ? $roles_allowed : [];

    $id = get_post_meta($post->ID, '_id', true);

    $date_opening = get_post_meta($post->ID, '_date_opening', true);
    $date_closing    = get_post_meta($post->ID, '_date_closing', true);
    $date_last_update    = get_post_meta($post->ID, '_date_last_update', true);
    $description   = get_post_meta($post->ID, '_description', true);

    $linked_is = get_post_meta($post->ID, '_linked_is', true);
    $linked_is = is_array($linked_is) ? $linked_is : [];
    $is_posts = get_posts([
        'post_type' => 'is',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $linked_character = get_post_meta($post->ID, '_linked_character', true);
    $linked_character = is_array($linked_character) ? $linked_character : [];
    $character_posts = get_posts([
        'post_type' => 'character',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    wp_nonce_field('save_folder_metabox', 'folder_metabox_nonce');
?>

    <p>
        <label for="id"><strong>Identidiant</strong></label><br>
        <input
            type="number"
            id="id"
            name="id"
            min="0"
            max="9999"
            value="<?php echo esc_attr($id); ?>">
    </p>

    <p>
        <strong>Rôles autorisés à consulter</strong><br>

        <?php foreach (ROLES_PASSIVE as $role): ?>
            <label style="display:block;">
                <input
                    type="checkbox"
                    name="roles_allowed[]"
                    value="<?php echo esc_attr($role); ?>"
                    <?php checked(in_array($role, $roles_allowed)); ?>>
                <?php echo esc_html(ucfirst($role)); ?>
            </label>
        <?php endforeach; ?>
    </p>

    <p>
        <label for="date_opening"><strong>Date d'ouverture</strong></label><br>
        <input
            type="date"
            id="date_opening"
            name="date_opening"
            value="<?php echo esc_attr($date_opening); ?>">
    </p>

    <p>
        <label for="date_closing"><strong>Date de fermeture</strong></label><br>
        <input
            type="date"
            id="date_closing"
            name="date_closing"
            value="<?php echo esc_attr($date_closing); ?>">
    </p>

    <p>
        <label for="date_last_update"><strong>Date de la dernière modification</strong></label><br>
        <input
            type="date"
            id="date_last_update"
            name="date_last_update"
            value="<?php echo esc_attr($date_last_update); ?>">
    </p>

    <p>
        <label for="description"><strong>Résumé</strong></label><br>
        <textarea
            name="description"
            id="description"
            style="width:100%; height:200px"><?php echo esc_textarea($description); ?></textarea>
    </p>

    <p>
        <strong>I.S. associés à ce dossier</strong>
    </p>
    <div style="max-height:200px; overflow:auto; border:1px solid #ddd; padding:10px; display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
        <?php foreach ($is_posts as $is): ?>
            <label style="display:block;">
                <input
                    type="checkbox"
                    name="linked_is[]"
                    value="<?php echo esc_attr($is->ID); ?>"
                    <?php checked(in_array($is->ID, $linked_is)); ?>>
                <?php echo esc_html($is->post_title); ?>
            </label>
        <?php endforeach; ?>
    </div>

    <p>
        <strong>Personnages associés à ce dossier</strong>
    </p>
    <div style="max-height:200px; overflow:auto; border:1px solid #ddd; padding:10px; display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
        <?php foreach ($character_posts as $character): ?>
            <label style="display:block;">
                <input
                    type="checkbox"
                    name="linked_character[]"
                    value="<?php echo esc_attr($character->ID); ?>"
                    <?php checked(in_array($character->ID, $linked_character)); ?>>
                <?php echo esc_html($character->post_title); ?>
            </label>
        <?php endforeach; ?>
    </div>

<?php
}

add_action('add_meta_boxes', function () {
    add_meta_box(
        'folder_logs',
        'Historique',
        'render_folder_logs_metabox',
        'folder',
        'normal',
        'default'
    );
});
function render_folder_logs_metabox($post)
{

    $roles_allowed_logs = get_post_meta($post->ID, '_roles_allowed_logs', true);
    $roles_allowed_logs = is_array($roles_allowed_logs) ? $roles_allowed_logs : [];

    $logs = get_post_meta($post->ID, '_folder_logs', true);
    $logs = is_array($logs) ? $logs : [];

    $characters = get_posts([
        'post_type' => 'character',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ]);


    $is_posts = get_posts([
        'post_type' => 'is',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ]);

    wp_nonce_field('save_folder_logs', 'folder_logs_nonce');
?>

    <p><small>Si vide, affichera pour tout le monde le message "Pas droit d'accès".</small></p>

    <p>
        <strong>Rôles autorisés à consulter l'historique</strong><br>
        <?php foreach (ROLES_PASSIVE as $role): ?>
            <label style="display:block;">
                <input
                    type="checkbox"
                    name="roles_allowed_logs[]"
                    value="<?php echo esc_attr($role); ?>"
                    <?php checked(in_array($role, $roles_allowed_logs)); ?>>
                <?php echo esc_html(ucfirst($role)); ?>
            </label>
        <?php endforeach; ?>
    </p>

    <table id="folder-log-table" style="width:100%;border-collapse:collapse; border:1px solid grey">
        <thead>
            <tr>
                <th>Date / Heure</th>
                <th>Action</th>
                <th>Donnée</th>
                <th>Entité</th>
                <th>Auteur de l'Action</th>
                <th></th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($logs as $i => $log): ?>

                <tr>

                    <td>
                        <input type="datetime-local"
                            name="logs[<?php echo $i ?>][datetime]"
                            value="<?php echo esc_attr($log['datetime'] ?? '') ?>">
                    </td>

                    <td>
                        <select name="logs[<?php echo $i ?>][action]">
                            <option value="create" <?php selected($log['action'] ?? '', 'create') ?>>Créer</option>
                            <option value="update" <?php selected($log['action'] ?? '', 'update') ?>>Modifier</option>
                            <option value="delete" <?php selected($log['action'] ?? '', 'delete') ?>>Supprimer</option>
                            <option value="join" <?php selected($log['action'] ?? '', 'join') ?>>Relier</option>
                            <option value="unlink" <?php selected($log['action'] ?? '', 'unlink') ?>>Délier</option>
                            <option value="read" <?php selected($log['action'] ?? '', 'read') ?>>Lire</option>
                            <option value="close" <?php selected($log['action'] ?? '', 'close') ?>>Ferme</option>
                        </select>
                    </td>

                    <td>
                        <select name="logs[<?php echo $i ?>][target]">
                            <option value="" <?php selected($log['target'] ?? '', '') ?>>Ce Dossier</option>
                            <option value="roles_allowed" <?php selected($log['target'] ?? '', 'roles_allowed') ?>>Roles Autorisés</option>
                            <option value="description" <?php selected($log['target'] ?? '', 'description') ?>>Description</option>
                            <option value="summary" <?php selected($log['target'] ?? '', 'summary') ?>>Résumé</option>
                            <option value="is" <?php selected($log['target'] ?? '', 'is') ?>>IS</option>
                            <option value="character" <?php selected($log['target'] ?? '', 'character') ?>>Personnage</option>
                            <option value="one rapport" <?php selected($log['target'] ?? '', 'one rapport') ?>>Rapport</option>
                        </select>
                    </td>

                    <td class="target-field">

                        <?php if (($log['target'] ?? '') === 'character'): ?>

                            <select name="logs[<?php echo $i ?>][target_id]">
                                <option value="">—</option>

                                <?php foreach ($characters as $c): ?>
                                    <option value="<?php echo $c->ID ?>"
                                        <?php selected($log['target_id'] ?? '', $c->ID) ?>>
                                        <?php echo esc_html($c->post_title) ?>
                                    </option>
                                <?php endforeach ?>

                            </select>

                        <?php elseif (($log['target'] ?? '') === 'is'): ?>

                            <select name="logs[<?php echo $i ?>][target_id]">
                                <option value="">—</option>

                                <?php foreach ($is_posts as $is): ?>
                                    <option value="<?php echo $is->ID ?>"
                                        <?php selected($log['target_id'] ?? '', $is->ID) ?>>
                                        <?php echo esc_html($is->post_title) ?>
                                    </option>
                                <?php endforeach ?>

                            </select>

                        <?php endif ?>

                    </td>

                    <td>
                        <select name="logs[<?php echo $i ?>][character]">
                            <option value="">—</option>
                            <?php foreach ($characters as $c): ?>
                                <option value="<?php echo $c->ID ?>"
                                    <?php selected($log['character'] ?? '', $c->ID) ?>>
                                    <?php echo esc_html($c->post_title) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </td>

                    <td>
                        <button type="button" class="remove-log button">✕</button>
                    </td>

                </tr>

            <?php endforeach ?>

        </tbody>
    </table>

    <p>
        <button type="button" class="button" id="add-log">Ajouter une ligne</button>
    </p>

    <style>
        #folder-log-table :is(input, select) {
            width: 100%;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const table = document.querySelector('#folder-log-table tbody')
            const addBtn = document.querySelector('#add-log')

            addBtn.addEventListener('click', function() {

                let index = table.children.length

                let row = `
<tr>

<td>
<input type="datetime-local" name="logs[${index}][datetime]">
</td>

<td>
<select name="logs[${index}][action]">
<option value="create">Créer</option>
<option value="update">Modifier</option>
<option value="delete">Supprimer</option>
<option value="join">Relier</option>
<option value="unlink">Délier</option>
<option value="read">Lire</option>
</select>
</td>

<td>
<select name="logs[${index}][target]">
<option value="">Ce Dossier</option>
<option value="roles_allowed">Roles Autorisés</option>
<option value="description">Description</option>
<option value="summary">Résumé</option>
<option value="is">IS</option>
<option value="character">Personnage</option>
<option value="one rapport">Rapport</option>
</select>
</td>

<td class="target-field"></td>

<td>
<select name="logs[${index}][character]">
<option value="">—</option>

<?php foreach ($characters as $c): ?>
<option value="<?php echo $c->ID ?>">
<?php echo esc_js($c->post_title) ?>
</option>
<?php endforeach ?>

</select>
</td>

<td>
<button type="button" class="remove-log button">✕</button>
</td>

</tr>
`

                table.insertAdjacentHTML('beforeend', row)

            })

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-log')) {
                    e.target.closest('tr').remove()
                }
            })

        })

        function updateTargetField(row) {

            const targetSelect = row.querySelector('[name*="[target]"]')
            const target = targetSelect.value
            const field = row.querySelector('.target-field')

            const name = targetSelect.name.replace('[target]', '')

            if (target === "character") {

                field.innerHTML = `<select name="${name}[target_id]">
<option value="">—</option>
<?php foreach ($characters as $c): ?>
<option value="<?php echo $c->ID ?>">
<?php echo esc_js($c->post_title) ?>
</option>
<?php endforeach ?>
</select>`

            } else if (target === "is") {

                field.innerHTML = `<select name="${name}[target_id]">
<option value="">—</option>
<?php foreach ($is_posts as $is): ?>
<option value="<?php echo $is->ID ?>">
<?php echo esc_js($is->post_title) ?>
</option>
<?php endforeach ?>
</select>`

            } else {

                field.innerHTML = ''

            }

        }

        document.addEventListener('change', function(e) {

            if (e.target.name.includes('[target]')) {

                const row = e.target.closest('tr')
                updateTargetField(row)

            }

        })
    </script>

<?php
}


function save_metabox_folder($post_id)
{

    // Sécurité
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (
        !isset($_POST['folder_metabox_nonce']) ||
        !wp_verify_nonce($_POST['folder_metabox_nonce'], 'save_folder_metabox')
    ) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'folder') return;


    if (isset($_POST['roles_allowed']) && is_array($_POST['roles_allowed'])) {
        $roles = array_map('sanitize_text_field', $_POST['roles_allowed']);
        update_post_meta($post_id, '_roles_allowed', $roles);
    } else {
        delete_post_meta($post_id, '_roles_allowed');
    }

    if (isset($_POST['id'])) {
        update_post_meta($post_id, '_id', sanitize_text_field($_POST['id']));
    }

    if (isset($_POST['date_opening'])) {
        update_post_meta($post_id, '_date_opening', sanitize_text_field($_POST['date_opening']));
    }

    if (isset($_POST['date_closing'])) {
        update_post_meta($post_id, '_date_closing', sanitize_text_field($_POST['date_closing']));
    }

    if (isset($_POST['date_last_update'])) {
        update_post_meta($post_id, '_date_last_update', sanitize_text_field($_POST['date_last_update']));
    }

    if (isset($_POST['description'])) {
        update_post_meta($post_id, '_description', sanitize_textarea_field($_POST['description']));
    }

    if (isset($_POST['linked_character']) && is_array($_POST['linked_character'])) {
        $roles = array_map('sanitize_text_field', $_POST['linked_character']);
        update_post_meta($post_id, '_linked_character', $roles);
    } else {
        delete_post_meta($post_id, '_linked_character');
    }

    // Sauvegarde relation bidirectionnelle IS <-> Folder
    if (isset($_POST['linked_is']) && is_array($_POST['linked_is'])) {
        $new_linked_is = array_map('intval', $_POST['linked_is']);
    } else {
        $new_linked_is = [];
    }

    // Anciennes relations
    $old_linked_is = get_post_meta($post_id, '_linked_is', true);
    $old_linked_is = is_array($old_linked_is) ? $old_linked_is : [];

    // 1️⃣ Mettre à jour le côté folder
    if (!empty($new_linked_is)) {
        update_post_meta($post_id, '_linked_is', $new_linked_is);
    } else {
        delete_post_meta($post_id, '_linked_is');
    }

    // 2️⃣ Supprimer le folder des anciens IS non conservés
    $removed_is = array_diff($old_linked_is, $new_linked_is);

    foreach ($removed_is as $is_id) {

        $folders = get_post_meta($is_id, '_linked_folders', true);
        $folders = is_array($folders) ? $folders : [];

        $folders = array_diff($folders, [$post_id]);

        if (!empty($folders)) {
            update_post_meta($is_id, '_linked_folders', $folders);
        } else {
            delete_post_meta($is_id, '_linked_folders');
        }
    }

    // 3️⃣ Ajouter le folder aux nouveaux IS
    $added_is = array_diff($new_linked_is, $old_linked_is);

    foreach ($added_is as $is_id) {

        $folders = get_post_meta($is_id, '_linked_folders', true);
        $folders = is_array($folders) ? $folders : [];

        if (!in_array($post_id, $folders)) {
            $folders[] = $post_id;
            update_post_meta($is_id, '_linked_folders', $folders);
        }
    }

    // LOGS
    if (isset($_POST['roles_allowed_logs']) && is_array($_POST['roles_allowed_logs'])) {
        $roles = array_map('sanitize_text_field', $_POST['roles_allowed_logs']);
        update_post_meta($post_id, '_roles_allowed_logs', $roles);
    } else {
        delete_post_meta($post_id, '_roles_allowed_logs');
    }

    if (!isset($_POST['folder_logs_nonce'])) return;
    if (!wp_verify_nonce($_POST['folder_logs_nonce'], 'save_folder_logs')) return;

    if (!current_user_can('edit_post', $post_id)) return;

    $logs = [];

    if (!empty($_POST['logs'])) {

        foreach ($_POST['logs'] as $log) {

            $logs[] = [
                'datetime' => sanitize_text_field($log['datetime']),
                'action' => sanitize_text_field($log['action']),
                'target' => sanitize_text_field($log['target']),
                'target_id' => intval($log['target_id'] ?? 0),
                'character' => intval($log['character'])
            ];
        }

        usort($logs, function ($a, $b) {
            return strtotime($a['datetime']) <=> strtotime($b['datetime']);
        });
    }

    update_post_meta($post_id, '_folder_logs', $logs);
}
add_action('save_post_folder', 'save_metabox_folder');

// Colonnes tableau backoffice
function folder_admin_columns($columns)
{

    $new_columns = [];

    foreach ($columns as $key => $label) {

        $new_columns[$key] = $label;

        if ($key === 'title') {
            $new_columns['id'] = 'Identifiant';
            $new_columns['linked_is'] = 'IS associés';
            $new_columns['linked_character'] = 'Personnages associés';
            $new_columns['roles_allowed'] = 'Rôles autorisés';
        }
    }

    return $new_columns;
}
add_filter('manage_folder_posts_columns', 'folder_admin_columns');

// Contenu des colonnes du tableau en backoffice
function folder_admin_column_content($column, $post_id)
{

    if ($column === 'id') {

        $id = get_post_meta($post_id, '_id', true);

        echo $id ? esc_html($id) : '—';
    }

    if ($column === 'roles_allowed') {

        $roles = get_post_meta($post_id, '_roles_allowed', true);

        if (!empty($roles)) {
            echo esc_html(implode(', ', $roles));
        } else {
            echo '—';
        }
    }

    if ($column === 'linked_is') {

        $linked_is = get_post_meta($post_id, '_linked_is', true);

        if (!empty($linked_is)) {

            $links = [];

            foreach ($linked_is as $is_id) {

                $title = get_the_title($is_id);
                $url   = get_edit_post_link($is_id);

                $links[] = '<a href="' . esc_url($url) . '">' . esc_html($title) . '</a>';
            }

            echo implode(', ', $links);
        } else {
            echo '—';
        }
    }

    if ($column === 'linked_character') {

        $linked_character = get_post_meta($post_id, '_linked_character', true);

        if (!empty($linked_character)) {

            $links = [];

            foreach ($linked_character as $character_id) {

                $title = get_the_title($character_id);
                $url   = get_edit_post_link($character_id);

                $links[] = '<a href="' . esc_url($url) . '">' . esc_html($title) . '</a>';
            }

            echo implode(', ', $links);
        } else {
            echo '—';
        }
    }
}
add_action('manage_folder_posts_custom_column', 'folder_admin_column_content', 10, 2);

// function add_folder_caps() {
//     $role = get_role('administrator');
//     // $role = get_role('editor');

//     $caps = [
//         'edit_folder',
//         'read_folder',
//         'delete_folder',
//         'edit_folders',
//         'edit_others_folders',
//         'publish_folders',
//         'read_private_folders',
//         'delete_folders',
//         'delete_private_folders',
//         'delete_published_folders',
//         'delete_others_folders',
//         'edit_private_folders',
//         'edit_published_folders',
//     ];

//     foreach ($caps as $cap) {
//         $role->add_cap($cap);
//     }
// }
// add_action('admin_init', 'add_folder_caps');

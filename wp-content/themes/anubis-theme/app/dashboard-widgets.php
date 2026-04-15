<?php

add_action('init', function () {
    if (isset($_GET['private_video'])) {

        $video = basename($_GET['private_video']);
        $path = WP_CONTENT_DIR . '/private-videos/' . $video;

        // Vérifier si l'utilisateur est connecté ET a le rôle admin ou éditeur
        if (current_user_can('edit_others_posts') && file_exists($path)) {
            header('Content-Type: video/mp4');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        } else {
            wp_die('Accès interdit', 'Interdit', ['response' => 403]);
        }
    }
});

add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget(
        'dashboard_comptes',
        'Liste des Comptes des Personnages',
        function () {
?>
        <p>3 comptes personnages existent actuellement avec des rôles différents. Ces comptes servent aux utilisateurs pour qu'ils puissent s'y connecter et consulter le site Anubis "comme s'ils avaient piraté le compte du personnage". Chaque rôle (donc chaque compte) permet d'accéder à des contenus différents suivant ce qui a été défini dans chacun de ces derniers.</p>
        <p>Voici la liste des comptes avec leurs rôles et identifiants pour que vous puissiez tester que chaque personne ai bien accès ou non aux bons contenus.</p>
        <p>Plus le rôle est bas, plus il est censé donner accès à davantage de contenu.</p>

        <ul>
            <li>
                Rôle&nbsp;:&nbsp;<strong>Agent</strong>
                <ul style="margin-left: 20px;">
                    <li>
                        Identifiant&nbsp;:&nbsp;<strong>0505</strong> <br/>
                    </li>
                    <li>
                        Mdp&nbsp;:&nbsp;<strong>kcrYxjI3GOjC2sK7</strong>
                    </li>
                </ul>
            </li>
            <br/>
            <li>
                Rôle&nbsp;:&nbsp;<strong>Super Agent</strong>
                <ul style="margin-left: 20px;">
                    <li>
                        Identifiant&nbsp;:&nbsp;<strong>2304</strong> <br/>
                    </li>
                    <li>
                        Mdp&nbsp;:&nbsp;<strong>JR1s31KoBBFplqNA</strong>
                    </li>
                </ul>
            </li>
            <br/>
            <li>
                Rôle&nbsp;:&nbsp;<strong>Snhffr Crefbaar ("Fausse Personne" en Code César)</strong>
                <ul style="margin-left: 20px;">
                    <li>
                        Identifiant&nbsp;:&nbsp;<strong>8567</strong> <br/>
                    </li>
                    <li>
                        Mdp&nbsp;:&nbsp;<strong>aZxfyiG61faELCGe</strong>
                    </li>
                </ul>
            </li>
            <br/>
            <li>
                Rôle&nbsp;:&nbsp;<strong>Directeur</strong>
                <ul style="margin-left: 20px;">
                    <li>
                        Identifiant&nbsp;:&nbsp;<strong>9273</strong> <br/>
                    </li>
                    <li>
                        Mdp&nbsp;:&nbsp;<strong>V6qcnh8DLkxWTuu0</strong>
                    </li>
                </ul>
                <span>Il est inutile de mettre du contenu accessible uniquement pour ce rôle car dès que l'utilisateur se connecte à ce compte, il aura un screamer et sera déconnecté.</span>
            </li>
        </ul>

        <br/>

        <p>Les comptes ne peuvent être géré que par l'administrateur.</p>

        <p>Mise à jour le 15/04/2026</p>
<?php
        }
    );
});

add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget(
        'dashboard_guide',
        'Définition des Contenus',
        function () {
?>
        <p>Ce site possède différents types de contenus. Voici la liste de ces derniers ainsi que leurs utilités&nbsp;:</p>

        <ul>
            <li><strong>Médias&nbsp;:&nbsp;</strong>Il s'agit des différentes images utilisées sur le site pour illustrer les <strong>I.S.</strong>, les <strong>Personnages</strong>... Vous n'avez pas besoin d'y enregistrer vos images avant d'editer un contenu, vous pouvez les importer directement dans les pages d'édition des différents contenus. Vous pouvez renseigner des <strong>Légendes</strong> à vos images, cela fera du texte caché !</li>
            <li><strong>Pages&nbsp;:&nbsp;</strong>Contenu de base utilisé seulement pour la page d'accueil et la page de profil. MERCI DE NE PAS LES SUPPRIMER SINON LE SITE SERA CASSÉ. Bien que vous puissiez en ajouter, le site n'a pas été prévu pour. Le seul moyen de rendre vos futures pages accessibles sera d'ajouter un bouton directement dans la page d'accueil.</li>
            <li><strong>Personnages&nbsp;:&nbsp;</strong>Il s'agit des différents profils visibles sur le site, ou autrement dit les membres d'Anubis. Ces derniers peuvent être reliés à un compte (les comptes sont gérés par l'administrateur) afin que les utilisateurs puissent se connecter sur le compte de ce personnage et avoir accès au même contenu que lui. Les personnages peuvent être reliés à des <strong>Dossiers</strong> pour indiquer qu'ils ont participé à telle ou telle affaire.</li>
            <li><strong>I.S.&nbsp;:&nbsp;</strong>Il s'agit des différents monstres répertoriés par Anubis. Faites-vous plaisir et mettez tout ce que vous voulez ! (tant que ce n'est pas offensant) Il serait idéal que chacun soit relié à un <strong>Dossier</strong> pour plus de cohérence, même si le <strong>Dossier</strong> en lui même est inaccessible. Même si vous n'avez pas de beau modèl 3D pour illustrer un <strong>I.S.</strong> vous pouvez utiliser un croquis, pour faire comme un "portrait robot" si l'<strong>I.S.</strong> n'a pas encore été capturé ;).</li>
            <li><strong>Dossiers&nbsp;:&nbsp;</strong>Il s'agit des différentes affaires effectuées par Anubis (par exemple le <strong>Dossier Schwach</strong>). Ces derniers regroupent beaucoup d'informations car c'est via ce contenu que des <strong>I.S.</strong> et des <strong>Personnages</strong> peuvent être liés.</li>
            <li><strong>Rapport&nbsp;:&nbsp;</strong>Il s'agit du détail, heure par heure, voir par minutes, d'un <strong>Dossier</strong>. Les événements de l'affaire doivent y être très détaillés.</li>
            <li><strong>Messages&nbsp;:&nbsp;</strong>Via cet onglet, vous pourrez créer des conversations entre 2 <strong>Personnages</strong>. Ces dernières seront visibles lorsque l'utilisateur est connecté à un compte relié à l'un des deux personnages. Il n'est donc pas utile de créer une conversation entre 2 <strong>Personnages</strong> si aucun d'eux n'est relié à un compte.</li>
        </ul>

        <p>Mise à jour le 25/03/2026</p>
<?php
        }
    );
});

add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget(
        'dashboard_tutorials',
        'Vidéos Tutoriels',
        function () {
?>
        <p>Voici quelques vidéos pour vous aider à administrer les contenus&nbsp;:</p>

        <ul>
            <li>
                <strong>Créer un I.S.</strong><br>
                <video width="100%" height="200" controls>
                    <source src="<?php echo esc_url(home_url('?private_video=create-is.mp4')); ?>" type="video/mp4">
                </video>
            </li>
            <li>
                <strong>Créer un Personnage</strong><br>
                <video width="100%" height="200" controls>
                    <source src="<?php echo esc_url(home_url('?private_video=create-character.mp4')); ?>" type="video/mp4">
                </video>
            </li>
            <li>
                <strong>Créer un Dossier</strong><br>
                <video width="100%" height="200" controls>
                    <source src="<?php echo esc_url(home_url('?private_video=create-folder.mp4')); ?>" type="video/mp4">
                </video>
            </li>
            <li>
                <strong>Créer un Rapport pour un Dossier</strong><br>
                <video width="100%" height="200" controls>
                    <source src="<?php echo esc_url(home_url('?private_video=create-rapport.mp4')); ?>" type="video/mp4">
                </video>
            </li>
            <li>
                <strong>Ajouter un Historique aux Dossiers et I.S.</strong><br>
                <video width="100%" height="200" controls>
                    <source src="<?php echo esc_url(home_url('?private_video=add-logs.mp4')); ?>" type="video/mp4">
                </video>
            </li>
            <li>
                <strong>Créer des messages</strong><br>
                <video width="100%" height="200" controls>
                    <source src="<?php echo esc_url(home_url('?private_video=create-messages.mp4')); ?>" type="video/mp4">
                </video>
            </li>
        </ul>

        <p>Mise à jour le 25/03/2026</p>
<?php
        }
    );
});

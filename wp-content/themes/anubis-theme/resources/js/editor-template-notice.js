wp.domReady(() => {
    const { select, subscribe, dispatch } = wp.data;
    const noticeStore = dispatch('core/notices');

    // Messages pour chaque template
    const templatesMessages = {
        'templates/home.blade.php': 'Cette page est utilisée pour la page d\'accueil. Editez la soignement, il s\'agit de la première page visité par les utilisateur ;)',
        'templates/profil.blade.php': 'Cette page est utilisée pour afficher le profil de l\'utilisateur connecté. Aucun contenu renseigné ici ne sera pris en compte sur la page.',
        'templates/eject.blade.php': 'Cette page est utilisée lorsque l\'utilisateur se connecte sur le compte du Directeur. Aucun contenu renseigné ici ne sera pris en compte sur la page.',
    };

    let previousTemplate = null;
    const noticeId = 'page-template-notice';

    function showNotice(template) {
        noticeStore.removeNotice(noticeId);

        if (templatesMessages[template]) {
            noticeStore.createNotice(
                'warning',
                templatesMessages[template],
                {
                    id: noticeId,
                    isDismissible: false,
                }
            );
        }
    }

    // Fonction d’écoute du changement de template
    subscribe(() => {
        const currentTemplate = select('core/editor').getEditedPostAttribute('template');
        if (currentTemplate !== previousTemplate) {
            previousTemplate = currentTemplate;
            showNotice(currentTemplate);
        }
    });

    // Initialisation au chargement
    const initialTemplate = select('core/editor').getEditedPostAttribute('template');
    showNotice(initialTemplate);
});
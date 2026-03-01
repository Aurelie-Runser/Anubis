jQuery(function($) {
    let frame;

    $('#upload_gallery_button').on('click', function(e) {
        e.preventDefault();


        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Sélectionner des images',
            button: {
                text: 'Utiliser ces images'
            },
            multiple: 'add'
        });

        // Avant d'ouvrir la frame, on sélectionne les images déjà choisies
        frame.on('open', function() {
            const selection = frame.state().get('selection');

            // Récupère les IDs déjà sélectionnés dans l'input caché
            const existingIds = $('#galerie_input').val() ? $('#galerie_input').val().split(',') : [];
                    
            existingIds.forEach(function(id) {
                const attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add(attachment ? attachment : []);
            });
        });

        frame.on('select', function() {
            const selection = frame.state().get('selection');
            const ids = [];

            // On vide l'affichage pour réafficher toutes les images sélectionnées (anciennes + nouvelles)
            const previewContainer = $('#gallery_preview');
            previewContainer.empty();

            selection.each(function(attachment) {
                attachment = attachment.toJSON();
                ids.push(attachment.id);

                const imgSrc = (attachment.sizes?.thumbnail?.url)
                    || (attachment.sizes?.medium?.url)
                    || attachment.url;

                previewContainer.append('<span style="margin-right:5px;"><img style="width:150px; height:150px; object-fit:cover" src="' + imgSrc + '" /></span>');
            });

            // On met à jour l'input caché avec tous les IDs sélectionnés
            $('#galerie_input').val(ids.join(','));
        });

        frame.open();
    });
});

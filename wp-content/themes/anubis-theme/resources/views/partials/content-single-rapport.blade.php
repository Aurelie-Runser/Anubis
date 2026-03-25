@php
$post_id = get_the_ID();

if (!is_user_allowed_for_content($post_id)) {
    wp_redirect( get_post_type_archive_link( get_post_type() ) );
    return;
};

// Récupération des métas
$meta = [
    'date_publish' => get_post_meta($post_id, '_date_publish', true),
    'linked_folder' => get_post_meta($post_id, '_linked_folder', true),
    'rapport_author' => get_post_meta($post_id, '_rapport_author', true),
];

@endphp

<a class="content-single-return" href="{{ get_permalink( $meta['linked_folder'] ) }}">Retourner au Dossier N°{!! $meta['linked_folder'] !!}</a>

<article @php(post_class('h-entry'))>
    <div class="e-content">
        <div class="content">
            <header>
                <h1 class="p-name">
                    Rapport Numéro&nbsp;:&nbsp;{!! get_the_title() !!}
                </h1>
            </header>

            <ul class="list-short_infos">
                <li>Date de publication&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_publish'])) !!}</span></li>
                <li>Auteur&nbsp;:&nbsp;<span>{!! display_meta($meta['rapport_author'] . ' - ' . get_the_title($meta['rapport_author'])) !!}</span></li>
            </ul>

        </div>

    </div>
</article>
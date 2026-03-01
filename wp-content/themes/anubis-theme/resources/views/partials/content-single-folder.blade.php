@php
$post_id = get_the_ID();

if (!is_user_allowed_for_content($post_id)) {
    wp_redirect( get_post_type_archive_link( get_post_type() ) );
    return;
};


// Récupération des métas
$meta = [
    'date_opening' => get_post_meta($post_id, '_date_opening', true),
    'date_closing'    => get_post_meta($post_id, '_date_closing', true),
    'date_last_update'    => get_post_meta($post_id, '_date_last_update', true),
    'description'   => wpautop(wp_kses_post(get_post_meta($post_id, '_description', true))),
];

$linked_is = get_post_meta($post_id, '_linked_is', true);
$linked_is = is_array($linked_is) ? $linked_is : [];

if(!empty($linked_is)) {
    $is_query = new WP_Query([
        'post_type' => 'is',
        'post__in'  => $linked_is,
        'orderby'   => 'post__in',
        'posts_per_page' => -1,
    ]);
}

@endphp

<a class="content-single-return" href="{{ get_post_type_archive_link( get_post_type() ) }}">Retourner à la liste des {!! get_post_type_object( get_post_type() )->label !!}</a>

<article @php(post_class('h-entry'))>
    <div class="e-content">
        <div class="content">
            <header>
                <h1 class="p-name">
                    Dossier Numéro&nbsp;:&nbsp;{!! get_the_title() !!}
                </h1>
            </header>

            <ul class="list-short_infos">               
                <li>Date d’ouverture&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_opening'])) !!}</span></li>

                <li>Date de fermeture&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_closing'])) !!}</span></li>

                <li>Date de la dernière modification&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_last_update'])) !!}</span></li>
            </ul>

            <h2>Résumé&nbsp;:</h2>
            @if(!empty($meta['description']))
                {!! $meta['description'] !!}
            @else
                {!! display_meta('') !!}
            @endif

            <h2>I.S. Liés&nbsp;:</h2>

            @if( !empty($is_query) && $is_query->have_posts())
                <ul class="linked-is-list">
                    @while($is_query->have_posts()) @php($is_query->the_post())
                        <li>
                            <a href="{{ get_permalink() }}">
                                {!! get_the_title() !!}
                            </a>
                        </li>
                    @endwhile
                </ul>

                @php(wp_reset_postdata())

            @else
                <x-alert type="default">
                    Aucun {!! get_post_type_object( "is" )->label !!} associé à ce {!! get_post_type_object( get_post_type() )->labels->singular_name !!}.
                </x-alert>
            @endif


            <h2>Historique</h2>
            <x-alert type="default">
                En cours de dev. Ca arrive bientôt ;)
            </x-alert>

        </div>

    </div>
</article>

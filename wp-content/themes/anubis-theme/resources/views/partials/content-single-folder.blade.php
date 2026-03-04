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

$linked_character = get_post_meta($post_id, '_linked_character', true);
$linked_character = is_array($linked_character) ? $linked_character : [];

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

            <div class="list-groupe">
                <div>
                    <h2>I.S. Liés&nbsp;:</h2>
                    @if(!empty($linked_is))
                        <ul class="list">
                            @foreach($linked_is as $is_id)
                                <li>
                                    <a href="{{ get_permalink($is_id) }}">
                                        {!! get_the_title($is_id) !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <x-alert type="default">
                            Aucun {!! get_post_type_object( "is" )->labels->singular_name !!} associé à ce {!! get_post_type_object( get_post_type() )->labels->singular_name !!}.
                        </x-alert>
                    @endif
                </div>
                <div>
                    <h2>Personnels Liés&nbsp;:</h2>
                    @if(!empty($linked_character))
                        <ul class="list">
                            @foreach($linked_character as $is_character)
                                <li>
                                    <a href="{{ get_permalink($is_character) }}">
                                        {!! get_the_title($is_character) !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <x-alert type="default">
                            Aucun Personnel associé à ce {!! get_post_type_object( get_post_type() )->labels->singular_name !!}.
                        </x-alert>
                    @endif
                </div>
                <div>
                    <h2>Rapports&nbsp;:</h2>
                    <x-alert type="default">
                        Aucun Rapport associé à ce {!! get_post_type_object( get_post_type() )->labels->singular_name !!}.
                    </x-alert>
                </div>
            </div>


            <h2>Historique</h2>
            <x-alert type="default">
                En cours de dev. Ca arrive bientôt ;)
            </x-alert>

        </div>

    </div>
</article>

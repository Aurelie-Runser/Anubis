@php
$post_id = get_the_ID();

if (!is_user_allowed_for_content($post_id)) {
    wp_redirect( get_post_type_archive_link( get_post_type() ) );
    return;
};


// Récupération des métas
$meta = [
    'id'            => get_post_meta($post_id, '_id', true),
    'name'          => get_post_meta($post_id, '_name', true),
    'etat'          => get_post_meta($post_id, '_etat', true),
    'date_discover' => get_post_meta($post_id, '_date_discover', true),
    'date_catch'    => get_post_meta($post_id, '_date_catch', true),
    'capacities'    => get_post_meta($post_id, '_capacities', true),
    'description'   => wpautop(wp_kses_post(get_post_meta($post_id, '_description', true))),
    'galerie'       => get_post_meta($post_id, '_galerie', true),
];

// Taxonomie
$categories = get_the_terms($post_id, 'is_category');

// Capacités
$capacities_items = $meta['capacities'] ? array_map('trim', explode("\n", $meta['capacities'])) : [];

// Dossiers
$linked_folders = get_post_meta($post_id, '_linked_folders', true);
$linked_folders = is_array($linked_folders) ? $linked_folders : [];
@endphp

<a class="content-single-return" href="{{ get_post_type_archive_link( get_post_type() ) }}">Retourner à la liste des {!! get_post_type_object( get_post_type() )->label !!}</a>

<article @php(post_class('h-entry'))>
    <div class="e-content">
        <div class="content">
            <header>
                <h1 class="p-name">
                    I.S. Numéro&nbsp;:&nbsp;{!! display_meta($meta['id']) !!}
                </h1>
            </header>

            <ul class="list-short_infos">
                <li>Nom Vernaculaire&nbsp;:&nbsp;<span>{!! display_meta($meta['name']) !!}<span></li>

                <li>Niveau de dangerosité&nbsp;:&nbsp;<span>
                    @if($categories && !is_wp_error($categories))
                        @foreach($categories as $category)
                            <strong>
                                {!! $category->name !!}
                            </strong>@if(!$loop->last), @endif
                        @endforeach
                    @else
                        {!! display_meta('') !!}
                    @endif
                </span> </li>

                <li>État&nbsp;:&nbsp;<span>{!! display_meta( is_etat_label($meta['etat']) ?? $meta['etat']) !!}</span></li>
                
                <li>Date de la dernière capture&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_catch'])) !!}</span></li>
                
                <li>Date de découverte&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_discover'])) !!}</span></li>
            </ul>

            <h2>Capacités</h2>
            @if(!empty($capacities_items))
                <ul class="list">
                    @foreach($capacities_items as $item)
                        <li>{!! $item !!}</li>
                    @endforeach
                </ul>
            @else
                {!! display_meta('') !!}
            @endif

            <h2>Description</h2>
            @if(!empty($meta['description']))
                {!! $meta['description'] !!}
            @else
                {!! display_meta('') !!}
            @endif

            <h2>Dossiers Liés :</h2>
            @if(!empty($linked_folders))
                <ul class="list">
                    @foreach($linked_folders as $folder_id)
                        <li>
                            <a href="{{ get_permalink($folder_id) }}">
                                {!! get_the_title($folder_id) !!}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <x-alert type="default">
                    Aucun {!! get_post_type_object( "folder" )->labels->singular_name !!} associé à cet {!! get_post_type_object( get_post_type() )->labels->singular_name !!} .
                </x-alert>
            @endif

            <h2>Historique</h2>
            <x-alert type="default">
                En cours de dev. Ca arrive bientôt ;)
            </x-alert>

        </div>

        <?php if ($meta['galerie']) :
            $ids = explode(',', $meta['galerie']);
        ?>
            <div class="gallery">
                <?php foreach ($ids as $img_id) : 
                    $description = get_post_field('post_content', $img_id);
                ?>
                    <figure>
                        <?php 
                        echo wp_get_attachment_image(
                            $img_id, 
                            'medium', 
                            false, 
                            ['alt' => esc_attr($description)]
                        ); 
                        ?>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</article>

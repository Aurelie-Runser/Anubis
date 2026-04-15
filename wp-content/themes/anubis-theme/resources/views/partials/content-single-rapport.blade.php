@php
$post_id = get_the_ID();

if (!is_user_allowed_for_content($post_id)) {
    wp_redirect( get_post_type_archive_link( get_post_type() ) );
    return;
};

// Récupération des métas
$meta = [
    'date_rapport' => get_post_meta($post_id, '_date_rapport', true),
    'linked_folder' => get_post_meta($post_id, '_linked_folder', true),
    'rapport_author' => get_post_meta($post_id, '_rapport_author', true),
    'galerie'       => get_post_meta($post_id, '_galerie', true),
];
    
$author_id = explode('-', get_the_title());
$author_id = $author_id[ count($author_id)-1 ];

$steps = get_post_meta($post_id, '_rapport_steps', true);

@endphp

<a class="content-single-return" href="{{ get_permalink( $meta['linked_folder'] ) }}">Retourner au Dossier N°{!! get_post_meta($meta['linked_folder'], '_id', true) !!}</a>

<article @php(post_class('h-entry'))>
    <div class="e-content">
        <div class="content">
            <header>
                <h1 class="p-name">
                    Rapport&nbsp;:&nbsp;{!! display_meta( get_the_title() ) !!}
                </h1>
            </header>

            <ul class="list-short_infos">
                <li>Jour des événements&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_rapport'])) !!}</span></li>
                <li>Auteur&nbsp;:&nbsp;<span>{!! display_meta( $author_id . ' - ' . get_the_title($meta['rapport_author'])) !!}</span></li>
            </ul>

            <h2>Déroulé</h2>
            @if(!empty($steps))
                <ul class="list list-rapport-steps">
                    @foreach($steps as $step)
                        <li>
                            <span>
                                <strong>{{ $step['time'] }}</strong>&nbsp;:&nbsp;<span>{!! $step['content'] !!}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif

        </div>

        <?php if ($meta['galerie']) :
            $ids = explode(',', $meta['galerie']);
        ?>
            <div class="gallery">
                <?php foreach ($ids as $media_id) : 
    $description = get_post_field('post_content', $media_id);
    $mime_type = get_post_mime_type($media_id);
    $url = wp_get_attachment_url($media_id);
?>
    <figure>
        <?php 
        if (str_starts_with($mime_type, 'image/')) {

            echo wp_get_attachment_image(
                $media_id, 
                'medium', 
                false, 
                ['alt' => esc_attr($description)]
            );

        } elseif (str_starts_with($mime_type, 'video/')) {

            ?>
            <video controls width="500">
                <source src="<?= esc_url($url) ?>" type="<?= esc_attr($mime_type) ?>">
                Votre navigateur ne supporte pas la vidéo.
            </video>
            <?php

        } elseif ($mime_type === 'application/pdf') {
            $url = wp_get_attachment_url($media_id);
            $preview = wp_get_attachment_image_src($media_id, 'medium');
            ?>
            <a class="pdf-preview" href="<?= esc_url($url) ?>" target="_blank">
                <div class="pdf-box">
                    <?php if ($preview) : ?>
                        <img class="pdf-img" src="<?= esc_url($preview[0]) ?>" alt="Preview PDF">
                    <?php else : ?>
                        <img class="pdf-eye" src="{{ Vite::asset('resources/images/eye.svg') }}" aria-hidden="true">
                        <span class="pdf-text">Dossier Confidentiel</span>
                    <?php endif; ?>

                    <span class="pdf-title">{!! get_the_title($media_id) !!}</span>
                </div>
            </a>
            <?php
        }
        ?>
    </figure>
<?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</article>
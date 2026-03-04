{{--
  Template Name: Page profil
--}}

@php
    $current_user_id = get_current_user_id();

    $character_query = new WP_Query([
    'post_type'      => 'character',
    'meta_key'       => '_linked_user',
    'meta_value'     => $current_user_id,
    'posts_per_page' => 1,
    ]);

    $character = $character_query->have_posts() ? $character_query->posts[0] : null;

    $post_id = $character->ID;
@endphp

@extends('layouts.app')

@section('content')

@if($character)
    @include('partials.content-single-character')
@else
    <p>Votre compte n'a pas été trouvé.</p>
@endif

<div class="btn-logout">
    <a href="<?= wp_logout_url(home_url('/login')) ?>" class="btn">
        Déconnexion
    </a>
</div>
@endsection
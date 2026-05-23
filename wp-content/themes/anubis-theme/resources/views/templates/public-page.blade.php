{{--
  Template Name: Page publique
--}}
    
@extends('layouts.app')
@section('content')

<header class="alignfull home-header">
    <div class="contentwide">
        <a href="{{ home_url() }}" aria-label="Accueil">
            <img src="{{ Vite::asset('resources/images/logo-black.png') }}" alt="logo d'Anubis">
        </a>

        <a href="{{ home_url('/#contact') }}" class="btn">
            Nous contacter
        </a>
    </div>
</header>

@php(the_content())

<footer class="alignfull home-footer">
    <div class="contentwide">
        <a href="{{ home_url() }}" aria-label="Accueil">
            <img src="{{ Vite::asset('resources/images/logo-black.png') }}" alt="logo d'Anubis">
        </a>

        <ul>
            <li>
                <a href="{{ home_url('/#contact') }}">Contact</a>
            </li>
            <li>
                <a href="{{ home_url('/mention-legales') }}">Mention légales</a>
            </li>
        </ul>

        <p class="copywrite">Propriété d'Anubis</p>
    </div>

</footer>

<a class="link-hidden" href="{{ wp_login_url() }}">Me connecter</a>

@endsection
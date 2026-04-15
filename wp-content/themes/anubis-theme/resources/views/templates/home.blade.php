{{--
  Template Name: Accueil
--}}
    
@extends('layouts.app')
@section('content')

<header class="alignfull home-header">
    <div class="contentwide">
        <a href="#" aria-label="Accueil">
            <img src="{{ Vite::asset('resources/images/logo-black.png') }}" alt="logo d'Anubis">
        </a>

        <a href="#contact" class="btn">
            Nous contacter
        </a>
    </div>
</header>

@php(the_content())

<section id="contact">
    <h2>Contactez-nous</h2>

    <form class="contact-form">
        <input type="text" placeholder="Nom" required>
        <input type="text" placeholder="Prénom" required>

        <input type="email" name="email" id="email" placeholder="E-mail" required>
        <input type="tel" name="telephone" id="telephone" placeholder="Téléphone" required>

        <textarea name="message" id="message" placeholder="Message" required></textarea>

        <button type="submit" class="btn">Envoyer</button>
        
    </form>
</section>

<footer class="alignfull home-footer">
    <div class="contentwide">
        <a href="#" aria-label="Accueil">
            <img src="{{ Vite::asset('resources/images/logo-black.png') }}" alt="logo d'Anubis">
        </a>

        <ul>
            <li>
                <a href="#contact">Contact</a>
            </li>
            <li>
                <a href="#">Mention légales</a>
            </li>
        </ul>

        <p class="copywrite">Propriété d'Anubis</p>
    </div>
</footer>

@endsection
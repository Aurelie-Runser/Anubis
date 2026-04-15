{{--
  Template Name: Accueil
--}}
    
@extends('layouts.app')
@section('content')

<header class="home-header">
    <div class="contentwide">
        <img src="{{ Vite::asset('resources/images/logo-black.png') }}" alt="logo d'Anubis">

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

<footer>
    <div>

    </div>
</footer>

@endsection
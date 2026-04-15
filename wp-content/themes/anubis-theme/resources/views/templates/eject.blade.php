{{--
  Template Name: Page Eject
--}}

@extends('layouts.app')
@section('content')

<section class="eject-section">
  <div class="eject-text">
    <p>Mince, tu n'aurais jamais dû pouvoir venir ici...</p>
    <p>Il ne me reste plus qu'à te trouver ;&#x29;</p>
  </div>

  <img class="eject-is" src="{{ Vite::asset('resources/images/is_ecran.png') }}" alt="l'IS des écrans t'observe">

  <p class="text-hidden">Pour revenir au site "normal" ferme l'onglet et attent 5 secondes.</p>
</section>

@endsection
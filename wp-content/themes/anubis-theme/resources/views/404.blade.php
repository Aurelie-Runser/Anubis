@extends('layouts.app')

@section('content')
  <h1>Erreur 404</h1>

  @if (! have_posts())
    <x-alert type="warning">
      {!! __('La page que vous recherchez n\'existe pas.', 'anubis-theme') !!}
    </x-alert>

    <div style="margin-top: 40px;">
      <a href="<?= home_url('/profil') ?>" class="btn">Retourner à mon profil</a>
    </div>

  @endif
@endsection

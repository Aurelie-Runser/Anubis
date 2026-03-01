@extends('layouts.app')

@section('content')
  @include('partials.page-header')

  @if (! have_posts())
    <x-alert type="warning">
      {!! __('La page que vous recherchez n\'existe pas.', 'anubis-theme') !!}
    </x-alert>

    <div style="margin: 10px auto">
      <a href="{{ get_home_url() }}">Retourner à l'accueil</a>
    </div>

  @endif
@endsection

<!-- Fichier utilisée sur les listes I.S. -->

@extends('layouts.app')


@section('content')
<a class="content-single-return" href="{{ get_home_url() }}">Retourner à l'accueil</a>

@include('partials.archive-header')

@if (have_posts())

<div id="archive_result">
  <table class="archive_table">
    <caption class="sr-only">Tableau des {!! $label_cpt !!}</caption>
    <thead class="archive_table--head">
      <tr>
        @foreach($columns as $meta_key => $title)
        <th scope="col" class="archive_table--head-colname">{{ $title }}</th>
        @endforeach
        <th scope="col" class="archive_table--head-colname">Actions</th>
      </tr>
    </thead>

    <tbody class="archive_table--body">
      @while($query->have_posts()) @php($query->the_post())
        @includeFirst(['partials.content-' . get_post_type(), 'partials.content'])
      @endwhile
    </tbody>

  </table>

  {{ custom_pagination($query) }}
</div>

@endif

@php(wp_reset_postdata())
@endsection
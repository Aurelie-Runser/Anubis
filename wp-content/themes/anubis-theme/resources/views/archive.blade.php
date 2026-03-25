<!-- Fichier utilisée sur les listes I.S. et Folder -->

@extends('layouts.app')


@section('content')

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
        <th scope="col" class="archive_table--head-colname"><span class="sr-only">Actions</span></th>
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

@else

<x-alert type="default">
  Aucun {!! get_post_type_object( "folder" )->labels->singular_name !!}
</x-alert>

@endif

@php(wp_reset_postdata())
@endsection
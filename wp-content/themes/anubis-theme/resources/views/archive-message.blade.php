<!-- Fichier utilisée sur les listes Messages -->
@php
$current_user_id = get_current_user_id();

$character_id = get_posts([
    'post_type'  => 'character',
    'meta_key'   => '_linked_user',
    'meta_value' => $current_user_id,
    'fields'     => 'ids',
    'numberposts'=> 1
]);

$character_id = $character_id[0] ?? 0;

$query = new WP_Query([
    'post_type' => 'message',
    'meta_query' => [
        'relation' => 'OR',
        [
            'key' => '_character_1',
            'value' => $character_id,
            'compare' => 'LIKE'
        ],
        [
            'key' => '_character_2',
            'value' => $character_id,
            'compare' => 'LIKE'
        ]
    ]
]);
@endphp

@extends('layouts.app')

@section('content')

<x-alert type="warning">
  Les messages de plus de 7 jours sont automatiquement supprimés pour des raisons de confidentialités.
</x-alert>

@if ($query->have_posts())

<div id="archive_result">
  <table class="archive_table">
    <caption class="sr-only">Les {!! $label_cpt !!}</caption>
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
  Aucun Message
</x-alert>

@endif

@php(wp_reset_postdata())
@endsection
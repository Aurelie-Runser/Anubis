@php
$post_id = get_the_ID();

$is_allowed = is_user_allowed_for_content($post_id);

$meta = [
    'id'            => get_the_title($post_id),
    'date_opening'  => get_post_meta($post_id, '_date_opening', true),
    'date_closing'  => get_post_meta($post_id, '_date_closing', true),
];

$classes = $is_allowed ? 'archive-item' : 'archive-item is_not_allow';

@endphp

<tr @php(post_class($classes))>
  <th>
    {!! $meta['id'] !!}
  </th>

  <th>
    <time class="dt-published" datetime="{{ $meta['date_opening'] }}">
      {{ format_date_fr( $meta['date_opening'] ) }}
    </time>
  </th>

  <th>
    <time class="dt-published" datetime="{{ $meta['date_closing'] }}">
      {{ format_date_fr( $meta['date_closing'] ) }}
    </time>
  </th>

  <th>
    @if( $is_allowed)
    <a href="{{ get_permalink() }}" class="btn btn-icon" aria-label="consulter la fiche de ce Dossier">
      <img src="{{ Vite::asset('resources/images/eye.svg') }}" aria-hidden="true">
    </a>
    @else
    <button class="btn btn-icon is_not_allowed" aria-label="vous n'avez pas les droits pous consulter la fiche de ce Dossier">
      <img src="{{ Vite::asset('resources/images/eye-close.svg') }}" aria-hidden="true">
    </button>
    @endif
  </th>
</tr>

@php
$post_id = get_the_ID();

$is_allowed = is_user_allowed_for_content($post_id);

$meta = [
    'id'            => get_post_meta($post_id, '_id', true),
    'name'          => get_post_meta($post_id, '_name', true),
    'etat'          => get_post_meta($post_id, '_etat', true),
    'date_discover' => get_post_meta($post_id, '_date_discover', true),
];

$classes = $is_allowed ? 'archive-item' : 'archive-item is_not_allow';

@endphp

<tr @php(post_class($classes))>
  <th scope="row">
    {!! $meta['id'] !!}
  </th>

  <th>
    {!! $meta['name'] !!}
  </th>

  <th>
    <time datetime="{{ $meta['date_discover'] }}">
      {{ format_date_fr( $meta['date_discover'] ) }}
    </time>
  </th>

  <th>
    {!! is_etat_label($meta['etat']) ?? $meta['etat'] !!}
  </th>

  <th>
    @if( $is_allowed)
      <a href="{{ get_permalink() }}" class="btn btn-icon" aria-label="consulter la fiche de cet I.S.">
        <img src="{{ Vite::asset('resources/images/eye.svg') }}" aria-hidden="true">
      </a>
    @else
      <button class="btn btn-icon is_not_allowed" aria-label="vous n'avez pas les droits pous consulter la fiche de cet I.S.">
        <img src="{{ Vite::asset('resources/images/eye-close.svg') }}" aria-hidden="true">
      </button>
    @endif
  </th>
</tr>

@php
$post_id = get_the_ID();

$current_user_id = get_current_user_id();

// récupérer le character du user connecté
$current_character = get_posts([
  'post_type'  => 'character',
  'meta_key'   => '_linked_user',
  'meta_value' => $current_user_id,
  'fields'     => 'ids',
  'numberposts'=> 1
]);

$current_character_id = $current_character[0] ?? 0;

// metas message
$character_1_ID = get_post_meta($post_id, '_character_1', true);
$character_2_ID = get_post_meta($post_id, '_character_2', true);

// déterminer l'autre personnage
$other_character_id = ($character_1_ID == $current_character_id)
  ? $character_2_ID
  : $character_1_ID;

// récupérer le post
$character_post = get_post($other_character_id);
$character_name = $character_post ? $character_post->post_title : '—';

// récupérer identifiant (user_login ou _id)
$linked_user = get_post_meta($other_character_id, '_linked_user', true);
$manual_id   = get_post_meta($other_character_id, '_id', true);

if ($linked_user) {
  $user = get_userdata($linked_user);
  $character_identifiant = $user ? $user->user_login : '—';
} else {
  $character_identifiant = $manual_id ?: '—';
}


$messages = get_post_meta($post_id, '_messages', true);
$messages = is_array($messages) ? $messages : [];

$latest_date = '';

if (!empty($messages)) {

    // On calcule un timestamp pour chaque message
    $timestamps = array_map(function($msg) {
        $day_offset = isset($msg['day_offset']) ? intval($msg['day_offset']) : 0;
        $time       = $msg['time'] ?? '00:00';
        
        // Date actuelle + day_offset
        $date = new DateTime();
        $date->modify($day_offset . ' days');
        // Ajouter l'heure
        [$h, $m] = explode(':', $time);
        $date->setTime(intval($h), intval($m));
        return $date;
    }, $messages);

    // Tri décroissant pour prendre le dernier
    usort($timestamps, function($a, $b) {
        return $b <=> $a;
    });

    $latest_date = $timestamps[0]->format('Y/m/d');
}


$classes = 'archive-item';

@endphp

<tr @php(post_class($classes))>
  <th scope="row">
    {!! $character_identifiant !!} - {!! $character_name !!}
  </th>

  <th>
    {!! $latest_date !!}
  </th>


  <th>
    <a href="{{ get_permalink() }}" class="btn btn-icon" aria-label="consulter les messages">
      <img src="{{ Vite::asset('resources/images/eye.svg') }}" aria-hidden="true">
    </a>
  </th>
</tr>

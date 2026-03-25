@php
$post_id = get_the_ID();

if (!is_user_allowed_for_content($post_id)) {
    wp_redirect( get_post_type_archive_link( get_post_type() ) );
    return;
};

$messages = get_post_meta($post_id, '_messages', true);
usort($messages, function($a, $b) {
    // Calculer la date complète pour chaque message
    $dateA = (new DateTime())->modify($a['day_offset'] . ' days')->setTime(
        (int) substr($a['time'], 0, 2),
        (int) substr($a['time'], 3, 2)
    );
    $dateB = (new DateTime())->modify($b['day_offset'] . ' days')->setTime(
        (int) substr($b['time'], 0, 2),
        (int) substr($b['time'], 3, 2)
    );

    // Retourner le tri
    return $dateA <=> $dateB;
});

$current_user_id = get_current_user_id();

// personnage du user connecté
$current_character = get_posts([
  'post_type'  => 'character',
  'meta_key'   => '_linked_user',
  'meta_value' => $current_user_id,
  'fields'     => 'ids',
  'numberposts'=> 1
]);

$current_character_id = $current_character[0] ?? 0;

// personnages de la conversation
$character_1_ID = get_post_meta($post_id, '_character_1', true);
$character_2_ID = get_post_meta($post_id, '_character_2', true);

// autre personnage
$other_character_id = ($character_1_ID == $current_character_id)
    ? $character_2_ID
    : $character_1_ID;

// récupérer le post
$other_character = get_post($other_character_id);
$other_name = $other_character ? $other_character->post_title : '—';

// récupérer identifiant
$linked_user = get_post_meta($other_character_id, '_linked_user', true);
$manual_id   = get_post_meta($other_character_id, '_id', true);

if ($linked_user) {
    $user = get_userdata($linked_user);
    $other_identifier = $user ? $user->user_login : '—';
} else {
    $other_identifier = $manual_id ?: '—';
}
@endphp

<a class="content-single-return" href="{{ get_post_type_archive_link( get_post_type() ) }}">Retourner à la {!! get_post_type_object( get_post_type() )->label !!}</a>

<article @php(post_class('h-entry'))>
    
    <h1>Conversation avec <strong>{{ $other_identifier }} - {{ $other_name }}</strong></h1>

    <div id="alert-container"></div>
    
    <div class="zone_messages">
        @foreach($messages as $msg)

        <?php
            $date = now()->modify($msg['day_offset'] . ' days')->format('d/m/Y');
            $is_mine = ($msg['sender'] === 'character_1' && $other_character_id)
                    || ($msg['sender'] === 'character_2' && !$other_character_id);
        ?>
        
        <div class="message-send {{ $is_mine ? 'by--me' : '' }}">
            <div class="message-content">
                <p>{{ $msg['content'] }}</p>
            </div>
            <span class="message-time">{{ $date }} {{ $msg['time'] }}</span>
        </div>
        
        @endforeach
    </div>

    <input type="text" id="input_fake" class="message-input" placeholder="Message...">
</article>
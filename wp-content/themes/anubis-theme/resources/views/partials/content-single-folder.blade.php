@php
$post_id = get_the_ID();

if (!is_user_allowed_for_content($post_id)) {
    wp_redirect( get_post_type_archive_link( get_post_type() ) );
    return;
};


// Récupération des métas
$meta = [
    'date_opening' => get_post_meta($post_id, '_date_opening', true),
    'date_closing' => get_post_meta($post_id, '_date_closing', true),
    'date_last_update' => get_post_meta($post_id, '_date_last_update', true),
    'description' => wpautop(wp_kses_post(get_post_meta($post_id, '_description', true))),
];

$linked_is = get_post_meta($post_id, '_linked_is', true);
$linked_is = is_array($linked_is) ? $linked_is : [];

$linked_character = get_post_meta($post_id, '_linked_character', true);
$linked_character = is_array($linked_character) ? $linked_character : [];

// Pour les LOGS
$roles_allowed_logs = get_post_meta($post_id, '_roles_allowed_logs', true);
$roles_allowed_logs = is_array($roles_allowed_logs) ? $roles_allowed_logs : [];

$current_user = wp_get_current_user();
$can_view_logs = !empty(array_intersect($current_user->roles, $roles_allowed_logs));
$is_admin = !empty(array_intersect($current_user->roles, ['administrator', 'editor']));

$logs = get_post_meta($post_id, '_folder_logs', true);
$logs = is_array($logs) ? $logs : [];

$logs_console = [];

if($is_admin || $can_view_logs){
    foreach($logs as $log){
        $datetime = !empty($log['datetime']) ? gmdate('Y-m-d\TH:i:s\Z', strtotime($log['datetime'])) : '';
        $action = $ACTION_LABELS[$log['action']] ?? strtoupper($log['action']);

        $target = strtoupper($log['target'] ?? '');
        $target_ids = [];

        if(!empty($log['target_id'])){
            $target_ids[] = $log['target_id'];
        }

        // Support plusieurs IDs si besoin (ex: rapports ou IS multiples)
        if($log['target'] === 'is' && !empty($log['target_id'])){
            $target_ids[] = $log['target_id'];
        }

        $author = !empty($log['character']) ? $log['character'] : '';

        $line = $datetime
            ? '<span class="log-date">'.$datetime.'</span> | '.$action
                .($target ? ' '.$target : '')
                .(!empty($target_ids) ? ' '.implode(' ', array_map(fn($id) => '<span class="log-id">'.$id.'</span>', $target_ids)) : '')
                .($author ? ' BY '. '<span class="log-id">'.$author.'</span>' : '')
            : '';

        $logs_console[] = [
            'line' => $line
        ];
    }
}

// END Pour les LOGS

@endphp

<a class="content-single-return" href="{{ get_post_type_archive_link( get_post_type() ) }}">Retourner à la liste des {!! get_post_type_object( get_post_type() )->label !!}</a>

<article @php(post_class('h-entry'))>
    <div class="e-content">
        <div class="content">
            <header>
                <h1 class="p-name">
                    Dossier Numéro&nbsp;:&nbsp;{!! get_the_title() !!}
                </h1>
            </header>

            <ul class="list-short_infos">
                <li>Date d'ouverture&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_opening'])) !!}</span></li>

                <li>Date de fermeture&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_closing'])) !!}</span></li>

                <li>Date de la dernière modification&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_last_update'])) !!}</span></li>
            </ul>

            <h2>Résumé&nbsp;:</h2>
            @if(!empty($meta['description']))
            {!! $meta['description'] !!}
            @else
            {!! display_meta('') !!}
            @endif

            <div class="list-groupe">
                <div>
                    <h2>I.S. Liés&nbsp;:</h2>
                    @if(!empty($linked_is))
                    <ul class="list">
                        @foreach($linked_is as $is_id)
                        <li>
                            <a href="{{ get_permalink($is_id) }}">
                                {!! get_the_title($is_id) !!}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <x-alert type="default">
                        Aucun {!! get_post_type_object( "is" )->labels->singular_name !!} associé à ce {!! get_post_type_object( get_post_type() )->labels->singular_name !!}.
                    </x-alert>
                    @endif
                </div>
                <div>
                    <h2>Personnels Liés&nbsp;:</h2>
                    @if(!empty($linked_character))
                    <ul class="list">
                        @foreach($linked_character as $is_character)
                        <li>
                            <a href="{{ get_permalink($is_character) }}">
                                {!! get_the_title($is_character) !!}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <x-alert type="default">
                        Aucun Personnel associé à ce {!! get_post_type_object( get_post_type() )->labels->singular_name !!}.
                    </x-alert>
                    @endif
                </div>
                <div>
                    <h2>Rapports&nbsp;:</h2>
                    <x-alert type="default">
                        Aucun Rapport associé à ce {!! get_post_type_object( get_post_type() )->labels->singular_name !!}.
                    </x-alert>
                </div>
            </div>


            <h2>Historique</h2>
            @if( $is_admin || ($can_view_logs && !empty($logs_console)) )
                @if( !empty($logs_console) )
                <div class="logs">
                    <ul>
                        @foreach($logs_console as $log)
                            <li>{!! $log['line'] !!}</li>
                        @endforeach
                    </ul>
                </div>
                @else
                <x-alert type="warning">
                    Aucun historique renseigé. Les visiteurs verront un message de restriction.
                </x-alert>
                @endif
            @else
            <x-alert type="restricted">
                Vous n'êtes pas autorisé à consulter cet historique.
            </x-alert>
            @endif

        </div>

    </div>
</article>
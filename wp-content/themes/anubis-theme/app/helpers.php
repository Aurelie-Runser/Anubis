<?php


function format_date_fr($date) {
    return $date ? date_i18n('d/m/Y', strtotime($date)) : '';
}

// Helper pour afficher la valeur ou “non renseigné”
function display_meta($value) {
    return !empty($value) 
        ? "<strong>{$value}</strong>" 
        : '<strong class="no-informed">' . __('non renseigné', 'anubis-theme') . '</strong>';
}

// IS Labels Etats
function is_etat_label($etat) {
    $labels = [
        'capture'       => 'Capturé',
        'under_control' => 'Sous contol',
        'liberte'       => 'En liberté',
        'echappe'       => 'Échappé',
    ];

    return $labels[$etat] ?? $etat;
}

$ACTION_LABELS = [
    'create' => 'CREATE',
    'update' => 'UPDATE',
    'delete' => 'DELETE',
    'join' => 'JOIN',
    'unlink' => 'UNLINK',
    'read' => 'READ',
    'close' => 'CLOSE',
];
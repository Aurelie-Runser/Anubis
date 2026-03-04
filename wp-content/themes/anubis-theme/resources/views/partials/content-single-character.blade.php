@php
$post_id = get_the_ID();

// Récupération des métas
$meta = [
'linked_user' => get_post_meta($post_id, '_linked_user', true),
'date_birthday' => get_post_meta($post_id, '_date_birthday', true),
'residence_primary' => get_post_meta($post_id, '_residence_primary', true),
'residence_secondary' => get_post_meta($post_id, '_residence_secondary', true),
'gender' => get_post_meta($post_id, '_gender', true),
'nationality' => get_post_meta($post_id, '_nationality', true),
'date_recruitment' => get_post_meta($post_id, '_date_recruitment', true),
'affiliated_ubsidiary' => get_post_meta($post_id, '_affiliated_ubsidiary', true),
'pathologies' => wpautop(wp_kses_post(get_post_meta($post_id, '_pathologies', true))),
'relatives' => wpautop(wp_kses_post(get_post_meta($post_id, '_relatives', true))),
];

$thumbnail_url = get_the_post_thumbnail_url($post->ID, 'medium');

$user = get_userdata($meta['linked_user']);
$roles = $user ? $user->roles : [];
$role_display = $roles ? implode(', ', $roles) : 'Aucun rôle';
@endphp

<article @php(post_class('h-entry'))>
    <div class="e-content">
        <header>
            <h1 class="p-name">
                Identitifant&nbsp;:&nbsp;{!! display_meta($user ? $user->display_name : '') !!}
            </h1>
        </header>

        <div class="content">
            <div class="member-picture">
                @if($thumbnail_url)
                    <img src="{{ $thumbnail_url }}" alt="{!! esc_attr($title) !!}">
                @endif
            </div>

            <div class="member-infos">
                <ul class="member-infos-section member-infos--list">
                    <li>Identité Légal&nbsp;:&nbsp;<span>{!! display_meta($title) !!}<span></li>
                    <li>Date de naissance&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_birthday'])) !!}<span></li>
                    <li>Résidence Principale&nbsp;:&nbsp;<span>{!! display_meta($meta['residence_primary']) !!}<span></li>
                    <li>Résidence Actuelle&nbsp;:&nbsp;<span>{!! display_meta($meta['residence_secondary']) !!}<span></li>
                    <li>Genre&nbsp;:&nbsp;<span>{!! display_meta($meta['gender']) !!}<span></li>
                    <li>Nationalité&nbsp;:&nbsp;<span>{!! display_meta($meta['nationality']) !!}<span></li>
                </ul>

                <div class="member-infos-section member-infos--2">

                    <ul class="member-infos--list">
                        <li>Date de recrutement&nbsp;:&nbsp;<span>{!! display_meta(format_date_fr($meta['date_recruitment'])) !!}<span></li>
                        <li>Role&nbsp;:&nbsp;<span>{!! display_meta($role_display) !!}<span></li>
                        <li>Filiale Affiliée&nbsp;:&nbsp;<span>{!! display_meta($meta['affiliated_ubsidiary']) !!}<span></li>
                    </ul>

                    <div class="member-infos--multilines">
                        <h2>Pathologies&nbsp;:</h2>
                        @if(!empty($meta['pathologies']))
                        {!! $meta['pathologies'] !!}
                        @else
                        {!! display_meta('') !!}
                        @endif
                    </div>

                    <div class="member-infos--multilines">
                        <h2>Proches&nbsp;:</h2>
                        @if(!empty($meta['relatives']))
                        {!! $meta['relatives'] !!}
                        @else
                        {!! display_meta('') !!}
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="btn-logout">
                <a href="#" class="btn">
                    Deconnexion
                </a>
            </div>
        </div>


    </div>
</article>
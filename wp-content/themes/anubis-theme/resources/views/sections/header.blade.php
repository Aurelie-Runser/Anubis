@php
  // Récupération de l'objet courant
  $queried = get_queried_object();
  
  // Vérifie si la page a le template "profil"
  $is_profil_template = false;
  if ( is_page() && $queried ) {
    $template_file = get_post_meta( $queried->ID, '_wp_page_template', true );
    $is_profil_template = $template_file === 'template-profil.blade.php';
  }

  // Liste de tes CPT à afficher dans le menu
  $cpts = ['is', 'folder'];
@endphp

<header>
  <nav class="nav-primary" aria-label="Menu Principal">
    <ul>
      @foreach($cpts as $cpt)
        @php
          $is_active = is_post_type_archive($cpt) 
                       || is_singular($cpt) 
                       || is_tax(get_object_taxonomies($cpt));
          $post_type_obj = get_post_type_object($cpt);
        @endphp
        <li>
          <a href="{{ get_post_type_archive_link($cpt) }}" class="{{ $is_active ? 'active' : '' }}">
            {!! $post_type_obj->label !!}
          </a>
        </li>
      @endforeach

      <li>
        <a href="#">
          Messagerie
        </a>
      </li>
      <li>
        <a href="#">
          Lexique
        </a>
      </li>

      <li>
        <a href="{{ home_url('/profil') }}" class="{{ $is_profil_template ? 'active' : '' }}">
          Profil
        </a>
      </li>
    </ul>
  </nav>
</header>


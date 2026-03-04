<header>
  <nav class="nav-primary" aria-label="Menu Principal">
    <ul>
      <li>
        <a href="{{ get_post_type_archive_link( 'is' ) }}"
          class="{{ 
            is_post_type_archive('is') 
            || is_singular('is') 
            || is_tax(get_object_taxonomies('is')) 
              ? 'active' 
              : '' 
          }}"
          >
          {!! get_post_type_object( 'is' )->label !!}
        </a>
      </li>
      <li>
        <a href="{{ get_post_type_archive_link( 'folder' ) }}"
          class="{{ 
            is_post_type_archive('folder') 
            || is_singular('folder') 
            || is_tax(get_object_taxonomies('folder')) 
              ? 'active' 
              : '' 
          }}"
          >
          {!! get_post_type_object( 'folder' )->label !!}
        </a>
      </li>

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
        <a href="#">
          Profil
        </a>
      </li>

    </ul>
  </nav>
</header>

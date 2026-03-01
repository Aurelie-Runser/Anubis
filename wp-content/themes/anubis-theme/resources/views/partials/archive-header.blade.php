<header class="archive_header">
  <h1 class="sr-only">Page listant les {!! $label_cpt !!}</h1>

  @if(!empty($filters) && !is_wp_error($filters))
    <nav>
      <ul class="archive_filter_table">
        @foreach ($filters as $index => $filter)
          <li>
            <a href="{{ get_term_link($filter) }}"
              class="{{ is_tax($taxonomy, $filter->slug) || (!is_tax() && $loop->first) ? 'is-active' : '' }}">
              {!! $filter->name !!}
            </a>
          </li>
        @endforeach
      </ul>
    </nav>

  @endif

  @if (! have_posts())
  <x-alert type="warning">
    Aucun résultat ne correspond à votre recherche.
  </x-alert>

  {!! get_search_form(false) !!}
  @endif

</header>
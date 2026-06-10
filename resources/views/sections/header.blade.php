<header class="banner {{ $is_fixed ? 'is-fixed' : '' }} innergrid" data-header>
  <div class="banner__wrapper">
    <a class="brand" href="{{ home_url('/') }}">
      <img src="{!! $logo_url !!}" alt="{!! get_bloginfo('name')!!}" />
    </a>


    @if($navigation)
      <nav class="banner__nav banner__nav--{{ $align }}" aria-label="Main">
        <ul class="nav-primary">
          @foreach($navigation as $item)
            <li class="menu-item {{ $item->active ? 'is-active' : '' }} {{ $item->children ? 'has-children' : '' }}">
              <a href="{{ $item->url }}">{{ $item->label }}</a>
            </li>
          @endforeach
        </ul>
      </nav>
    @endif

    <div class="banner__actions">
      @if($has_lang && $languages)
        <nav class="banner__lang">
          @foreach($languages as $lang)
            <a href="{{ $lang['url'] }}" class="{{ $lang['current_lang'] ? 'active' : '' }}">
              {{ strtoupper($lang['slug']) }}
            </a>
          @endforeach
        </nav>
      @endif

      @if($has_cta)
        <div class="banner__cta wp-block-buttons">
          <div class="wp-block-button is-style-outline">
            <a href="{!! $headerLinks['wine_and_sake_url'] !!}" class="wp-block-button__link is-style-wine-sake" target="_blank">WINE & SAKÉ LIST</a>
          </div>
          <div class="wp-block-button">
            <a href="{!! $headerLinks['reservation_url'] !!}" class="wp-block-button__link is-style-reservations" target="_blank">RESERVATIONS</a>
          </div>
        </div>
      @endif

      <button class="burger" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</header>

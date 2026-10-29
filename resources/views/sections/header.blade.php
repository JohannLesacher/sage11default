<header class="banner {{ $is_fixed ? 'is-fixed' : '' }} innergrid" data-header>
  <div class="banner__wrapper">
    <a class="brand" href="{!! esc_url(home_url('/')) !!}">
      @include('partials.logo')
    </a>

    @if($navigation)
      <nav id="primary-navigation" class="banner__nav banner__nav--{{ $align }}" aria-label="Menu principal">
        @include('partials.navigation', ['items' => $navigation, 'class' => 'nav-primary'])
      </nav>
    @endif

    <div class="banner__actions">
      @if($has_lang && $languages)
        <nav class="banner__lang" aria-label="Langues">
          @foreach($languages as $lang)
            <a href="{!! esc_url($lang['url']) !!}" lang="{{ $lang['slug'] }}" hreflang="{{ $lang['slug'] }}"
               class="{{ $lang['current_lang'] ? 'active' : '' }}"
               @if($lang['current_lang']) aria-current="true" @endif>
              {{ strtoupper($lang['slug']) }}
            </a>
          @endforeach
        </nav>
      @endif

      @if($cta)
        <div class="banner__cta">
          <a href="{!! esc_url($cta['url']) !!}" class="btn btn-primary">{!! esc_html($cta['label']) !!}</a>
        </div>
      @endif

      @if($navigation)
        <button class="burger" type="button" aria-label="Menu" aria-expanded="false" aria-controls="primary-navigation">
          <span></span>
          <span></span>
          <span></span>
        </button>
      @endif
    </div>
  </div>
</header>

<section class="block-menu {!! $blockClass() !!}" @if($blockStyle()) style="{!! $blockStyle() !!}" @endif>
  <div class="block-menu__inner">
    <header class="block-menu__head">
      <div class="block-menu__heading">
        @if($title())
          <h2 class="block-menu__title">{!! $title() !!}</h2>
        @endif
        @if($subtitle())
          <p class="block-menu__subtitle">{!! $subtitle() !!}</p>
        @endif
      </div>
      @if($iconId())
        <div class="block-menu__icon">
          {!! wp_get_attachment_image($iconId(), 'thumbnail') !!}
        </div>
      @endif
    </header>

    @if($items())
      <ul class="block-menu__list">
        @foreach($items() as $item)
          <li class="block-menu__item">
            <div class="block-menu__item-text">
              @if(!empty($item['item_title']))
                <span class="block-menu__item-title">{!! $item['item_title'] !!}</span>
              @endif
              @if(!empty($item['item_subtitle']))
                <span class="block-menu__item-subtitle">{!! $item['item_subtitle'] !!}</span>
              @endif
            </div>
            @if(!empty($item['item_price']))
              <span class="block-menu__item-price">{!! $item['item_price'] !!}</span>
            @endif
          </li>
        @endforeach
      </ul>
    @endif

    @if ($is_preview)
      <div
        class="block-menu__cta"
      >
        <span class="block-menu__cta-label">{!! $cta()['text'] ?? $cta()['title'] ?? 'Télécharger' !!}</span>
      </div>
    @elseif(!empty($cta()['link']))
      <a
        class="block-menu__cta"
        href="{!! $cta()['link'] !!}"
        @if($cta()['download']) download @else target="_blank" rel="noopener" @endif
      >
        <span class="block-menu__cta-label">{!! $cta()['text'] ?? $cta()['title'] ?? 'Télécharger' !!}</span>
      </a>
    @endif
  </div>
</section>

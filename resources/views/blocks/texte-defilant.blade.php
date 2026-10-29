<div class="block-texte-defilant {!! $blockClass() !!} @if($pause()) has-pause @endif"
     @if($blockStyle()) style="{!! $blockStyle() !!}" @endif
     data-vitesse="{!! $vitesse() !!}"
     data-direction="{!! $direction() !!}"
     role="marquee"
     aria-label="{!! $ariaLabel() !!}">
  <div class="block-texte-defilant__track" aria-hidden="true">
    @foreach([0, 1] as $i)
      <div class="block-texte-defilant__group">
        <{!! $tag() !!} class="block-texte-defilant__text">{!! $texte() ?: ($is_preview ? 'Texte défilant…' : '') !!}</{!! $tag() !!}>
        @if($separateur())
          <span class="block-texte-defilant__separator">{!! $separateur() !!}</span>
        @endif
      </div>
    @endforeach
  </div>
</div>

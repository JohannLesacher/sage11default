<li class="splide__slide block-slide {!! $blockClass() !!} @if($animated()) is-animated is-animated--slide @endif"
    @if($blockStyle()) style="{!! $blockStyle() !!}" @endif
    @if($animated()) data-animation="{!! $animationType() !!}" @endif>
  {!! $content !!}
</li>

<li class="splide__slide block-slide {!! $blockClass() !!} @if($animated()) is-animated @endif"
    @if($blockStyle()) style="{!! $blockStyle() !!}" @endif
    @if($animated()) data-animation="{!! $animationType() !!}" @endif>
  {!! $content !!}
</li>

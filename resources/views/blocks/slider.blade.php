<section class="block-slider splide {!! $blockClass() !!}"
         @if($blockStyle()) style="{!! $blockStyle() !!}" @endif
         aria-label="{!! $label() !!}"
         data-splide="{!! $splideOptions() !!}">
  <div class="splide__track">
    <ul class="splide__list">
      {!! $content !!}
    </ul>
  </div>
</section>

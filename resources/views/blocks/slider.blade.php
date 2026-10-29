<section {!! $wrapperAttributes('block-slider splide', ['aria-label' => $label(), 'data-splide' => $splideOptions()]) !!}>
  <div class="splide__track">
    <ul class="splide__list">
      {!! $content !!}
    </ul>
  </div>
</section>

<div class="block-key-figure {!! $blockClass() !!}" @if($blockStyle()) style="{!! $blockStyle() !!}" @endif>
  @if($valeur())
    <p class="block-key-figure__value" @if($valueStyle()) style="{!! esc_attr($valueStyle()) !!}" @endif>
      @if($prefixe())
        <span class="block-key-figure__prefix">{!! esc_html($prefixe()) !!}</span>
      @endif
      <span class="block-key-figure__number">{!! esc_html($valeur()) !!}</span>
      @if($unite())
        <span class="block-key-figure__unit">{!! esc_html($unite()) !!}</span>
      @endif
    </p>
  @elseif($is_preview)
    <p class="block-key-figure__value" @if($valueStyle()) style="{!! esc_attr($valueStyle()) !!}" @endif>00</p>
  @endif

  @if($libelle())
    <p class="block-key-figure__label">{!! $libelle() !!}</p>
  @endif
</div>

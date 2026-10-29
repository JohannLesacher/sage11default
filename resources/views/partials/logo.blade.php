@if($logoId())
  {!! wp_get_attachment_image($logoId(), 'medium', false, ['alt' => $siteName(), 'class' => 'logo']) !!}
@else
  @svg('logo', 'logo', ['aria-hidden' => 'true'])
  <span class="sr-only">{!! esc_html($siteName()) !!}</span>
@endif

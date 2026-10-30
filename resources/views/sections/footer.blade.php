<footer class="site-footer">
  <div class="site-footer__logo">
    @include('partials.logo', ['footer' => true])
  </div>

  @if($contact)
    <address class="site-footer__contact">
      @isset($contact['address'])
        <p>{!! nl2br(esc_html($contact['address'])) !!}</p>
      @endisset
      @isset($contact['phone'])
        <p><a href="{!! esc_attr($contact['phoneHref']) !!}">{!! esc_html($contact['phone']) !!}</a></p>
      @endisset
      @isset($contact['email'])
        <p><a href="mailto:{!! antispambot($contact['email']) !!}">{!! antispambot($contact['email']) !!}</a></p>
      @endisset
    </address>
  @endif

  @if($footerNav)
    <nav class="site-footer__nav" aria-label="Pied de page">
      @include('partials.navigation', ['items' => $footerNav])
    </nav>
  @endif

  @if($social)
    <ul class="site-footer__social">
      @foreach($social as $link)
        <li class="site-footer__social-item site-footer__social-item--{{ $link['network'] }}">
          <a href="{!! esc_url($link['url']) !!}" target="_blank" rel="noopener">{!! esc_html($link['label']) !!}</a>
        </li>
      @endforeach
    </ul>
  @endif

  <div class="site-footer__bottom">
    <p class="site-footer__copyright">{!! esc_html($copyright) !!}</p>

    @if($legalNav)
      <nav class="site-footer__legal" aria-label="Mentions légales">
        @include('partials.navigation', ['items' => $legalNav])
      </nav>
    @endif
  </div>
</footer>

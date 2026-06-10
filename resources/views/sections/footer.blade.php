<footer class="site-footer innergrid">
  <div class="site-footer__container">
    <div class="site-footer__logo">
      <img src="{!! $logo_url !!}" width="100" alt="{!! get_bloginfo('name') !!}">

      <span class="site-footer__copyright">{!! $copyright !!}</span>
    </div>

    <div class="site-footer__right">
      <nav class="site-footer__nav">
        <ul class="nav-primary">
          @foreach($footerNav as $item)
            <li class="menu-item {{ $item->active ? 'is-active' : '' }}">
              <a href="{{ $item->url }}">{{ $item->label }}</a>
            </li>
          @endforeach
        </ul>
      </nav>

      @if($social)
        <ul class="site-footer__social">
          @foreach($social as $network)
            <li class="site-footer__social-item">
              <a href="{!! $network['url'] !!}"
                 class="site-footer__social-link site-footer__social-link--{!! $network['key'] !!}" target="_blank"
                 rel="noopener noreferrer" aria-label="{!! $network['label'] !!}">
                @svg('social.' . $network['key'], 'site-footer__social-icon', ['width' => 20, 'height' => 20])
              </a>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</footer>

<footer class="site-footer">
  <nav class="site-footer__nav">
    <ul class="nav-primary">
      @foreach($footerNav as $item)
        <li class="menu-item {{ $item->active ? 'is-active' : '' }}">
          <a href="{{ $item->url }}">{{ $item->label }}</a>
        </li>
      @endforeach
    </ul>
  </nav>
  <div class="site-footer__logo">
    <img src="{!! Vite::asset('resources/images/logo.svg') !!}" alt="{!! get_bloginfo('name') !!}">
  </div>
  <nav class="site-footer__nav">
    <ul class="nav-primary">
      @foreach($legalNav as $item)
        <li class="menu-item {{ $item->active ? 'is-active' : '' }}">
          <a href="{{ $item->url }}">{{ $item->label }}</a>
        </li>
      @endforeach
    </ul>
  </nav>
</footer>

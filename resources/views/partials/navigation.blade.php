<ul class="{{ $class ?? 'menu' }}">
  @foreach($items as $item)
    <li class="menu-item {{ $item->classes ?? '' }} {{ $item->active ? 'is-active' : '' }} {{ $item->activeAncestor ? 'is-active-ancestor' : '' }} {{ $item->children ? 'has-children' : '' }}">
      <a href="{!! esc_url($item->url) !!}"
         @if($item->active) aria-current="page" @endif
         @if($item->target) target="{!! esc_attr($item->target) !!}" @endif>{!! esc_html($item->label) !!}</a>

      @if($item->children)
        @include('partials.navigation', ['items' => $item->children, 'class' => 'sub-menu'])
      @endif
    </li>
  @endforeach
</ul>

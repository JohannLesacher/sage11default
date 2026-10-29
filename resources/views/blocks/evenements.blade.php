<section {!! $wrapperAttributes('block-evenements') !!}>
  @if($evenements())
    <ul class="block-evenements__liste">
      @foreach($evenements() as $evenement)
        <li class="block-evenements__item">
          @if($evenement['image'])
            {!! $evenement['image'] !!}
          @endif
          <h3 class="block-evenements__titre">
            <a href="{!! esc_url($evenement['url']) !!}">{!! esc_html($evenement['titre']) !!}</a>
          </h3>
          <time class="block-evenements__date" datetime="{!! esc_attr($evenement['datetime']) !!}">{!! esc_html($evenement['date']) !!}</time>
        </li>
      @endforeach
    </ul>
  @elseif($is_preview)
    <p>Aucun événement publié.</p>
  @endif
</section>

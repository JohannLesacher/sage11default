<section class="block-upcoming-event {!! $blockClass() !!}" @if($blockStyle()) style="{!! $blockStyle() !!}" @endif>
    <div class="block-upcoming-event__inner">
        @if($imageId())
            <figure class="block-upcoming-event__media">
                {!! wp_get_attachment_image($imageId(), 'large') !!}
            </figure>
        @endif

        <div class="block-upcoming-event__content">
            <p class="block-upcoming-event__eyebrow">Upcoming Event</p>

            @if($title())
                <h2 class="block-upcoming-event__title">{!! $title() !!}</h2>
            @endif

            <div class="block-upcoming-event__description">
                <InnerBlocks />
            </div>

            @if($dateLabel() || $time() || $price())
                <ul class="block-upcoming-event__meta">
                    @if($dateLabel() || $time())
                        <li class="block-upcoming-event__meta-item">
                            <svg class="block-upcoming-event__meta-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="17" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M3 9h18M8 2v4M16 2v4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <span>{!! trim($dateLabel() . ($dateLabel() && $time() ? ', ' : '') . $time()) !!}</span>
                        </li>
                    @endif
                    @if($price())
                        <li class="block-upcoming-event__meta-item">
                            <svg class="block-upcoming-event__meta-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="2" y="6" width="20" height="12" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/>
                                <circle cx="12" cy="12" r="2.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                            <span>{!! $price() !!}</span>
                        </li>
                    @endif
                </ul>
            @endif

            @if(!empty($cta()['link']))
                <a class="block-upcoming-event__cta" href="{!! $cta()['link'] !!}">
                    <span>{!! $cta()['text'] ?: 'Enquire now' !!}</span>
                </a>
            @endif
        </div>
    </div>
</section>

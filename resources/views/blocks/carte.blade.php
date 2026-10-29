<section class="block-carte {{ $attributes['className'] ?? '' }} align{{ $attributes['align'] ?? 'normal' }}">
    @if($estComplet())
        <div class="block-carte__cadre">
            <iframe src="{!! esc_url($embedUrl()) !!}" title="{!! esc_attr($titre()) !!}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    @elseif($is_preview)
        <p class="block-carte__vide">Renseignez la latitude et la longitude.</p>
    @endif
</section>

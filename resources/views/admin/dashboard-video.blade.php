<div class="cereal-one-video">
    <video class="cereal-one-video__player" controls preload="metadata" src="{!! esc_url($videoUrl) !!}"></video>

    <h3 class="cereal-one-video__title">Chapitres</h3>
    <ol class="cereal-one-video__chapters">
        @foreach($chapters as $chapter)
            <li>
                <button type="button" class="cereal-one-video__chapter" data-time="{!! $chapter['time'] !!}">
                    <span class="cereal-one-video__time">{!! $chapter['timecode'] !!}</span>
                    {!! esc_html($chapter['label']) !!}
                </button>
            </li>
        @endforeach
    </ol>

    <p class="cereal-one-video__support">
        Une question ? Écrivez à <a href="mailto:{!! esc_attr($supportEmail) !!}">{!! esc_html($supportEmail) !!}</a>, réponse sous 48 h ouvrées.
    </p>
</div>

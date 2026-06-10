@php
    use Filament\Support\Enums\IconPosition;
    use Illuminate\View\ComponentAttributeBag;

    $descriptionIcon = $getDescriptionIcon();
    $descriptionIconPosition = $getDescriptionIconPosition();
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';
    
    $bgColorHex = match($getColor()) {
        'success' => '#22c55e',
        'info' => '#3b82f6',
        'danger' => '#ef4444',
        'warning' => '#eab308',
        default => '#22c55e',
    };
@endphp

<{!! $tag !!}
    @if ($url)
        {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab()) }}
    @endif
    {{
        $getExtraAttributeBag()
            ->class([
                'fi-wi-stats-overview-stat relative rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10',
            ])
            ->style(['padding: 0 !important;', 'display: flex;', 'flex-direction: row;', 'overflow: hidden;'])
    }}
>
    <!-- Left Column: 60% -->
    <div style="width: 60%; padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; border-right: 1px solid #e5e7eb;">
        @php
            $isUrl = str_contains($getValue(), 'http');
            $fontSize = $isUrl ? '1.125rem' : '2.4375rem';
            $wordBreak = $isUrl ? 'break-all' : 'normal';
        @endphp
        <div style="font-size: {{ $fontSize }}; font-weight: 600; color: #111827; margin-bottom: 0.5rem; line-height: 1.2; word-break: {{ $wordBreak }};">
            {{ $getValue() }}
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem;">
            {{ \Filament\Support\generate_icon_html($getIcon(), attributes: (new \Illuminate\View\ComponentAttributeBag(['style' => 'width: 1.5rem; height: 1.5rem; color: #888ea8;']))) }}
            <span style="font-size: 70%; font-weight: 500; color: #6b7280;">
                {{ $getLabel() }}
            </span>
        </div>
    </div>

    <!-- Right Column: 40% -->
    @if ($description = $getDescription())
        <div 
            @if($isUrl)
                x-data="{ copied: false }"
                x-on:click="navigator.clipboard.writeText('{{ strip_tags($getValue()) }}'); copied = true; setTimeout(() => copied = false, 2000);"
                style="width: 40%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; text-align: center; background-color: {{ $bgColorHex }}; cursor: pointer; transition: opacity 0.2s;"
                x-bind:class="copied ? 'opacity-50' : 'hover:opacity-90'"
                title="Haz clic para copiar"
            @else
                style="width: 40%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; text-align: center; background-color: {{ $bgColorHex }};"
            @endif
        >
            @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::Before, 'before']))
                {{ \Filament\Support\generate_icon_html($descriptionIcon, attributes: (new \Illuminate\View\ComponentAttributeBag(['style' => 'width: 2rem; height: 2rem; color: #ffffff; margin-bottom: 0.5rem;']))) }}
            @endif
            
            <span 
                style="font-size: 0.875rem; font-weight: 600; color: #ffffff; line-height: 1.2;"
                @if($isUrl) x-text="copied ? '¡Liga copiada al portapapeles!' : '{{ addslashes($description) }}'" @endif
            >
                @if(!$isUrl) {{ $description }} @endif
            </span>

            @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::After, 'after']))
                {{ \Filament\Support\generate_icon_html($descriptionIcon, attributes: (new \Illuminate\View\ComponentAttributeBag(['style' => 'width: 2rem; height: 2rem; color: #ffffff; margin-top: 0.5rem;']))) }}
            @endif
        </div>
    @endif
</{!! $tag !!}>

<section class="card mt-8 p-6">
    <div class="flex flex-wrap items-start justify-between gap-5">
        <div>
            <h2 class="text-xl font-bold text-navy">{{ $chartTitle }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $chartSummary }}</p>
        </div>
        <form method="GET" action="{{ request()->url() }}" class="flex items-end gap-2">
            <div>
                <label for="analytics-year" class="mb-1 block text-xs font-semibold text-slate-600">Year</label>
                <select id="analytics-year" name="year" class="px-3 py-2 text-sm">
                    @foreach($years as $availableYear)
                        <option value="{{ $availableYear }}" @selected($availableYear === $year)>{{ $availableYear }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-navy px-4 py-2 text-sm font-bold text-white hover:bg-navy-800">Show</button>
        </form>
    </div>

    <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold">
        @foreach($chart['series'] as $line)
            <span class="flex items-center gap-2 text-slate-700">
                <span class="h-3 w-3 rounded-full" style="background-color: {{ $line['color'] }}"></span>
                {{ $line['label'] }}
            </span>
        @endforeach
    </div>

    <div class="mt-3 overflow-x-auto">
        <svg viewBox="0 0 710 265" class="h-64 min-w-[680px] w-full" role="img" aria-labelledby="yearly-chart-title yearly-chart-description">
            <title id="yearly-chart-title">{{ $chartTitle }} — {{ $year }}</title>
            <desc id="yearly-chart-description">{{ $chartDescription }}</desc>

            @for($grid = 0; $grid <= 4; $grid++)
                @php($gridY = 210 - $grid * 42.5)
                @php($gridValue = (int) round($chart['scale'] * $grid / 4))
                <line x1="54" y1="{{ $gridY }}" x2="659" y2="{{ $gridY }}" stroke="#e2e8f0" stroke-width="1" />
                <text x="44" y="{{ $gridY + 4 }}" text-anchor="end" fill="#64748b" font-size="11">{{ $gridValue }}</text>
            @endfor

            @foreach($chart['series'] as $line)
                <polyline points="{{ $line['points'] }}" fill="none" stroke="{{ $line['color'] }}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                @foreach($chart['months'] as $month)
                    <circle cx="{{ $month['x'] }}" cy="{{ $month[$line['key'].'_y'] }}" r="4" fill="{{ $line['color'] }}">
                        <title>{{ $month['label'] }} {{ $year }}: {{ $month[$line['key']] }} — {{ $line['label'] }}</title>
                    </circle>
                @endforeach
            @endforeach

            @foreach($chart['months'] as $month)
                <text x="{{ $month['x'] }}" y="239" text-anchor="middle" fill="#64748b" font-size="11">{{ $month['label'] }}</text>
            @endforeach
        </svg>
    </div>

    <p class="mt-3 rounded-xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-600">
        <span class="font-bold text-navy">How to read this chart:</span>
        {{ $chartDescription }}
        @unless($chart['hasData'])
            No activity was recorded for {{ $year }} yet.
        @endunless
    </p>
</section>

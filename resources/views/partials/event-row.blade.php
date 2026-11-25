@php($muted = $muted ?? false)
<a href="{{ route('events.show', $event) }}" data-event data-title="{{ strtolower($event->title.' '.$event->organizerName().' '.$event->location) }}" data-cat="{{ $event->category->id }}"
   class="card reveal group flex flex-col p-5 {{ $muted ? 'opacity-60' : '' }}">
    @if($event->poster_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($event->poster_path) }}" alt="Poster for {{ $event->title }}" class="mb-4 h-40 w-full rounded-xl bg-slate-100 object-cover">
    @endif
    <div class="flex items-start gap-4">
        <div class="shrink-0 w-16 h-[72px] rounded-2xl bg-gradient-to-br from-navy to-indigo-500 text-center flex flex-col justify-center shadow-lg shadow-navy/20 group-hover:scale-105 transition">
            <span class="text-xs font-bold uppercase tracking-wider text-gold-400">{{ $event->start_date->format('M') }}</span>
            <span class="text-3xl font-extrabold text-white leading-none mt-0.5">{{ $event->start_date->format('j') }}</span>
        </div>
        <div class="min-w-0 flex-1">
            <span class="inline-block text-xs font-semibold bg-gold-400/20 text-navy rounded-full px-2.5 py-1">{{ $event->category->name }}</span>
            <h3 class="mt-2 text-lg font-bold text-navy leading-snug group-hover:text-indigo-500 transition">{{ $event->title }}</h3>
        </div>
    </div>
    <p class="mt-4 text-sm text-slate-600">{{ $event->organizerName() }}</p>
    <p class="text-sm text-slate-500 mt-1">{{ $event->start_date->format('l, H:i') }} · {{ $event->location }}</p>
    <div class="mt-auto pt-4 flex items-center justify-between">
        @unless($muted)
            <span class="text-xs font-bold rounded-full px-3 py-1 {{ $event->isFull() ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}">
                {{ $event->isFull() ? 'Full' : $event->seatsLeft().' places left' }}
            </span>
        @else <span></span> @endunless
        <span class="text-navy text-sm font-semibold group-hover:translate-x-1 transition">View &rarr;</span>
    </div>
</a>

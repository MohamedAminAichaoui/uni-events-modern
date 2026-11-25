@extends('layouts.app')
@section('title', 'Upcoming events')

@section('hero')
<section class="hero-bg text-white relative overflow-hidden">
    <div class="max-w-6xl mx-auto px-4 py-16 grid gap-10 lg:grid-cols-5 lg:items-center">
        <div class="lg:col-span-3">
            <span class="inline-flex items-center gap-2 text-xs font-semibold bg-white/10 border border-white/15 rounded-full px-3 py-1.5"><span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span> Live campus calendar</span>
            <h1 class="mt-5 text-4xl sm:text-6xl font-extrabold leading-[1.05]">What’s on at the <span class="text-gold-400">university</span></h1>
            <p class="mt-5 text-lg text-slate-300 max-w-xl">Job fairs, career talks and club activities. Pick an event and register in a click.</p>

            <form method="GET" action="{{ route('home') }}" class="mt-8 p-2 bg-white rounded-3xl shadow-2xl shadow-black/30 grid gap-2 sm:grid-cols-[1fr_auto_auto]">
                <input id="liveSearch" type="text" name="q" value="{{ request('q') }}" placeholder="Search events instantly…" aria-label="Search by title" class="bg-slate-50 text-slate-800 px-4 py-3 rounded-2xl placeholder-slate-400 border-0">
                <select name="category" aria-label="Category" class="bg-slate-50 text-slate-800 px-4 py-3 rounded-2xl border-0">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="club" aria-label="Organiser" class="bg-slate-50 text-slate-800 px-4 py-3 rounded-2xl border-0 sm:col-span-1">
                    <option value="">All organisers</option>
                    <option value="university" @selected(request('club') === 'university')>University administration</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" @selected(request('club') == $club->id)>{{ $club->name }}</option>
                    @endforeach
                </select>
                <button class="bg-gold-500 text-navy font-bold px-6 py-3 rounded-2xl hover:bg-gold-400 sm:col-span-3">Find events</button>
            </form>
        </div>

        @if($featured)
            <a href="{{ route('events.show', $featured) }}" class="lg:col-span-2 block group bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-6 hover:bg-white/15 hover:-translate-y-1 transition duration-300">
                <p class="text-xs font-bold uppercase tracking-widest text-gold-400">Next on campus</p>
                <div class="mt-4 flex gap-4">
                    <div class="shrink-0 w-16 h-20 rounded-2xl bg-white text-center flex flex-col justify-center">
                        <span class="text-xs font-bold uppercase text-indigo-500">{{ $featured->start_date->format('M') }}</span>
                        <span class="text-3xl font-extrabold text-navy leading-none mt-1">{{ $featured->start_date->format('j') }}</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold leading-snug">{{ $featured->title }}</h2>
                        <p class="mt-1 text-slate-300 text-sm">{{ $featured->organizerName() }}</p>
                    </div>
                </div>
                <p class="mt-5 text-sm text-slate-300">{{ $featured->start_date->format('l, H:i') }} · {{ $featured->location }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs font-bold rounded-full px-3 py-1 {{ $featured->isFull() ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $featured->isFull() ? 'Full' : $featured->seatsLeft().' places left' }}</span>
                    <span class="text-gold-400 font-semibold group-hover:translate-x-1 transition">Details &rarr;</span>
                </div>
            </a>
        @endif
    </div>
</section>
@endsection

@section('content')
@if($events->isEmpty())
    <div class="text-center py-16 card"><p class="text-4xl">🔎</p><p class="mt-3 text-slate-600">No upcoming event matches your search. Try removing a filter.</p></div>
@else
    <div class="flex flex-wrap items-center gap-2 mb-8" id="chips">
        <button type="button" data-cat="" class="chip active rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold">All</button>
        @foreach($categories as $category)
            <button type="button" data-cat="{{ $category->id }}" class="chip rounded-full border border-slate-300 bg-white px-4 py-2 text-sm font-semibold hover:border-navy">{{ $category->name }}</button>
        @endforeach
        <span id="count" class="ml-auto text-sm text-slate-500"></span>
    </div>

    @php($grouped = $events->getCollection()->groupBy(fn ($event) => $event->start_date->format('F Y')))
    @foreach($grouped as $month => $group)
        <section data-month>
            <h2 class="text-2xl font-extrabold text-navy flex items-center gap-4 {{ $loop->first ? '' : 'mt-12' }} mb-5">{{ $month }}<span class="flex-1 h-px bg-slate-200"></span></h2>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($group as $event)
                    @include('partials.event-row', ['event' => $event])
                @endforeach
            </div>
        </section>
    @endforeach
    <p id="none" class="hidden text-center text-slate-500 py-12">Nothing matches. Try another word or category.</p>

    <div class="mt-10">{{ $events->links() }}</div>
@endif
@endsection

@push('scripts')
<script>
    (() => {
        const cards = $$('[data-event]'), input = $('#liveSearch'); let cat = '';
        if (!cards.length) return;
        const apply = () => {
            const q = (input.value || '').trim().toLowerCase(); let n = 0;
            cards.forEach(c => {
                const ok = (!q || c.dataset.title.includes(q)) && (!cat || c.dataset.cat === cat);
                c.style.display = ok ? '' : 'none'; if (ok) { n++; c.classList.add('in'); }
            });
            $$('[data-month]').forEach(m => m.style.display = $$('[data-event]', m).some(c => c.style.display !== 'none') ? '' : 'none');
            $('#none').classList.toggle('hidden', n > 0);
            $('#count').textContent = n + (n === 1 ? ' event' : ' events');
        };
        input.addEventListener('input', apply);
        $$('#chips .chip').forEach(b => b.addEventListener('click', () => {
            $$('#chips .chip').forEach(x => x.classList.remove('active')); b.classList.add('active'); cat = b.dataset.cat; apply();
        }));
        apply();
    })();
</script>
@endpush

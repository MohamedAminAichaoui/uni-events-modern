<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1" for="title">Title</label>
        <input id="title" type="text" name="title" value="{{ old('title', $event->title) }}" required class="w-full border rounded px-3 py-2">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1" for="description">Description</label>
        <textarea id="description" name="description" rows="5" required class="w-full border rounded px-3 py-2">{{ old('description', $event->description) }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1" for="poster">Event poster (JPG, PNG or WebP; max 5 MB)</label>
        <input id="poster" type="file" name="poster" accept="image/jpeg,image/png,image/webp" class="w-full border rounded px-3 py-2">
        @if($event->poster_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($event->poster_path) }}" alt="Current poster for {{ $event->title }}" class="mt-3 max-h-56 rounded object-contain">
            <p class="mt-1 text-sm text-slate-500">Choose a new image only if you want to replace the current poster.</p>
        @endif
    </div>

    <div>
        <label class="block text-sm font-medium mb-1" for="start_date">Starts</label>
        <input id="start_date" type="datetime-local" name="start_date" value="{{ old('start_date', $event->start_date?->format('Y-m-d\TH:i')) }}" required class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1" for="end_date">Ends</label>
        <input id="end_date" type="datetime-local" name="end_date" value="{{ old('end_date', $event->end_date?->format('Y-m-d\TH:i')) }}" required class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1" for="location">Location</label>
        <input id="location" type="text" name="location" value="{{ old('location', $event->location) }}" required class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1" for="capacity">Capacity</label>
        <input id="capacity" type="number" min="1" name="capacity" value="{{ old('capacity', $event->capacity) }}" required class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1" for="category_id">Category</label>
        <select id="category_id" name="category_id" required class="w-full border rounded px-3 py-2">
            <option value="">Choose...</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $event->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    @if(auth()->user()->isAdmin())
        <div>
            <label class="block text-sm font-medium mb-1" for="club_id">Organiser</label>
            <select id="club_id" name="club_id" class="w-full border rounded px-3 py-2">
                <option value="">University administration</option>
                @foreach($clubs as $club)
                    <option value="{{ $club->id }}" @selected(old('club_id', $event->club_id) == $club->id)>{{ $club->name }}</option>
                @endforeach
            </select>
        </div>
    @else
        <div>
            <label class="block text-sm font-medium mb-1">Organiser</label>
            <p class="px-3 py-2 text-slate-600">{{ auth()->user()->club?->name }}</p>
        </div>
    @endif
</div>

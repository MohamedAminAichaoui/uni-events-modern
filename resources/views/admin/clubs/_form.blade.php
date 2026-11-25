<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium mb-1" for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $club->name) }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1" for="acronym">Acronym</label>
        <input id="acronym" type="text" name="acronym" value="{{ old('acronym', $club->acronym) }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1" for="email">Contact email (optional)</label>
        <input id="email" type="email" name="email" value="{{ old('email', $club->email) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1" for="logo">Club logo (JPG, PNG or WebP; max 2 MB)</label>
        <input id="logo" type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="w-full border rounded px-3 py-2">
        @if($club->logo_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($club->logo_path) }}" alt="Current logo for {{ $club->name }}" class="mt-3 h-24 w-24 rounded-xl object-contain">
            <p class="mt-1 text-sm text-slate-500">Choose a new image only if you want to replace the current logo.</p>
        @endif
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1" for="description">Description</label>
        <textarea id="description" name="description" rows="4" required class="w-full border rounded px-3 py-2">{{ old('description', $club->description) }}</textarea>
    </div>
</div>

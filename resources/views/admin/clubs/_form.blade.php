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
        <label class="block text-sm font-medium mb-1" for="description">Description</label>
        <textarea id="description" name="description" rows="4" required class="w-full border rounded px-3 py-2">{{ old('description', $club->description) }}</textarea>
    </div>
</div>

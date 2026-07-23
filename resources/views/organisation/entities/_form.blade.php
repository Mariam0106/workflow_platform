<div>
    <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Nom</label>
    <input type="text" name="name" value="{{ old('name', $entity?->name) }}" required
           class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">
</div>
<div>
    <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Code</label>
    <input type="text" name="code" value="{{ old('code', $entity?->code) }}" required
           class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">
</div>
<div>
    <label class="mb-1.5 block text-[13px] font-medium text-slate-700">Description <span class="font-normal text-slate-400">(optionnel)</span></label>
    <textarea name="description" rows="3" class="block w-full rounded-lg border border-brand-border px-3.5 py-2.5 text-sm">{{ old('description', $entity?->description) }}</textarea>
</div>

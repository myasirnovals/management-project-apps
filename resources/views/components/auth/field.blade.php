@props(['name', 'label', 'type' => 'text', 'autocomplete' => null, 'value' => '', 'autofocus' => false, 'readonly' => false])
<div>
    <div class="mb-1.5 flex items-center justify-between">
        <label for="{{ $name }}" class="text-sm font-medium text-slate-200">{{ $label }}</label>
        {{ $aside ?? '' }}
    </div>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $type === 'password' ? '' : ($value !== '' ? $value : old($name)) }}"
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if($autofocus) autofocus @endif
        @if($readonly) readonly @endif
        required
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
        {{ $attributes->class([
            'block w-full rounded-lg border bg-slate-900 px-3.5 py-2.5 text-sm text-white placeholder-slate-500',
            'focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/40',
            'border-slate-700' => ! $errors->has($name),
            'border-rose-500' => $errors->has($name),
            'text-slate-400' => $readonly,
        ]) }}>
    @error($name)
        <p id="{{ $name }}-error" class="mt-1.5 text-xs text-rose-300">{{ $message }}</p>
    @enderror
</div>

<div class="relative flex-1">
    <div class="rounded-md shadow-sm">
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
        <select name="{{ $name }}"
                wire:model.live="{{ $name }}"
                aria-label="{{ $label }}"
                class="rounded-lg border border-gray-300 bg-white text-gray-700 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 w-full py-2 px-3">
            <option value="">Seleccione</option>
            @foreach($options as $option)
                <option value="{{ $option->name }}">{{ $option->name }}</option>
            @endforeach
        </select>
    </div>
    @if ($errors->has($name))
        <small class="text-red-600 text-sm mt-1">{{ $errors->first($name) }}</small>
    @endif
</div>

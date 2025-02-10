<div>
    <div class="flex items-center mb-2 space-x-4">
        <label for="clinica" class="text-sm font-medium text-gray-700">{{$label}}</label>
        <select name="{{$name}}" wire:model.live="{{$name}}" class="form-select mt-0 block w-64 px-4 py-2 border rounded-lg">
            <option value="">Todas las clínicas</option>
            @foreach ($options as $clinica)
                <option value="{{ $clinica->id }}">{{ $clinica->name }}</option>
            @endforeach
        </select>
        @if ($errors->has($name))
            <small class="text-red-600 text-sm mt-1">{{ $errors->first($name) }}</small>
        @endif
    </div>
</div>

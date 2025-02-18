<div class="bg-white shadow rounded-md p-4 flex flex-col">
    <div class="flex items-center">
        <svg class="w-8 h-8 text-gray-500 mr-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M10 4L2 8V16L10 20L18 16V8L10 4Z"></path>
        </svg>
        <button
            {{ $attributes->merge(['class' => 'text-lg font-medium text-gray-700 hover:underline']) }}>
            {{ $folderName }}
        </button>
    </div>
</div>

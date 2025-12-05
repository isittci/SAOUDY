<div class="dropdown">
    <button onclick="toggleDropdown(this)"
        class="w-full flex items-center justify-between px-4 py-3 hover:bg-green-600 transition-colors">
        <div class="flex items-center space-x-3">
            <i class="{{ $icon }} text-sm"></i>
            <span class="font-medium text-sm">{{ $label }}</span>
        </div>
        <i class="fas fa-chevron-down text-xs transition-transform"></i>
    </button>

    <div class="hidden ml-10 mt-1 space-y-1">
        @foreach ($items as $item)
            <a href="#" class="flex items-center space-x-3 px-3 py-2 text-sm hover:bg-green-600 rounded">
                <i class="{{ $item['icon'] }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

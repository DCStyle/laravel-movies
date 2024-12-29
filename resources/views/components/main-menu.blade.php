<div class="hidden lg:flex items-center space-x-1">
    @foreach($menus as $menu)
        @if($menu->children->isEmpty())
            <a href="{{ $menu->url }}"
               class="px-4 py-2 text-gray-300 rounded-lg transition-all duration-200 hover:text-white hover:bg-gray-700/50 relative group">
                <span class="relative z-10">{{ $menu->title }}</span>
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg opacity-0 group-hover:opacity-10 transition-opacity"></div>
            </a>
        @else
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        @click.away="open = false"
                        class="flex items-center px-4 py-2 text-gray-300 rounded-lg transition-all duration-200 hover:text-white hover:bg-gray-700/50">
                    {{ $menu->title }}
                    <svg class="w-4 h-4 ml-2 transition-transform duration-200"
                         :class="{'rotate-180': open}"
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute left-0 mt-2 w-48 rounded-xl bg-gray-800 shadow-xl border border-gray-700/50 overflow-hidden">
                    @foreach($menu->children as $child)
                        <a href="{{ $child->url }}"
                           class="block px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-white transition-colors">
                            {{ $child->title }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>
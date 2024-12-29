<div class="lg:hidden">
    @foreach($menus as $menu)
        @if($menu->children->isEmpty())
            <a href="{{ $menu->url }}"
               class="block py-3 px-4 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                {{ $menu->title }}
            </a>
        @else
            <div x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center justify-between w-full py-3 px-4 text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                    {{ $menu->title }}
                    <svg class="w-4 h-4 transition-transform duration-200"
                         :class="{'rotate-180': open}"
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="pl-4">
                    @foreach($menu->children as $child)
                        <a href="{{ $child->url }}"
                           class="block py-3 px-4 text-sm text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                            {{ $child->title }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>
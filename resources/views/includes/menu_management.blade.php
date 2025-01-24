<a href="{{ route('management.articles.index') }}"
   class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.articles.*') ? 'bg-gray-700' : '' }}">
    <i class="fas fa-newspaper mr-2"></i> Tin tức
</a>
<a href="{{ route('management.categories.index') }}"
   class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }}">
    <i class="fas fa-folder mr-2"></i> Danh mục
</a>
<a href="{{ route('management.movies.index') }}"
   class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('movies.management.*') ? 'bg-gray-700' : '' }}">
    <i class="fas fa-film mr-2"></i> Phim
</a>
<a href="{{ route('management.settings') }}"
   class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.settings') ? 'bg-gray-700' : '' }}">
    <i class="fas fa-cogs mr-2"></i> Cài đặt
</a>
<a href="{{ route('management.menus.index') }}"
   class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.menus.*') ? 'bg-gray-700' : '' }}">
    <i class="fas fa-bars mr-2"></i> Menu
</a>
<a href="{{ route('management.pages.index') }}"
   class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.pages.*') ? 'bg-gray-700' : '' }}">
    <i class="fas fa-pager mr-2"></i> Các trang
</a>
<a href="{{ route('management.ads.index') }}"
   class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.ads.*') ? 'bg-gray-700' : '' }}">
    <i class="fas fa-ad mr-2"></i> Quảng cáo
</a>
<a href="{{ route('management.movie-ads.index') }}"
   class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.movie-ads.*') ? 'bg-gray-700' : '' }}">
    <i class="fas fa-tachometer-alt-average mr-2"></i> Quảng cáo trong phim
</a>
<a href="{{ route('management.footer.index') }}"
   class="block px-4 py-2 hover:bg-gray-700 {{ request()->routeIs('admin.footer.*') ? 'bg-gray-700' : '' }}">
    <i class="fas fa-th-large mr-2"></i> Footer
</a>
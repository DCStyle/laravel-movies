@if ($paginator->hasPages())
    <nav class="pagination">
        <a href="{{ $paginator->previousPageUrl() }}" class="{{ $paginator->onFirstPage() ? 'disabled' : '' }}">&lsaquo;</a>

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="dots">...</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <a href="{{ $url }}" class="{{ $page == $paginator->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach
            @endif
        @endforeach

        <a href="{{ $paginator->nextPageUrl() }}" class="{{ !$paginator->hasMorePages() ? 'disabled' : '' }}">&rsaquo;</a>
    </nav>
@endif
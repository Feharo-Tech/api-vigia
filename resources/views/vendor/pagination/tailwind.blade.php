@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <!-- Texto de resultados - agora acima dos botões -->
        <div class="text-center mb-4 text-sm text-gray-600">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
        </div>

        <div class="flex justify-center">
            <ul class="flex items-center space-x-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li aria-disabled="true">
                        <span class="px-3 py-1 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                            &laquo;
                        </span>
                    </li>
                @else
                    <li>
                        <a href="{{ $paginator->previousPageUrl() }}" 
                           class="px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           rel="prev">
                            &laquo;
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li aria-disabled="true">
                            <span class="px-3 py-1 rounded-md bg-gray-100 text-gray-400">
                                {{ $element }}
                            </span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li aria-current="page">
                                    <span class="px-3 py-1 rounded-md bg-blue-600 text-white font-medium">
                                        {{ $page }}
                                    </span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $url }}" 
                                       class="px-3 py-1 rounded-md bg-white text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       aria-label="Ir para página {{ $page }}">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li>
                        <a href="{{ $paginator->nextPageUrl() }}" 
                           class="px-3 py-1 rounded-md bg-blue-600 text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           rel="next">
                            &raquo;
                        </a>
                    </li>
                @else
                    <li aria-disabled="true">
                        <span class="px-3 py-1 rounded-md bg-gray-100 text-gray-400 cursor-not-allowed">
                            &raquo;
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
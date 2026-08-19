@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Navegacao de paginacao" class="flex items-center justify-between">
            <div class="flex justify-between flex-1 sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 cursor-default leading-5 rounded-lg dark:text-[#70847d] dark:bg-[#112a21] dark:border-[#16352a]">
                            Anterior
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 leading-5 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition dark:text-[#e6f2ec] dark:bg-[#0c221a] dark:border-[#16352a] dark:hover:bg-[#112a21]">
                            Anterior
                        </button>
                    @endif
                </span>

                <span>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-700 bg-white border border-slate-200 leading-5 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-green-500 transition dark:text-[#e6f2ec] dark:bg-[#0c221a] dark:border-[#16352a] dark:hover:bg-[#112a21]">
                            Proximo
                        </button>
                    @else
                        <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 cursor-default leading-5 rounded-lg dark:text-[#70847d] dark:bg-[#112a21] dark:border-[#16352a]">
                            Proximo
                        </span>
                    @endif
                </span>
            </div>

            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-slate-600 leading-5 dark:text-[#8aa39a]">
                        Exibindo
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        ate
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                        de
                        <span class="font-medium">{{ $paginator->total() }}</span>
                        resultados
                    </p>
                </div>

                <div>
                    <span class="relative z-0 inline-flex rtl:flex-row-reverse rounded-lg shadow-sm">
                        <span>
                            @if ($paginator->onFirstPage())
                                <span aria-disabled="true">
                                    <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 cursor-default rounded-l-lg leading-5 dark:text-[#70847d] dark:bg-[#112a21] dark:border-[#16352a]" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </span>
                            @else
                                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-slate-500 bg-white border border-slate-200 rounded-l-lg leading-5 hover:text-slate-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-green-500 transition dark:text-[#8aa39a] dark:bg-[#0c221a] dark:border-[#16352a] dark:hover:bg-[#112a21] dark:hover:text-[#4fd1a5]">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif
                        </span>

                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <span aria-disabled="true">
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-500 bg-white border border-slate-200 cursor-default leading-5 dark:text-[#8aa39a] dark:bg-[#0c221a] dark:border-[#16352a]">{{ $element }}</span>
                                </span>
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if ($page == $paginator->currentPage())
                                            <span aria-current="page">
                                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-green-600 border border-green-600 cursor-default leading-5 dark:bg-[#008b5c] dark:border-[#008b5c] dark:text-[#e6f2ec]">{{ $page }}</span>
                                            </span>
                                        @else
                                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-700 bg-white border border-slate-200 leading-5 hover:bg-slate-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-green-500 transition dark:text-[#e6f2ec] dark:bg-[#0c221a] dark:border-[#16352a] dark:hover:bg-[#112a21]">
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        <span>
                            @if ($paginator->hasMorePages())
                                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-500 bg-white border border-slate-200 rounded-r-lg leading-5 hover:text-slate-700 focus:z-10 focus:outline-none focus:ring-2 focus:ring-green-500 transition dark:text-[#8aa39a] dark:bg-[#0c221a] dark:border-[#16352a] dark:hover:bg-[#112a21] dark:hover:text-[#4fd1a5]">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @else
                                <span aria-disabled="true" aria-label="Proximo">
                                    <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed rounded-r-lg leading-5 dark:text-[#70847d] dark:bg-[#112a21] dark:border-[#16352a]" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </span>
                            @endif
                        </span>
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>

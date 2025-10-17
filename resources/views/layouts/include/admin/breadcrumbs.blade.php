{{--verifica si hay un elemento en el arreglo breadcrumb--}}
@if (count($breadcrumbs))
    {{-- Margin botton o margen inferior--}}
    <nav class="mb-2 block">

        {{-- order list--}}
        <ol class ="flex flex-wrap text-slate-700 dark:text-slate-400 text-sm">
            {{-- Recorre cada elemento del breadcrumb --}}
             @foreach ($breadcrumbs as $item)
                {{-- Centra los li--}}
                <li class="flex items-center">
                    {{-- Si NO es el primer elemento, usa "/" --}}
                    @unless ($loop->first)
                        {{--Padding del eje x --}}
                        <span class="px-2 text-gray-400">/</span>
                    @endunless

                    @isset($item['href'])
                        {{-- Revisar si existe una llave 'href', muestralo --}}
                        <a href="{{$item['href'] }}" class="opacity-60 hover:opacity-100 transition">
                            {{$item['name'] }}
                        </a>
                        {{--Si no hay href --}}
                    @else
                        {{$item['name']}}
                    @endisset

                </li>
            @endforeach
        </ol>
        {{--El ultimo item aparece resaltado--}}
        @if (count($breadcrumbs)>1)
            {{-- mt: margin top--}}
            <h6 class="font-bold mt-2">
                {{ end ($breadcrumbs)['name']}}
            </h6>
        @endif
    </nav>
@endif

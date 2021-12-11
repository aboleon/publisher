<x-aboleon.publisher-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Oups !
        </h2>
    </x-slot>
    {!! ResponseRenderers::warning($message) !!}

</x-aboleon.publisher-layout>
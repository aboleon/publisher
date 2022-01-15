<x-aboleon.publisher-layout title="404">
    <img src="{{ asset('aboleon/publisher/system/404/'.(Arr::random(File::allFiles(public_path('aboleon/publisher/system/404/')), 1)[0]->getFilename())) }}" alt="">
</x-aboleon.publisher-layout>
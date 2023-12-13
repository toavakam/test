<x-layout>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="#"><h2>{{ $attempt->test->getQuestionsTitle(app()->currentLocale()) }}</h2></a>
            </div>
        </nav>
    </header>

    <div class="cont justify-content-center">
        <h1 class="questiontext">{{ __('messages.thank_you') }}</h1>
        <h3 class="questiondescrip">{{ __('messages.finish') }}</h3>
        @if(! $hasImageCustomQuestion)
            <h2 style="color: blue">{{ __('messages.result') }} {{ $percentage }}%</h2>
        @endif
    </div>
</x-layout>
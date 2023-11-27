<x-layout>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#"><h2>{{$test->getQuestionsTitle($lang)}}</h2></a>
            <div>
                <ul class="navbar-nav"@if(App::currentLocale()==="lv")
                    <li class="nav-item"><a class="nav-link" href="{{ route('finish', ['pk'=>$pk, 'lang' => 'en'])}}">English</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('finish', ['pk'=>$pk, 'lang' => 'ru'])}}">Русский</a></li>
                @elseif(App::currentLocale()==="ru")
                    <li class="nav-item"><a class="nav-link" href="{{ route('finish', ['pk'=>$pk, 'lang' => 'en'])}}">English</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('finish', ['pk'=>$pk, 'lang' => 'lv'])}}">Latviesu</a></li>
                @elseif(App::currentLocale()==="en")
                    <li class="nav-item"><a class="nav-link" href="{{ route('finish', ['pk'=>$pk, 'lang' => 'lv'])}}">Latviesu</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('finish', ['pk'=>$pk, 'lang' => 'ru'])}}">Русский</a></li>
                    </ul>
                @endif
            </div>
        </div>
    </nav>
</header>
<body>
@if($hasImageCustomQuestion)
    <div class="cont justify-content-center">
        <h1>{{ __('messages.thank_you') }}</h1>
        <h3>{{ __('messages.finish') }}</h3>
    </div>
@else
    <div class="cont justify-content-center">
        <h1>{{ __('messages.thank_you') }}</h1>
        <h3>{{ __('messages.finish') }}</h3>
        <h2 style="color: blue">{{ __('messages.result') }} {{ $percentage }}%</h2>
    </div>
@endif
</body>
</x-layout>
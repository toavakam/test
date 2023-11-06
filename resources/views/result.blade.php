
<!DOCTYPE html>
<html>
<head>
    <title>Online Test - Finish</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .cont{
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
            padding-top: 10%;
        }
    </style>
</head>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
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
<div class="cont justify-content-center">
@if(App::currentLocale()==="en")
<h1>Thank You!</h1>
<h3>Your test has been completed.</h3>
    <h2 style="color: blue">Your result: {{ $percentage }}%</h2>
@elseif(App::currentLocale()==="lv")
    <h1>Paldies!</h1>
    <h3>Jusu tests ir pabeigts.</h3>
        <h2 style="color: blue">Jusu rezultats: {{ $percentage }}%</h2>

    @elseif(App::currentLocale()==="ru")
        <h1>Спасибо!</h1>
        <h3>Ваш тест завершен.</h3>
        <h2 style="color: blue">Ваш результат: {{ $percentage }}%</h2>

    @endif
</div>
</body>
</html>
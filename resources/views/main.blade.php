
<!DOCTYPE html>
<html>
<head>
    <title>{{$test->getQuestionsTitle($lang)}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
.log-in{
    width: 100%;
    max-width: 330px;
    padding: 15px;
    margin: auto;
    padding-top: 10%;
}
.form-floating{
    position: relative;
}
    </style>
</head>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><h2>{{$test->getQuestionsTitle($lang)}}</h2></a>
            <div>
                <ul class="navbar-nav">
                @if(App::currentLocale()==="lv")
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard', ['pk'=>$pk, 'lang' => 'en'])}}">English</a></li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('dashboard', ['pk'=>$pk, 'lang' => 'ru'])}}">Русский</a></li>
                        @elseif(App::currentLocale()==="ru")
                            <li class="nav-item"><a class="nav-link" href="{{ route('dashboard', ['pk'=>$pk, 'lang' => 'en'])}}">English</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ route('dashboard', ['pk'=>$pk, 'lang' => 'lv'])}}">Latviesu</a></li>
                            @elseif(App::currentLocale()==="en")
                                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard', ['pk'=>$pk, 'lang' => 'lv'])}}">Latviesu</a></li>
                                <li class="nav-item"> <a class="nav-link" href="{{ route('dashboard', ['pk'=>$pk, 'lang' => 'ru'])}}">Русский</a></li>
                    </ul>
                @endif
            </div>
        </div>
    </nav>
</header>
<body>
<div class="log-in text-center">
                <form action="" method="POST">
        @csrf
                    @if(App::currentLocale()==="lv")
                <h1 class="h5 mb-4 fw-normal">Ludzu ievadiet vardu un uzvradu</h1>
        <div class="form-floating">
                <input type="text" class="form-control" id="name" name="name" placeholder="Vards" required>
                <label for="name">Vards</label>
        </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Uzards" required>
                <label for="lastname">Uzvards</label>
            </div>
            <button type="submit" class="btn btn-primary btn-lg mt-4">Sakt testu</button>
                        @elseif(App::currentLocale()==="ru")
                            <h1 class="h5 mb-4 fw-normal">Введите имя и фамилию</h1>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Имя" required>
                                <label for="name">Имя</label>
                            </div>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Фамилия" required>
                                <label for="lastname">Фамилия</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg mt-4">Начать тест</button>
                            @elseif(App::currentLocale()==="en")
                                <h1 class="h5 mb-4 fw-normal">Please enter your name and lastname</h1>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                                    <label for="name">Name</label>
                                </div>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Lastname" required>
                                    <label for="lastname">Lastname</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg mt-4">Start test</button>
                    @endif
</form>
</div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>{{ $question['text'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
    .cont{
        width: 100%;
        max-width: 1000px;
        padding: 15px;
        margin: auto;
        padding-top: 10%;
    }
    .bar {
        margin-bottom: 7%;
    }
</style>
</head>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><h2>{{$test->getQuestionsTitle($lang)}}</h2></a>
            <div>
                <ul class="navbar-nav"@if(App::currentLocale()==="lv")
                        <li class="nav-item"><a class="nav-link" href="{{ route('question', ['pk'=>$pk, 'num' => $num, 'lang' => 'en'])}}">English</a></li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('question', ['pk'=>$pk, 'num' => $num, 'lang' => 'ru'])}}">Русский</a></li>
                    @elseif(App::currentLocale()==="ru")
                        <li class="nav-item"><a class="nav-link" href="{{ route('question', ['pk'=>$pk, 'num' => $num, 'lang' => 'en'])}}">English</a></li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('question', ['pk'=>$pk, 'num' => $num, 'lang' => 'lv'])}}">Latviesu</a></li>
                    @elseif(App::currentLocale()==="en")
                        <li class="nav-item"><a class="nav-link" href="{{ route('question', ['pk'=>$pk, 'num' => $num, 'lang' => 'lv'])}}">Latviesu</a></li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('question' , ['pk'=>$pk, 'num' => $num, 'lang' => 'ru'])}}">Русский</a></li>
                </ul>
                @endif
            </div>
        </div>
    </nav>
</header>
<body>

<div class="cont justify-content-center">
    <h5 class="text-center">
        {{$num}} / {{$bar}}
    </h5>
    <div class="progress bar" role="progressbar" aria-label="Basic example" aria-valuenow="{{$num}}" aria-valuemin="0" aria-valuemax="{{$bar}}">
        <div class="progress-bar" style="width: {{$percentage}}%"></div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    @if($error == __('messages.select_at_least_one_answer'))
                        <p>{{ $error }}</p>
                    @elseif($error == __('messages.duplicate_order_numbers'))
                        <p>{{ $error }}</p>
                    @elseif($error == __('messages.invalid_answer_selected'))
                        <p>{{ $error }}</p>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
    @if($question['type'] === 'single-choice')
        @include('components.single_choice_question', ['question' => $question, 'pk' => $pk, 'num' => $num, 'lang' => $lang, 'userAnswer' => $userAnswer])
    @elseif($question['type'] === 'multiple-choice')
        @include('components.multiple_choice_question', ['question' => $question, 'pk' => $pk, 'num' => $num, 'lang' => $lang, 'userAnswer' => $userAnswer])
    @elseif($question['type'] === 'order')
        @include('components.order_question', ['question' => $question, 'pk' => $pk, 'num' => $num, 'lang' => $lang, 'userAnswer' => $userAnswer])
    @endif
</div>
</body>
</html>


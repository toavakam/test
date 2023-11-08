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
    <h2>{{ $question['text'] }}</h2>
    @if(isset($question['image']))
        <img src="{{ $question['image'] }}">
    @endif
    <p>{{ $question['description'] }}</p>
    @if($question['type'] === 'multiple-choice')
        <form method="post" action="{{ route('answer', ['pk' => $pk, 'num' => $num, 'lang'=>$lang]) }}">
            @csrf
            @foreach($question['answers'] as $answer)
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="answer_{{ $answer['id'] }}" name="a{{ $question['id'] }}[]" value="{{ $answer['id'] }}"
                           @if(isset($userAnswer) && in_array($answer['id'], $userAnswer)) checked @endif>
                    <label class="form-check-label" for="answer_{{ $answer['id'] }}">{{ $answer['value'] }}</label>
                </div>
            @endforeach
            @if($num > 1)
                <a href="{{ route('question', ['pk' => $pk, 'num' => $num - 1, 'lang' => $lang]) }}" class="btn btn-primary mt-4">{{ __('messages.previous_question') }}</a>
            @endif
            <button type="submit" class="btn btn-primary mt-4">{{ __('messages.next_question') }}</button>
        </form>
    @elseif($question['type'] === 'single-choice')
        <form method="post" action="{{ route('answer', ['pk' => $pk, 'num' => $num, 'lang'=>$lang]) }}">
            @csrf
            @foreach($question['answers'] as $answer)
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="answer_{{ $answer['id'] }}" name="answer" value="{{ $answer['id'] }}"
                           @if(isset($userAnswer) && $userAnswer == $answer['id']) checked @endif>
                    <label class="form-check-label" for="answer_{{ $answer['id'] }}">{{ $answer['value'] }}</label>
                </div>
            @endforeach
            @if($num > 1)
                <a href="{{ route('question', ['pk' => $pk, 'num' => $num - 1, 'lang' => $lang]) }}" class="btn btn-primary mt-4">{{ __('messages.previous_question') }}</a>
            @endif
            <button type="submit" class="btn btn-primary mt-4">{{ __('messages.next_question') }}</button>
        </form>
    @elseif($question['type'] === 'order')
        <form method="post" action="{{ route('answer', ['pk' => $pk, 'num' => $num, 'lang'=>$lang]) }}">
            @csrf
            @foreach($question['answers'] as $answer)
                <div class="mb-3">
                    <label for="answer_{{ $answer['id'] }}" class="form-label">{{ $answer['value'] }}</label>
                    <div class="dropdown">
                        <select class="form-select" aria-label="Default select example" name="{{ $question['id'] }}[{{ $answer['id'] }}]">
                            <option value="">---</option>
                            @for($i = 1; $i <= count($question['answers']); $i++)
                                <option value="{{ $i }}" @if(isset($userAnswer[$answer['id']]) && $userAnswer[$answer['id']] == $i) selected @endif>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            @endforeach
            @if($num > 1)
                <a href="{{ route('question', ['pk' => $pk, 'num' => $num - 1, 'lang' => $lang]) }}" class="btn btn-primary mt-4">{{ __('messages.previous_question') }}</a>
            @endif
            <button type="submit" class="btn btn-primary mt-4">{{ __('messages.next_question') }}</button>
        </form>
    @endif
</div>
</body>
</html>


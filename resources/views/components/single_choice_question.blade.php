@props(['question', 'pk', 'num', 'userAnswer'])

<h1 class="questiontext">{{ $question['text'] }}</h1>
@if(isset($question['image']))
<div class="text-center">
    <img class="align-items-center" src="{{ $question['image'] }}" alt="Question Image">
</div>
@endif
<p class="questiondescrip">{{ $question['description'] }}</p>
<form method="post" action="{{ route('answer', ['pk' => $pk, 'num' => $num, 'lang'=> app()->currentLocale()]) }}">
    @csrf
    @foreach($question['answers'] as $answer)
        <div class="form-check py-2">
            <input type="radio"
                   class="btn-check answerbtn"
                   id="answer_{{ $answer['id'] }}"
                   name="answer"
                   value="{{ $answer['id'] }}"
                   @checked(old('answer', $userAnswer) == $answer['id'])>
            <label class="btn btn-outline-secondary answerbtn" for="answer_{{ $answer['id'] }}">
                {{ $answer['value'] }}
            </label>
        </div>
    @endforeach

    <div class="d-flex flex-row-reverse justify-content-between mt-4">
        <button type="submit" class="btn btn-primary btn-bottom">
            {{ __('messages.next_question') }} &raquo;
        </button>
        @if($num > 1)
            <a class="btn btn-primary btn-bottom d-flex align-items-center" href="{{ route('question', ['pk' => $pk, 'num' => $num - 1, 'lang' => app()->currentLocale()]) }}">
                &laquo; {{ __('messages.previous_question') }}
            </a>
        @endif
    </div>
</form>


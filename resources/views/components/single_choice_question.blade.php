@props(['question', 'pk', 'num', 'lang', 'userAnswer'])
<h2>{{ $question['text'] }}</h2>
@if(isset($question['image']))
    <div class="text-center">
        <img src="{{ $question['image'] }}">
    </div>
@endif
<p>{{ $question['description'] }}</p>
<form method="post" action="{{ route('answer', ['pk' => $pk, 'num' => $num, 'lang'=> $lang]) }}">
    @csrf
    @foreach($question['answers'] as $answer)
        <div class="form-check">
            <input type="radio" class="form-check-input" id="answer_{{ $answer['id'] }}" name="answer" value="{{ $answer['id'] }}" @checked($userAnswer === $answer['id'])>
            <label class="form-check-label" for="answer_{{ $answer['id'] }}">
                {{ $answer['value'] }}
            </label>
        </div>
    @endforeach
    <div class="d-flex flex-row-reverse justify-content-between mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            {{ __('messages.next_question') }} &raquo;
        </button>
        @if($num > 1)
            <a href="{{ route('question', ['pk' => $pk, 'num' => $num - 1, 'lang' => $lang]) }}" class="btn btn-primary btn-lg">
                &laquo; {{ __('messages.previous_question') }}
            </a>
        @endif
    </div>
</form>


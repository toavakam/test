@props(['question', 'pk', 'num', 'lang', 'userAnswer'])
<h1 class="questiontext">{{ $question['text'] }}</h1>
<p class="questiondescrip">{{ $question['description'] }}</p>
<form method="post" action="{{ route('answer', ['pk' => $pk, 'num' => $num, 'lang'=> $lang]) }}">
    @csrf
    @foreach($question['answers'] as $answer)
        <div class="form-check py-2">
            <input type="radio" class="btn-check answerbtn" id="answer_{{ $answer['id'] }}" name="answer" value="{{ $answer['id'] }}" {{ old('answer', $userAnswer) == $answer['id'] ? 'checked' : '' }}>
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
        <button class="btn btn-primary btn-bottom">
            <a href="{{ route('question', ['pk' => $pk, 'num' => $num - 1, 'lang' => $lang]) }}" class="btn btn-primary btn-lg">
                &laquo; {{ __('messages.previous_question') }}
            </a>
</button>
        @endif
    </div>
</form>


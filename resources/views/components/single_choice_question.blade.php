<h2>{{ $question['text'] }}</h2>
@if(isset($question['image']))
    <img src="{{ $question['image'] }}">
@endif
<p>{{ $question['description'] }}</p>
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


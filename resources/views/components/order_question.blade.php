<h2>{{ $question['text'] }}</h2>
@if(isset($question['image']))
    <img src="{{ $question['image'] }}">
@endif
<p>{{ $question['description'] }}</p>
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
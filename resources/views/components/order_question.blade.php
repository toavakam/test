@props(['question', 'pk', 'num', 'lang', 'userAnswer'])

<h2>{{ $question['text'] }}</h2>
@if(isset($question['image']))
    <img src="{{ $question['image'] }}">
@endif
<p>{{ $question['description'] }}</p>
<form method="post" action="{{ route('answer', ['pk' => $pk, 'num' => $num, 'lang'=>$lang]) }}">
    @csrf
    @foreach($question['answers'] as $answer)
            <label for="answer_{{ $answer['id'] }}" class="form-label">{{ $answer['value'] }}</label>
            <div class="dropdown">
                <select class="form-select" aria-label="Default select example" name="{{ $question['id'] }}[{{ $answer['id'] }}]">
                    <option value="">---</option>
                    @for($i = 1; $i <= count($question['answers']); $i++)
                        <option value="{{ $i }}" @if(isset($userAnswer[$answer['id']]) && $userAnswer[$answer['id']] == $i) selected @endif>{{ $i }}</option>
                    @endfor
                </select>
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
</form>
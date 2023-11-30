@props(['question', 'pk', 'num', 'lang', 'userAnswer'])
<h1 class="questiontext">{{ $question['text'] }}</h1>
@if(isset($question['image']))
    <img src="{{ $question['image'] }}" alt="Question Image">
@endif
<p class="questiondescrip">{{ $question['description'] }}</p>

<form method="post" action="{{ route('answer', ['pk' => $pk, 'num' => $num, 'lang'=>$lang]) }}">
    @csrf
    @foreach($question['answers'] as $answer)
        <div class="form-group">
            <label for="answer_{{ $answer['id'] }}">{{ $answer['value'] }}</label>
            <input type="text" class="form-control" id="answer_{{ $answer['id'] }}" name="answer_{{ $answer['id'] }}" value="{{ old('answer_'.$answer['id'], $userAnswer[$answer['id']] ?? '') }}" required>
        </div>
    @endforeach
    <div class="d-flex flex-row-reverse justify-content-between mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            {{ __('messages.next_question') }} &raquo;
        </button>
        @if($num > 1)
            <a class="btn btn-primary btn-bottom d-flex align-items-center" href="{{ route('question', ['pk' => $pk, 'num' => $num - 1, 'lang' => $lang]) }}" class="btn btn-primary btn-lg">
                &laquo; {{ __('messages.previous_question') }}
            </a>
        @endif
    </div>
</form>

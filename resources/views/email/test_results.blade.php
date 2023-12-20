<style>
    .correct { color: green; }
    .incorrect { color: red; }
</style>
<h1>{{ $attempt->test->getQuestionsTitle('lv') }}<br>
    {{ $attempt->name }} {{ $attempt->lastname }}<br>
    {{ $attempt->created_at }}
</h1>
@foreach ($formatAnswers as $index => $formatAnswer)
    <h2>{{ $index + 1 }}. {{ $formatAnswer['question'] }}</h2>
    <ul>
        @php
            $answerClass = $formatAnswer['isCorrect'] === 'Correct' ? 'correct' : 'incorrect';
        @endphp
        @foreach(explode('<br>', $formatAnswer['formatAnswer']) as $answer)
            <li class="{{ $answerClass }}">{!! $answer !!}</li>
        @endforeach
    </ul>

    <ul>
        @if($formatAnswer['isCorrect'] !== 'Correct')
            <p>Pareizas atbildes</p>
    @foreach(explode('<br>', $formatAnswer['correctAnswers']) as $answer)
        <li class="correct">{!! $answer !!}</li>
    @endforeach
        @endif
    </ul>

@endforeach

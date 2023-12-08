
<style>
    .correct { color: green; }
    .incorrect { color: red; }
    @font-face {
        font-family: "DejaVu Sans";
        font-style: normal;
        font-weight: 400;
        src: url("{{asset('fonts/DejaVuSans.ttf')}}");
        src:
        local("DejaVu Sans"),
        local("DejaVu Sans"),
        url("{{asset('fonts/DejaVuSans.ttf')}}") format("truetype");
    }
    body {
        font-family: "DejaVu Sans";
        font-size: 12px;
    }
</style>
<body>
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
@endforeach
</body>

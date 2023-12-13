<x-layout>
    <x-slot name="title">{{ $question['text'] }} - {{ $attempt->test->getQuestionsTitle(app()->currentLocale()) }}</x-slot>

    <x-header :attempt="$attempt" :number="$num" />

    <div class="cont justify-content-center">
        <h5 class="text-center">
            {{ $num }} / {{ $questionCount }}
        </h5>
        <div class="progress bar" role="progressbar" aria-label="{{ $question['text'] }}" aria-valuenow="{{ $num }}" aria-valuemin="0" aria-valuemax="{{ $questionCount }}">
            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
        </div>

        @if ($errors->isNotEmpty())
            <div class="alert alert-danger">
                <ul class="mb-0 list-unstyled">
                    @foreach(collect($errors->all())->unique() as $error)
                        <li class="questiondescrip">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($question['type'] === 'single-choice')
            <x-single_choice_question :question="$question" :pk="$attempt->id" :num="$num" :userAnswer="$userAnswer" />
        @elseif($question['type'] === 'multiple-choice')
            <x-multiple_choice_question :question="$question" :pk="$attempt->id" :num="$num" :userAnswer="$userAnswer" />
        @elseif($question['type'] === 'order')
            <x-order_question :question="$question" :pk="$attempt->id" :num="$num" :userAnswer="$userAnswer" />
        @elseif($question['type'] === 'image-custom')
            <x-image_custom :question="$question" :pk="$attempt->id" :num="$num" :userAnswer="$userAnswer" />
        @endif
    </div>
</x-layout>

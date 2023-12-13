<x-layout>
    <x-slot name="title">{{ $question['text'] }} - {{ $test->getQuestionsTitle($lang) }}</x-slot>

    <x-header :test="$test" :attempt="$attempt" :number="$num" />

    <div class="cont justify-content-center">
        <h5 class="text-center">
            {{ $num }} / {{ $bar }}
        </h5>
        <div class="progress bar" role="progressbar" aria-label="{{ $question['text'] }}" aria-valuenow="{{ $num }}" aria-valuemin="0" aria-valuemax="{{ $bar }}">
            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors as $error)
                        <li class="questiondescrip">{{ $error }}</li>
                    @endforeach
{{--                   @php--}}
{{--                   $error = $errors->first();--}}
{{--                   @endphp--}}
{{--                    @if($error === __('messages.select_at_least_one_answer'))--}}
{{--                        <li class="questiondescrip">{{ $error }}</li>--}}
{{--                    @elseif($error === __('messages.duplicate_order_numbers'))--}}
{{--                        <li class="questiondescrip">{{ $error }}</li>--}}
{{--                    @elseif($error === __('messages.invalid_answer_selected'))--}}
{{--                        <li class="questiondescrip">{{ $error }}</li>--}}
{{--                    @elseif($error === __('messages.image_custom_field_empty'))--}}
{{--                        <li class="questiondescrip">{{ $error }}</li>--}}
{{--                    @endif--}}
                </ul>
            </div>
        @endif
        @if($question['type'] === 'single-choice')
            <x-single_choice_question :question="$question" :pk="$pk" :num="$num" :lang="$lang" :userAnswer="$userAnswer" />
        @elseif($question['type'] === 'multiple-choice')
            <x-multiple_choice_question :question="$question" :pk="$pk" :num="$num" :lang="$lang" :userAnswer="$userAnswer" />
        @elseif($question['type'] === 'order')
            <x-order_question :question="$question" :pk="$pk" :num="$num" :lang="$lang" :userAnswer="$userAnswer" />
        @elseif($question['type'] === 'image-custom')
            <x-image_custom :question="$question" :pk="$pk" :num="$num" :lang="$lang" :userAnswer="$userAnswer" />
        @endif
    </div>
</x-layout>

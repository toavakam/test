<x-layout>
    <div class="text-center" style="padding-top: 20%">
        <h3 class="card-title mb-4">@lang('messages.home_title')</h3>
        @foreach($tests as $test)
            <div class="py-3 fs-1">
                <a href="{{ route('dashboard', ['lang' => app()->currentLocale(), 'pk' => $test->id]) }}">
                    {{ $test->getQuestionsTitle(app()->currentLocale()) }}
                </a>
            </div>
        @endforeach
    </div>

    <x-footer :test="$test"/>

</x-layout>

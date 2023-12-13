<x-layout>
    <div class="text-center" style="padding-top: 20%">
            <h3 class="card-title mb-4">@lang('messages.home_title')</h>
            @foreach($tests as $test)
                <ol class="py-3" style="font-size: 35px">
                    <a href="{{ route('dashboard', ['lang' => App::getLocale(), 'pk' => $test->id]) }}">
                        {{ $test->getQuestionsTitle(App::getLocale()) }}
                    </a>
                </ol>
            @endforeach
    </div>
    <x-footer :test="$test"/>
</x-layout>

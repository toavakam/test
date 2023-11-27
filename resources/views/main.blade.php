<x-layout>
    <x-slot name="title">{{ $test->getQuestionsTitle($lang) }}</x-slot>
    <x-header :test="$test" />
    <div class="log-in card p-5 shadow-sm">
        <div class="card-body">
            <form action="" method="POST">
                @csrf
                <h3 class="card-title mb-4">@lang('messages.dashboard_title')</h3>
                <div class="form-floating relative mb-2">
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="@lang('messages.dashboard_name')" required>
                    <label for="name">@lang('messages.dashboard_name')</label>
                </div>
                <div class="form-floating relative mb-2">
                    <input type="text" class="form-control" id="lastname" name="lastname" value="{{ old('lastname') }}" placeholder="@lang('messages.dashboard_surname')" required>
                    <label for="lastname">@lang('messages.dashboard_surname')</label>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        @lang('messages.dashboard_button')
                    </button>
                </div>
            </form>
        </div>
    </div>
    <x-footer :test="$test"/>
</x-layout>
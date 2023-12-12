<?php

namespace App\View\Components;

use App\Models\Attempt;
use App\Models\Test;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Footer extends Component
{
    public function __construct(
        public ?Test $test = null,
        public ?Attempt $attempt = null,
        public ?int $number = null, )
    {
    }

    public function render(): View
    {
        return view('components.footer', [
            'lang' => App::currentLocale(),
            'languageUrls' => $this->getLanguageMenu(), ]);
    }

    private function getLanguageMenu(): array
{
    $lang = Request::input('lang');
    $routeName = Route::currentRouteName();
    $result = [];

    foreach (config('app.languages') as $code => $name) {
        if ($code === App::currentLocale()) {
            continue;
        }

        if ($routeName === 'dashboard' && $this->test !== null) {
            $params = ['lang' => $code, 'pk' => $this->test->id];
        } else {
            $params = ['lang' => $code, 'pk' => null];
        }

        $result[] = [
            route($routeName, $params),
            $name,
        ];
    }

    return $result;
}


}

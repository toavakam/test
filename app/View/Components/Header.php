<?php

namespace App\View\Components;

use App\Models\Attempt;
use App\Models\Test;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\View\Component;

class Header extends Component
{
    public function __construct(
        public ?Test $test = null,
        public ?Attempt $attempt = null,
        public ?int $number = null,
    ) {
    }

    public function render(): View
    {
        return view('components.header', [
            'lang' => App::currentLocale(),
            'languageUrls' => $this->getLanguageMenu(),
        ]);
    }

    private function getLanguageMenu(): array
    {
        $result = [];
        if ($this->attempt !== null && $this->number !== null) {
            foreach (config('app.languages') as $code => $name) {
                if ($code === App::currentLocale()) {
                    continue;
                }
                $result[] = [
                    route('question', ['lang' => $code, 'pk' => $this->attempt->id, 'num' => $this->number]),
                    $name,
                ];
            }
        } else {
            foreach (config('app.languages') as $code => $name) {
                if ($code === App::currentLocale()) {
                    continue;
                }
                $result[] = [
                    route('dashboard', ['lang' => $code, 'pk' => $this->test->id]),
                    $name,
                ];
            }
        }
        return $result;
    }
}

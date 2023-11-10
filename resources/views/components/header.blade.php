<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <h2>{{ $test->getQuestionsTitle($lang) }}</h2>
            </a>
            <div>
                <ul class="navbar-nav">
                    @foreach($languageUrls as $item)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ $item[0] }}">{{ $item[1] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</header>
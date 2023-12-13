<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <h1>{{ ($attempt->test ?? $test)?->getQuestionsTitle(app()->currentLocale()) }}</h1>
            </a>
        </div>
    </nav>
</header>
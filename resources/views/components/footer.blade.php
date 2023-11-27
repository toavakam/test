<footer>
    <div>
    <nav class="navbar navbar-expand justify-content-center">
        <ul class="navbar-nav">
            @foreach($languageUrls as $item)
                <li class="nav-item">
                    <a class="nav-link lang" href="{{ $item[0] }}">{{ $item[1] }}</a>
                </li>
            @endforeach
        </ul>
</nav>
    </div>
</footer>   
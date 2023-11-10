
<!DOCTYPE html>
<html lang="{{ app()->currentLocale() }}">
<head>
    <title>{{ $title ?? 'Dashboard' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .log-in {
            width: 100%;
            max-width: 100%;
            margin: 10% auto;
            border: 0 !important;
        }
        @media (min-width: 576px) {
            .log-in {
                max-width: 680px;
            }
        }
        .cont {
            width: 100%;
            max-width: 1000px;
            padding: 5% 15px 15px;
            margin: auto;
        }
        .bar {
            margin-bottom: 7%;
            height: 5px;
        }
    </style>
</head>
<body>

    {{ $slot }}

</body>
</html>
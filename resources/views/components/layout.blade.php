
<!DOCTYPE html>
<html lang="{{ app()->currentLocale() }}">
<head>
    <title>{{ $title ?? 'Home' }}</title>
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
                max-height: 700px; 
            }
            .card-title {
                font-size: 40px;
            }
            .form-control {
                height: 100px !important;
            }
            .form-select {
                height: 100px !important;
            }
            .btn {
                font-size: 35px; 
            }
            .questiontext{
            font-size: 70px
            }
            .questiondescrip{
            font-size: 40px
            }
            .orderlabel{
                font-size: 30px;
            }
            .answerbtn{
                font-size: 50px;
                width: 100%;
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
            height: 20px;
        }
        .lang{
            font-size: 50px;
        }
        .btn-bottom{
            height: 120px;
        }
    </style>
</head>
<body>

    {{ $slot }}
    
</body>
</html>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">


    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Survivalruns</title>

    {{--    <!-- Fonts -->--}}
    {{--    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">--}}

    {{--    <!-- Styles -->--}}
    {{--    <style>--}}
    {{--        html, body {--}}
    {{--            background-color: #fff;--}}
    {{--            color: #636b6f;--}}
    {{--            font-family: 'Nunito', sans-serif;--}}
    {{--            font-weight: 200;--}}
    {{--            height: 100vh;--}}
    {{--            margin: 0;--}}
    {{--        }--}}

    {{--        .full-height {--}}
    {{--            height: 100vh;--}}
    {{--        }--}}
    {{--    </style>--}}
</head>
<body>
<div class="container">
    @yield('content')
</div>
<!--Import jQuery before materialize.js-->
<script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
</body>
</html>

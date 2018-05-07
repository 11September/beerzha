<!doctype html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i"
          rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <title>{{ Voyager::setting('title') }}</title>
    <title>{{ Voyager::setting('description') }}</title>

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .logo{
            margin: 100px auto 50px auto;
            text-align: center;
        }

        .logo img{
            width: 120px;
            text-align: center;
            margin: 0 auto;
        }

        .wrapper{
            width:100%;
        }

        .wrapper h1{
            text-align: center;
        }

        .wrapper-content{
            max-width:100%;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="logo">
                <img src="{{ asset('img/beerzha(logo).png') }}" alt="logo">
            </div>
        </div>

        <div class="col-md-12">
            <h1 style="text-align: center">{{ $notification->title }}</h1>
        </div>

        <div class="col-md-12">
            <div class="wrapper-content">
                {!! $notification->content !!}
            </div>
        </div>
    </div>
</div>
</body>

<script
        src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous">
</script>

<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-101014788-1', 'auto');
    ga('send', 'pageview');
</script>

</html>

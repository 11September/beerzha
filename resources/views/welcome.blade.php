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

    <title>{{ Voyager::setting('title') }}</title>
    <title>{{ Voyager::setting('description') }}</title>

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .wrapper {
            height: 100vh;
            width: 100%;
            position: relative;
        }

        .bug {
            background-color: #0D141A;
            width: 100%;
            height: 250px;
        }

        .bottom {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 280px;
        }

        .table-all {
            width: 100%;
            background-color: #0D141A;
            color: white;
            /*height: calc(100vh - 137px);*/
            border-collapse: collapse;
        }

        .table-all tr td {
            padding: 25px 0;
        }

        .table-all tr {
            font-size: 194px;
            font-family: 'Roboto';
            border-bottom: 1px solid #464646;
        }

        .table-all tr td:nth-child(1),
        .table-all tr td:nth-child(2),
        .table-all tr td:nth-child(3) {
            color: white;
        }

        .green-modificator {
            color: #02a912 !important;
        }

        .red-modificator {
            color: #a02b21 !important;
        }

        .table-all tr td:nth-child(4) {
            width: 750px;
            display: inline-block;
            padding: 20px 10px;
            margin-top: 24px;
            text-align: center;
        }

        .table-all tr td:nth-child(1) span {
            padding-left: 400px;
        }

        .green-bg {
            background-color: #029112;
        }

        .red-bg {
            background-color: #a02b21;
        }

        marquee {
            font-size: 180px;
            background-color: #2C353C;
            color: white;
            height: 280px;
            /*padding-top: 15px;*/
            padding-bottom: 5px;
            width: 78%;
            display: inline-block;
            font-family: 'Open Sans';
        }

        .code-field {
            display: inline-block;
            margin: 0;
            position: absolute;
            font-size: 180px;
            padding-bottom: 25px;
            width: 22%;
            background-color: #2c353c;
            color: white;
            height: 100%;
        }

        .code-field p {
            height: 100%;
            margin: 0;
            padding-top: 12px;
            padding-left: 20px;
            font-family: 'Open Sans';
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="wrapper-table">
        <table class="table-all">

            @foreach($beers as $beer)
                <tr>
                    <td><span>{{ $beer->title }}</span></td>
                    <td><span>{!! $beer->price_stable !!}</span></td>
                    <td class="green-modificator">
                    <span>

                        <?php
                        if(!$beer->price_quotations){
                            $beer->price_quotations = $beer->stable;
                        }

                        if($beer->share == "enable" && $beer->share_count && $beer->share_count > 0) {
                            $beer->price_quotations = 0.00;
                            $beer->percent = 100;
                        }
                        ?>

                        {!! $beer->price_quotations !!}
                    </span>
                    </td>
                    <td class="
                    @if(($beer->share == "enable" && $beer->share_count && $beer->share_count > 0))
                            green-bg
                    @elseif($beer->price_quotations > $beer->price_stable)
                            green-bg
                    @elseif($beer->price_quotations === $beer->price_stable)
                            green-bg
                    @else
                            red-bg
                        @endif
                            ">
                    <span>
                        {!! $beer->percent !!} %
                    </span>
                    </td>
                </tr>
            @endforeach
        </table>

        <div class="bug"></div>
    </div>


    <div class="bottom">
        <marquee id="ticker" bgcolor="silver" height="150">{{ $ticker->value }}</marquee>

        <div class="code-field">
            <p> Code: <span id="code">{{ $code->value }}</span></p>
        </div>
    </div>

</div>
</body>

<script
        src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous">
</script>

<script type="text/javascript">

    $(document).ready(function () {
        function scroll(speed) {
            $('html, body').animate({scrollTop: $(document).height() - $(window).height()}, speed, function () {
                $(this).animate({scrollTop: 0}, speed);
            });
        }

        speed = 11000;

        scroll(speed);
        setInterval(function () {
            scroll(speed)
        }, speed);
    });

    var auto_refresh_line = setInterval(
        function () {
            $.ajax({
                url: "/ticker",
                type: "GET",
                success: function (data) {
                    $("#ticker").text(data.value);
                },
                error: function (xhr, status) {
                    $("div").html("<span>" + status + "</span>");
                }
            });
        }, 300000);

    var auto_refresh_code = setInterval(
        function () {
            $.ajax({
                url: "/code",
                type: "GET",
                success: function (data) {
                    $("#code").text(data.value);
                },
                error: function (xhr, status) {
                    $("div").html("<span>" + status + "</span>");
                }
            });
        }, 3600000);

    var auto_refresh = setInterval(
        function () {
            $.ajax({
                url: "/beers",
                type: "GET",
                success: function (data) {
                    manageRow(data.data);
                },
                error: function (xhr, status) {
                    $("div").html("<span>" + status + "</span>");
                }
            });
        }, 30000);

    function manageRow(data) {
        var rows = '';

        var classStyle = 'red-bg';

        $.each(data, function (key, value) {

            if (!value.percent) {
                value.percent = 0;
            }

            if (value.price_quotations == null) {
                value.price_quotations = value.price_stable;
            }

            if ((value.price_quotations == 0 || value.price_quotations == 0.00) && (value.share == 'enable' && value.share_count > 0)) {
                classStyle = "green-bg";
            }

            else if(value.price_quotations == value.price_stable) {
                classStyle = "green-bg";
            }

            else if (value.price_quotations > value.price_stable) {
                classStyle = "green-bg";
            }

            else {
                classStyle = "red-bg";
            }

            rows = rows + '<tr>';
            rows = rows + '<td><span>' + value.title + '</span></td>';
            rows = rows + '<td><span>' + value.price_stable + '</span></td>';
            rows = rows + '<td class="green-modificator"><span>' + value.price_quotations + '</span></td>';
            rows = rows + '<td class="' + classStyle + '"><span>' + value.percent + ' %</span></td>';
            rows = rows + '</tr>';
        });

        $(".table-all").html(rows);
    }
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

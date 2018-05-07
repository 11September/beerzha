<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            font-size: 100%;
            font-family: 'Avenir Next', "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
            line-height: 1.65;
        }

        img {
            max-width: 100%;
            margin: 0 auto;
            display: block;
        }

        body,
        .body-wrap {
            width: 100% !important;
            height: 100%;
            background: #efefef;
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
        }

        a {
            color: #71bc37;
            text-decoration: none;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .button {
            display: inline-block;
            color: white;
            background: #71bc37;
            border: solid #71bc37;
            border-width: 10px 20px 8px;
            font-weight: bold;
            border-radius: 4px;
        }

        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 20px;
            line-height: 1.25;
        }

        h1 {
            font-size: 32px;
        }

        h2 {
            font-size: 28px;
        }

        h3 {
            font-size: 24px;
        }

        h4 {
            font-size: 20px;
        }

        h5 {
            font-size: 16px;
        }

        p, ul, ol {
            font-size: 16px;
            font-weight: normal;
            margin-bottom: 20px;
        }

        .container {
            display: block !important;
            clear: both !important;
            margin: 0 auto !important;
            max-width: 580px !important;
        }

        .container table {
            width: 100% !important;
            border-collapse: collapse;
        }

        .container .masthead {
            padding: 80px 0;
            background: #71bc37;
            color: white;
        }

        .container .masthead h1 {
            margin: 0 auto !important;
            max-width: 90%;
            text-transform: uppercase;
        }

        .container .content {
            background: white;
            padding: 30px 35px;
        }

        .container .content.footer {
            background: none;
        }

        .container .content.footer p {
            margin-bottom: 0;
            color: #888;
            text-align: center;
            font-size: 14px;
        }

        .container .content.footer a {
            color: #888;
            text-decoration: none;
            font-weight: bold;
        }

        .wrapper {
            background-color: #FFFFFF;
            width: 100%;
        }

        .image-wrapper{
            background-image:url('http://beerzha.com.ua/storage/settings/June2017/yZncYZnGGMjLv3zxPF6n.jpg');
        }
    </style>


</head>
<body>
<div class="wrapper">
    <div class="image-wrapper">

        <table class="body-wrap">
            <tr>
                <td class="container">

                    <!-- Message start -->
                    <table>
                        <tr>
                            <td class="content">

                                <h1 style="text-align: center; width: 100%;">Дякую за пароль для
                                    відновлення {{ $user->email }}</h1>
                                <h2>Ваш новий пароль: {{ $new_password }}</h2>

                                <p>Це лист, ви отримали з мобільному додатку. Відвідайте <a href="http://google.com">Gold
                                        Master</a>.</p>

                                <p><em>Beerzha</em></p>

                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
            <tr>
                <td class="container">
                    <table>
                        <tr>
                            <td class="content footer" align="center">
                                <p>Sent by <a href="http://google.com">Beerzha</a></p>
                                <p><a href="mailto:">beerbirzha@gmail.com</a></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
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

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 100%;
        }

        .logo {
            margin: 100px auto 50px auto;
            text-align: center;
        }

        .logo img {
            width: 120px;
            text-align: center;
            margin: 0 auto;
        }

        .card{
            margin-top: 20px;
        }

        .wrapper h1 {
            text-align: center;
        }

        .wrapper-content {
            max-width: 100%;
        }

        .card-title, .card-text {
            text-align: center;
        }

        .none {
            display: none;
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

            <h1 style="text-align: center">Зачисление бонусов!</h1>
        </div>


        @foreach($orders as $order)
            <div class="col-md-4">
                <div class="card" style="width:100%">
                    <img class="card-img-top" src="{{ asset('storage/' . $order->dish->image) }}" alt="Card image">
                    <div class="card-body">
                        <h4 class="card-title">Цена: {{ $order->dish->price }}</h4>
                        <h4 class="card-title">Кол-во: {{ $order->amount }}</h4>
                        <p class="card-text">{{ $order->dish->title }}</p>
                    </div>
                </div>
            </div>
        @endforeach


        <div class="col-md-12">
            <form method="post" action="{{ action('BonusesController@getOrders') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="exampleFormControlInput1">Общая сума за заказ</label>
                    <input disabled name="price" type="number" value="{{ $total }}" class="form-control"
                           id="exampleFormControlInput1"
                           placeholder="100 грн.">
                </div>

                <div class="form-group">
                    <input hidden="hidden" name="user_id" value="{{ $bonusToken }}" type="text"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label for="exampleFormControlInput2">Код сотрудника</label>
                    <input name="code" type="password" class="form-control" id="exampleFormControlInput2"
                           placeholder="100 грн.">
                </div>

                <div class="form-group none">
                    <select name="orders[]" multiple="multiple" class="form-control">

                        @foreach($orders as $order)
                            <option selected="selected" value="{{ $order->id }}"></option>
                        @endforeach

                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Отправить</button>
            </form>
        </div>

    </div>
</div>
</body>

<script
        src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous">
</script>

</html>

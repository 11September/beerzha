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

        .card {
            margin-top: 15px;
        }

        .logo {
            margin: 50px auto 50px auto;
            text-align: center;
        }

        .logo img {
            width: 120px;
            text-align: center;
            margin: 0 auto;
        }

        .wrapper {
            width: 100%;
        }

        .wrapper h1 {
            text-align: center;
        }

        .wrapper-content {
            max-width: 100%;
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

        @php
            $total_item = 0;
            $total = 0;
        @endphp

        @foreach($orders as $order)
            <div class="col-md-4">
                <div class="card" style="width:100%">
                    <div class="card-body">
                        <h4 style="text-align: center" class="card-title">Цена: {{ $order->dish->price }}</h4>
                        <h4 style="text-align: center" class="card-title">Кол-во: {{ $order->amount }}</h4>
                        <p style="text-align: center" class="card-text">{{ $order->dish->title }}</p>
                        <img style="width: 50%;height: 120px;margin: 0 auto;text-align: center;display: flex;"
                             src="{{ asset('storage/' . $order->dish->image) }}" alt="{{ $order->dish->title }}">
                    </div>
                </div>
            </div>

            @php
                $total_item = $order->dish->price * $order->amount;
                $total += $total_item;
            @endphp

        @endforeach

        <div class="col-md-12">
            <div class="wrapper">
                <h1 style="text-align: center">Покупка за бонусы!</h1>

                <form method="post" action="{{ action('BonusesController@spendBonuses') }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="exampleFormControlInput1">Общая сума за заказ</label>
                        <input name="price" value="{{ $total }}" type="number" class="form-control"
                               id="exampleFormControlInput1"
                               placeholder="грн.">
                    </div>

                    <div class="form-group">
                        <input hidden="hidden" name="user_id" value="{{ $bonusToken }}" type="text"
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <select hidden="hidden" name="orders_id[]" multiple class="form-control" id="exampleSelect2">
                            @foreach($orders as $order)
                                <option selected="selected" value="{{ $order->id }}"></option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlInput2">Код сотрудника</label>
                        <input name="code" type="password" class="form-control" id="exampleFormControlInput2"
                               placeholder="****">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Отправить</button>
                    </div>
                </form>
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

</html>

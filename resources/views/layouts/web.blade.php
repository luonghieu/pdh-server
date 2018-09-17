<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Cheers</title>
    </head>
    <body>
      <div>
        @yield('web.content')
      </div>
    </body>
    <script src="{{ mix('js/web.js') }}" type="text/javascript"></script>
</html>

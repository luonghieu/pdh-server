<html>
<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/custom.css') }}">
</head>

<body>
  @yield('web.extra')
  @include('web.partials.header')

  <main id="@yield('screen.id')">
    @yield('web.content')
  </main>

  @include('web.partials.footer')
  <script src="{{ mix('js/web.js') }}" type="text/javascript"></script>
</body>
</html>

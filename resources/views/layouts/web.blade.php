<html>
<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/mmenu/jquery.mmenu.all.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/slick/slick-theme.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/slick/slick.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/ge_1.css') }}">
</head>

<body id="@yield('controller.id')">
  @yield('web.extra')
  <div id="page">
    @include('web.partials.header')

    <main id="@yield('screen.id')" class="@yield('screen.class')">
      @yield('web.content')
    </main>

    @include('web.partials.footer')
  </div>
  @yield('web.script')
  <script src="{{ mix('js/web.js') }}" type="text/javascript"></script>
  @yield('web.extra_js')
</body>
</html>

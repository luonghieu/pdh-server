<html>
<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/mmenu/jquery.mmenu.all.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/slick/slick-theme.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/slick/slick.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/custom.css') }}">
  @yield('web.extra_css')
  <script>
      (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NKVKVFK');
  </script>
  <script>
    window.App = {!! json_encode([
          'api_url' => config('common.api_url')
      ]) !!};
  </script>
</head>

<body id="@yield('controller.id')">
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NKVKVFK"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
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
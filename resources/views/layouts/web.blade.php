<html>
<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
  <meta name="keywords" content="">
  <meta name="description" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/mmenu/jquery.mmenu.all.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/slick/slick-theme.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/slick/slick.css') }}">
  <!-- link to the custom styles for SqPaymentForm -->
  <link rel="stylesheet" type="text/css" href="{{ mix('assets/web/css/card_square.min.css') }}">
  <link href="{{ mix('assets/web/css/web.css') }}" rel="stylesheet">

  <link rel="shortcut icon" href="{{ asset('/ld/images/cast/favicon/favicon.png') }}">

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
          'api_url' => config('common.api_url'),
          'payment_service' => config('common.payment_service')
      ]) !!};
  </script>
  <script src="/assets/admin/js/exif.js" type="text/javascript"></script>
</head>

<body id="@yield('controller.id')" class="@yield('controller.class')">
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NKVKVFK"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  @yield('web.extra')
  <div id="page">
      @if (Auth::check())
        @php
          $user = Auth::user();
        @endphp

        @if (!empty($user->date_of_birth))
          @include('web.partials.header')
        @endif
      @endif
    <main id="@yield('screen.id')" class="@yield('screen.class')">
      @yield('web.content')
    </main>

    @if(isset($token))
    <script>
      window.localStorage.setItem('access_token', '{{ $token }}');
    </script>
    @endif

    @if(config('common.payment_service') == 'telecom_credit')
      @include('web.partials.telecom_credit')
    @endif

    @include('web.partials.footer')
  </div>
  @yield('web.script')
  <script src="{{ mix('js/web.js') }}" type="text/javascript"></script>
  @yield('web.extra_js')
</body>
</html>

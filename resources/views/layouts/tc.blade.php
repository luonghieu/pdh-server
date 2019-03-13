<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>@yield('title')</title>
  <meta http-equiv="Content-Type" content="text/html; charset=shift_jis" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <link rel="shortcut icon" href="{{ asset('/ld/images/cast/favicon/favicon.png') }}">
  <link href="{{ asset('assets/web/css/web.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/web/css/tc.css') }}" rel="stylesheet">
  @yield('web.extra_css')
  <script src="{{ asset('assets/web/js/jquery.js') }}" type="text/javascript"></script>
</head>

<body id="@yield('controller.id')" class="@yield('controller.class')">
  @yield('web.extra')
  <div id="page">
    <main id="@yield('screen.id')" class="@yield('screen.class')">
      @yield('web.content')
    </main>
  </div>

  <script src="{{ asset('assets/web/js/tc.js') }}" type="text/javascript"></script>
</body>
</html>

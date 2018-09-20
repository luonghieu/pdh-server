<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CheersAdmin">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="CleverAdmin, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="" >
    <title>Cheers Admin</title>
    <link rel="shortcut icon" href="/assets/admin/img/logo/icon.png">
    <!-- Bootstrap core CSS -->
    <link href="/assets/admin/css/bootstrap.min.css" rel="stylesheet">
    <!-- page css files -->
    <link href="/assets/admin/css/font-awesome.min.css" rel="stylesheet">
    <link href="/assets/admin/css/jquery-ui.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/assets/admin/css/style.min.css" rel="stylesheet">
    <!-- user-info css  -->
    <link href="{{ mix('/bundle/css/all.css') }}" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

    <div class="container-fluid content">
      @yield('auth.content')
    </div>

    <!--<![endif]-->
    <!--[if !IE]>-->
    <script type="text/javascript">
      window.jQuery || document.write("<script src='/assets/admin/js/jquery-2.1.0.min.js'>"+"<"+"/script>");
    </script>
    <script src="/assets/admin/js/jquery-2.1.0.min.js"></script>
    <!--<![endif]-->
    <script src="/assets/admin/js/jquery-migrate-1.2.1.min.js"></script>
    <script src="/assets/admin/js/bootstrap.min.js"></script>
    <!-- page scripts -->
    <script src="/assets/admin/js/jquery-ui.min.js"></script>
    <script src="/assets/admin/js/jquery.sparkline.min.js"></script>
    <!-- page scripts -->
    <script src="/assets/admin/js/jquery.icheck.min.js"></script>
    <!-- theme scripts -->
    <!-- inline scripts related to this page -->
    <script src="/assets/admin/js/pages/table.js"></script>
    <script src="/assets/admin/js/custom.min.js"></script>
    <script src="/assets/admin/js/core.min.js"></script>
    <!-- inline scripts related to this page -->
    <script src="/assets/admin/js/pages/login.js"></script>
    <!-- end: JavaScript-->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
  </body>
</html>

<!-- start: Header -->
<div class="navbar" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href=""><h3 class="title-logo">Cheers</h3></a>
    </div>

    <ul class="nav navbar-nav navbar-right">
      @if(Auth::user())
      <li><a href="{{ route('admin.logout') }}"><i class="fa fa-power-off"></i></a></li>
      @endif
    </ul>
    <div class="notify">
      <a  class='bag' href="">
      <img src="/assets/admin/img/logo/notifications.png" alt="notifications">
      <span class="indicator">1</span>
      </a>
    </div>
      <div class="clearfix"></div>
  </div>
</div>
<div class="col-lg-2 col-sm-6 col-xs-6 col-xxs-12 notifications" >
  <div class="smallstat">
   <span class="value">
    <a href="">NGワードが発言されました。</a>
  </span>
  </div>
</div><!--/col-->
<!-- end: Header -->

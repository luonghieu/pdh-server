@extends('layouts.auth')
@section('auth.content')
<div class="row login">
  <div id="content" class="col-sm-12 full">
    <div class="row">
      <div class="login-box">
        <div class="login-title-wrap">
          <h1 class="login-title">Cheers</h1>
        </div>
        <div class="text-with-hr">
          <span>ようこそ</span>
        </div>
        @if(Session::has('msg'))
        <div class="alert alert-success fade in">
          <button data-dismiss="alert" class="close close-sm" type="button">
              <i class="icon-remove"></i>
          </button>
          <strong>{{ Session::get('msg') }}</strong>
        </div>
        @endif
        <form class="form-horizontal login" action="{{ route('admin.login') }}" method="POST" data-toggle="validator">
          {{ csrf_field() }}
          <fieldset class="col-sm-12">
            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
              <div class="controls row">
                <div class="input-group col-sm-12">
                  <input type="email" name="email" class="form-control" id="email" placeholder="Email" value="{{ old('email') }}" />
                </div>
                @if ($errors->has('email'))
                  <span class="help-block">{{ $errors->first('email') }}</span>
                @endif
              </div>
            </div>
            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
              <div class="controls row">
                <div class="input-group col-sm-12">
                  <input type="password" name="password" class="form-control" id="password" placeholder="Password" />
                </div>
                @if ($errors->has('password'))
                  <span class="help-block">{{ $errors->first('password') }}</span>
                @endif
              </div>
            </div>
            <div class="row">
              <button type="submit" class="btn btn-lg btn-primary col-xs-12">Login</button>
            </div>
          </fieldset>
        </form>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>
@endsection

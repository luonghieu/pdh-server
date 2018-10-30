@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-notification-schedules')
        <div class="clearfix"></div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <div class="search">
            <div class="info-table col-lg-8">
              <form action="{{ route('admin.notification_schedules.store') }}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="row">
                  <div class="col-sm-12 init-inline-flex">
                    <label class="col-sm-2 init-m-auto">投稿日時: </label>
                    <div class="input-group date col-sm-10" id="datetimepicker">
                      <input type="text" class="form-control" name="send_date" data-date-format="YYYY/MM/DD HH:mm" placeholder="yyyy/mm/dd hh:mm" />
                      <span class="input-group-addon init-border">
                        <span class="glyphicon glyphicon-calendar init-glyphicon"></span>
                      </span>
                    </div>
                  </div>
                </div>
                @if ($errors->has('send_date'))
                  <div class="error">
                    <span>{{ $errors->first('send_date') }}</span>
                  </div>
                @endif
                <div class="row">
                  <div class="col-sm-12 init-inline-flex init-mt">
                    <label class="col-sm-2 init-m-auto">タイトル: </label>
                    <div class="col-sm-10 p-0">
                      <input type="text" class="form-control" placeholder="タイトル" name="title" value="">
                    </div>
                  </div>
                </div>
                @if ($errors->has('title'))
                  <div class="error">
                    <span>{{ $errors->first('title') }}</span>
                  </div>
                @endif
                <div class="row p-0">
                  <div class="col-sm-12 init-mt">
                    <textarea class="col-sm-12" name="content"></textarea>
                  </div>
                </div>
                @if ($errors->has('content'))
                  <div class="error">
                    <span>{{ $errors->first('content') }}</span>
                  </div>
                @endif
                <div class="col-sm-12 p-0">
                  <div class="init-m">
                    <label class="css-m-auto">ステータスを変更: </label>
                    <select name="status">
                      <option value="">全て</option>
                      @foreach($notificationScheduleStatus as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                @if ($errors->has('status'))
                  <div class="error">
                    <span>{{ $errors->first('status') }}</span>
                  </div>
                @endif
                <div class="col-sm-12 p-0">
                  <div class="init-m">
                    <button type="submit" class="btn-register init-m">登録</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
@section('admin.js')
<script src="https://cdn.ckeditor.com/4.10.1/standard/ckeditor.js"></script>
<script>
  CKEDITOR.replace('content');
</script>
@stop
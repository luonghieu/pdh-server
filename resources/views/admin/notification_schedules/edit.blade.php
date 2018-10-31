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
              <form action="{{ route('admin.notification_schedules.update', $notificationSchedule->id) }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="row">
                  <div class="col-sm-12 init-inline-flex">
                    <label class="col-sm-2 init-m-auto">投稿日時: </label>
                    <div class="input-group date col-sm-10" id="datetimepicker">
                      <input type="text" class="form-control" name="send_date" value="{{ $notificationSchedule->send_date }}" data-date-format="YYYY/MM/DD HH:mm" placeholder="yyyy/mm/dd hh:mm" />
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
                      <input type="text" class="form-control" placeholder="タイトル" name="title" value="{{ $notificationSchedule->title }}">
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
                    <textarea class="col-sm-12" name="content">{!! $notificationSchedule->content !!}</textarea>
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
                      @foreach($notificationScheduleStatus as $key => $value)
                        @php
                          $selected = '';
                          if ($notificationSchedule->status == $key) {
                            $selected = "selected='selected'";
                          }
                        @endphp
                        <option value="{{ $key }}" {{ $selected }} >{{ $value }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-sm-12 p-0">
                  <div class="init-m">
                    <label class="css-m-auto">送信先: </label>
                    <select name="send_to">
                      @foreach($notificationScheduleSendTo as $key => $value)
                        @php
                          $selected = '';
                          if ($notificationSchedule->send_to == $key) {
                            $selected = "selected='selected'";
                          }
                        @endphp
                        <option value="{{ $key }}" {{ $selected }} >{{ $value }}</option>
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
                    <button type="submit" class="btn-update init-m">登録</button>
                  </div>
                </div>
              </form>
              <div class="col-sm-12 p-0">
                <div class="init-m init-pull-left">
                  <a href="javascript:void(0)" class="btn btn-danger" id="link-del-notification-schedule" data-id="{{ $notificationSchedule->id }}" data-toggle="modal" data-target="#delNotificationSchedule">削除する</a>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>

<div class="modal fade" id="delNotificationSchedule" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p>本当に削除しますか？</p>
      </div>
      <form action="{{ route('admin.notification_schedules.delete', $notificationSchedule->id) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <input type="hidden" name="type" value="{{ request()->type }}">
        <div class="modal-footer">
          <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-accept">削除する</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@section('admin.js')
    <script src="https://cdn.ckeditor.com/4.7.3/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.plugins.addExternal( 'lineutils', '/assets/admin/js/ckeditor_plugin/lineutils/', 'plugin.js' );
        CKEDITOR.plugins.addExternal( 'widgetselection', '/assets/admin/js/ckeditor_plugin/widgetselection/', 'plugin.js' );
        CKEDITOR.plugins.addExternal( 'widget', '/assets/admin/js/ckeditor_plugin/widget/', 'plugin.js' );
        CKEDITOR.plugins.addExternal( 'image2', '/assets/admin/js/ckeditor_plugin/image2/', 'plugin.js' );
        CKEDITOR.plugins.addExternal( 'justify', '/assets/admin/js/ckeditor_plugin/justify/', 'plugin.js' );
        CKEDITOR.config.filebrowserImageUploadUrl = '{!! route('admin.notification_schedules.upload').'?_token=' . csrf_token() !!}';
        CKEDITOR.replace('content', {
            extraPlugins: 'widget,widgetselection,lineutils,image2,justify'
        });
    </script>
@stop

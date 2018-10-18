@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <div class="col-lg-12">
            <div class="list-avatar">
              @php
                $avatars = $cast->avatars->reverse();
                $avatars = $avatars->slice(0, 2);
              @endphp
              @foreach ($avatars as $avatar)
                <img src="{{ @getimagesize($avatar->path) ? $avatar->path :'/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="avatar">
              @endforeach
            </div>
          </div>
          <div class="clearfix"></div>
          <div class="info-table col-lg-6">
            <table class="table table-bordered">
              <!--  table-striped -->
              <tr>
                <th>ユーザーID</th>
                <td>{{ $cast->id }}</td>
              </tr>
              <tr>
                <th>お名前</th>
                <td>{{ $cast->nickname }}</td>
              </tr>
              <tr>
                <th>性別</th>
                <td>{{ App\Enums\UserGender::getDescription($cast->gender) }}</td>
              </tr>
              <tr>
                <th>生年月日</th>
                <td>{{ ($cast->date_of_birth) ? Carbon\Carbon::parse($cast->date_of_birth)->format('Y年m月d日') : "" }}</td>
              </tr>
              <tr>
                <th>年齢</th>
                <td>{{ $cast->age }}</td>
              </tr>
              <tr>
                <th>職業</th>
                <td>{{ $cast->job ? $cast->job->name : "" }}</td>
              </tr>
              <tr>
                <th>LINE ID</th>
                <td>{{ $cast->line_id }}</td>
              </tr>
              <tr>
                <th>申請日時</th>
                <td>{{ Carbon\Carbon::parse($cast->request_transfer_date)->format('Y/m/d H:i') }}</td>
              </tr>
            </table>
          </div>
        </div>
        <div class="panel-body">
          <div class="col-lg-6 change-type-transfer">
            <label for="{{ App\Enums\CastTransferStatus::APPROVED }}">
              <input type="radio" name="transfer_request_status" id="{{ App\Enums\CastTransferStatus::APPROVED }}" value="{{ App\Enums\CastTransferStatus::APPROVED }}" {{ $cast->cast_transfer_status == App\Enums\CastTransferStatus::APPROVED ? 'checked':''}}><br>
              <span>通過</span>
            </label>
            <label for="{{ App\Enums\CastTransferStatus::DENIED }}">
              <input type="radio" name="transfer_request_status" id="{{ App\Enums\CastTransferStatus::DENIED }}" value="{{ App\Enums\CastTransferStatus::DENIED }}" {{ $cast->cast_transfer_status == App\Enums\CastTransferStatus::DENIED ? 'checked':''}}><br>
              <span>見送り</span>
            </label>
            <button type="button" id="btn-change-status" class="btn-detail">更新する</button>
          </div>
        </div>
        <div class="modal fade" id="passModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>このユーザーのキャスト申請を「通過」で更新しますか？</p>
              </div>
              <div class="modal-footer">
                <form action="{{ route('admin.request_transfer.update', ['cast' => $cast->id]) }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                  <input type="hidden" name="transfer_request_status" value="{{ App\Enums\CastTransferStatus::APPROVED }}">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept">はい</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="deniedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>このユーザーのキャスト申請を「見送り」で更新しますか？</p>
              </div>
              <div class="modal-footer">
                <form action="{{ route('admin.request_transfer.update', ['cast' => $cast->id]) }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                  <input type="hidden" name="transfer_request_status" value="{{ App\Enums\CastTransferStatus::DENIED }}">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept">はい</button>
                </form>
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
@endsection
@section('admin.js')
  <script type="text/javascript">
    $(document).ready(function() {
      $("#btn-change-status").click(function(event) {
        var valueRadioChecked = $("input[name='transfer_request_status']:checked").val();

        if(valueRadioChecked == 3) {
          $('#passModal').modal();
        }

        if(valueRadioChecked == 2) {
          $('#deniedModal').modal();
        }
      });
    });
  </script>
@stop

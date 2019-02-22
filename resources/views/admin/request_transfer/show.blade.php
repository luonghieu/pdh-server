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
          <div class="col-lg-10 wrap-qr-code">
            <div class="list-avatar">
              @php
                $avatars = $cast->avatars->reverse();
                $avatars = $avatars->slice(0, 2);
              @endphp
              @foreach ($avatars as $avatar)
                <img src="{{ @getimagesize($avatar->path) ? $avatar->path :'/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="avatar">
              @endforeach
            </div>
            <button type="button" data-toggle="modal" data-target="#btn-qr-code" class="btn btn-info pull-right">QRコードを表示する</button>
          </div>
          <div class="clearfix"></div>
          <div class="info-table col-lg-10">
            <table class="table table-bordered">
              <!--  table-striped -->
              <tr>
                <th>ユーザーID</th>
                <td>{{ $cast->id }}</td>
              </tr>
              <tr>
                <th>お名前</th>
                <td>{{ $cast->fullname }}</td>
              </tr>
              <tr>
                <th>お名前(ふりがな)</th>
                <td>{{ $cast->fullname_kana }}</td>
              </tr>
              <tr>
                <th>稼働希望エリア</th>
                <td>{{ $cast->prefecture ? $cast->prefecture->name:'' }}</td>
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
          <div class="col-lg-10 change-type-transfer">
            <label for="approved">
              <input type="radio" name="transfer_request_status" id="approved" value="approved"
                {{ $cast->cast_transfer_status == App\Enums\CastTransferStatus::APPROVED ? 'checked' : '' }}><br>
              <span>通過</span>
            </label>
            <label for="denied-female">
              <input type="radio" name="transfer_request_status" id="denied-female" value="denied-female"
                {{ $cast->cast_transfer_status == App\Enums\CastTransferStatus::DENIED && $cast->gender == App\Enums\UserGender::FEMALE ? 'checked' : '' }}><br>
              <span>見送り(女性)</span>
            </label>
            <label for="denied-male">
              <input type="radio" name="transfer_request_status" id="denied-male" value="denied-male" 
                {{ $cast->cast_transfer_status == App\Enums\CastTransferStatus::DENIED && $cast->gender == App\Enums\UserGender::MALE ? 'checked' : '' }} /><br>
              <span>見送り(男性)</span>
            </label>
            <button type="button" id="btn-change-status" class="btn btn-info">更新する</button>
          </div>
        </div>
        <div class="modal fade" id="approved-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>このユーザーのキャスト申請を「通過」で更新しますか？</p>
              </div>
              <div class="modal-footer">
                <form action="{{ route('admin.request_transfer.update', ['cast' => $cast->id]) }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                  <input type="hidden" name="transfer_request_status" value="approved">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept">はい</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="denied-female-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>このユーザーのキャスト申請を「見送り」で更新しますか？</p>
              </div>
              <div class="modal-footer">
                <form action="{{ route('admin.request_transfer.update', ['cast' => $cast->id]) }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                  <input type="hidden" name="transfer_request_status" value="denied-female">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept">はい</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="denied-male-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>このユーザーのキャスト申請を「見送り」で更新しますか？</p>
              </div>
              <div class="modal-footer">
                <form action="{{ route('admin.request_transfer.update', ['cast' => $cast->id]) }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                  <input type="hidden" name="transfer_request_status" value="denied-male">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept">はい</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="btn-qr-code" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                @if ($cast->line_qr)
                <img src="{{ $cast->line_qr}}" alt="">
                @else
                <p>QRコードが登録されていません</p>
                @endif
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
        var valRadioChecked = $("input[name='transfer_request_status']:checked").val();

        if (valRadioChecked == 'approved') {
          $('#approved-modal').modal();
        }

        if (valRadioChecked == 'denied-female') {
          $('#denied-female-modal').modal();
        }

        if (valRadioChecked == 'denied-male') {
          $('#denied-male-modal').modal();
        }
      });
    });
  </script>
@stop

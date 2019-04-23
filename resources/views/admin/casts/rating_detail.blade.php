@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <div class="info-table col-lg-8">
            <form action="{{ route('admin.casts.guest_rating_update', ['user' => $rating->rated_id, 'rating' => $rating->id]) }}" method="post">
              {{ csrf_field() }}
              <table class="table table-bordered">
                <!--  table-striped -->
                <tr>
                  <th>キャストID</th>
                  <td>{{ $rating->rated_id }}</td>
                </tr>
                <tr>
                  <th>キャスト名</th>
                  <td>{{ $rating->rated->nickname }}</td>
                </tr>
                <tr>
                  <th>予約ID</th>
                  <td>{{ $rating->order_id }}</td>
                </tr>
                <tr>
                  <th>ゲストID</th>
                  <td>{{ $rating->user_id }}</td>
                </tr>
                <tr>
                  <th>ゲスト名</th>
                  <td>{{ $rating->user->nickname }}</td>
                </tr>
                <tr>
                  <th>日時</th>
                  <td>{{ Carbon\Carbon::parse($rating->created_at)->format('Y/m/d H:i') }}</td>
                </tr>
                <tr>
                  <th>満足度</th>
                  <td>{{ str_repeat('★', $rating->satisfaction) }}</td>
                </tr>
                <tr>
                  <th>ルックス・身だしなみ</th>
                  <td>{{ str_repeat('★', $rating->appearance) }}</td>
                </tr>
                <tr>
                  <th>愛想・気遣い</th>
                  <td>{{ str_repeat('★', $rating->friendliness) }}</td>
                </tr>
                <tr>
                  <th>コメント</th>
                  <td>{{ $rating->comment }}</td>
                </tr>
                <tr>
                  <th>ステータス</th>
                  <td>{{ App\Enums\Status::getDescription($rating->is_valid) }}</td>
                </tr>
                <tr>
                  <th>運営者メモ</th>
                  <td><textarea id="js-memo">{!! $rating->memo !!}</textarea></td>
                </tr>
              </table>
              @php
                $dataTarget = '#memo_null';
                if ($rating->memo) {
                  switch ($rating->is_valid) {
                    case true:
                      $dataTarget = '#invalid';
                      break;
                    case false:
                      $dataTarget = '#valid';
                      break;

                    default:break;
                  }
                }
              @endphp
              <a href="javascript:void(0)" class="btn btn-info pull-right" id="js-submit" data-toggle="modal" data-target="{{ $dataTarget }}">{{ App\Enums\Status::getDescription((int) !$rating->is_valid) }}にする</a>
              <input type="hidden" id="is-valid" value="{{ (int) $rating->is_valid }}">
            </form>
          </div>
          <div class="modal fade" id="memo_null" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-body">
                  <p>運営者メモは必ず入力してください</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-info" data-dismiss="modal">OK</button>
                </div>
              </div>
            </div>
          </div>

          <div class="modal fade" id="valid" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-body">
                  <p>この評価を"有効"にしますか？</p>
                </div>
                <form action="{{ route('admin.casts.guest_rating_update', ['user' => $rating->rated_id, 'rating' => $rating->id]) }}" method="POST">
                {{ csrf_field() }}
                  <input type="hidden" name="is_valid" value="1" />
                  <input type="hidden" name="memo" id="memo_valid" value="{{ $rating->memo }}" />
                  <div class="modal-footer">
                    <button type="button" class="btn btn-canceled" data-dismiss="modal">いいえ</button>
                    <button type="submit" class="btn btn-accept">はい</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="modal fade" id="invalid" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-body">
                  <p>この評価を"無効"にしますか？</p>
                </div>
                <form action="{{ route('admin.casts.guest_rating_update', ['user' => $rating->rated_id, 'rating' => $rating->id]) }}" method="POST">
                {{ csrf_field() }}
                  <input type="hidden" name="is_valid" value="0" />
                  <input type="hidden" name="memo" id="memo_invalid" value="{{ $rating->memo }}" />
                  <div class="modal-footer">
                    <button type="button" class="btn btn-canceled" data-dismiss="modal">いいえ</button>
                    <button type="submit" class="btn btn-accept">はい</button>
                  </div>
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
<script>
  $('document').ready(function() {
    var isValid = $('#is-valid').val();

    $('body').on('change', '#js-memo', function() {
      var memo = $(this).val();

      if (memo.length == 0) {
        $('#js-submit').attr('data-target', '#memo_null');
      } else {
        if (isValid) {
          $('#memo_invalid').val(memo);
          $('#js-submit').attr('data-target', '#invalid');
        } else {
          $('#memo_valid').val(memo);
          $('#js-submit').attr('data-target', '#valid');
        }
      }
    });
  });
</script>
@endsection

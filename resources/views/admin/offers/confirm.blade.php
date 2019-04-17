@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <label style="margin-left: 30px">選択しているキャスト</label>
              <span class="total-cast-offer" >現在選択しているキャスト: 0名</span>
          </div>
        </div>
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <div class="col-lg-12 wrap-qr-code">
            @if(count($casts))
              @foreach($casts as $cast)
              <div class="list-avatar icon-cast">
              <a href="{{ route('admin.users.show', ['id' => $cast->id ]) }}" class="cast-link cast-detail">
                @if (@getimagesize($cast['avatars'][0]['thumbnail']))
                  <img src="{{ $cast->avatars[0]['thumbnail'] }}" alt="" class="adaf">
                  @else
                  <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
                @endif
              </a>
              </div>
              @endforeach
            @endif
            <div class="pagination-outter">
              <ul class="pagination">
              </ul>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <label for="comment-offer" class="lb-comment-offer">訴求コメント</label>
            <div class="col-sm-12 ">
              <p class="confirm-message-offer">{!! nl2br($data['comment_offer']) !!}</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <label class="lb-date-offer">開始日時</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-2 ">
                {{ Carbon\Carbon::parse($data['date_offer'])->format('Y年m月d日') }}
               <p class="confirm-date-offer"></p>
            </div>
            <div class="col-sm-4">
              {{ $data['start_time'] }} &nbsp ~ &nbsp {{ $data['end_time'] }}
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code lb-date-offer">
            <label class="">ギャラ飲みの時間</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-4 ">
                <p class="confirm-duration-offer">{{ $data['duration_offer'] }}時間</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code lb-date-offer">
            <label class="">エリア</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-4 ">
              <p class="confirm-area-offer">{{ $prefecture }}</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code lb-date-offer">
            <label class="">オファーの応募締切期限</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-4 ">
              <p class="">{{ Carbon\Carbon::parse($data['expired_date'])->format('Y年m月d日 H:i') }}</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <label class="lb-date-offer"></label>
            <hr style="border: 1px #0000006e dashed;">
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <label class="lb-date-offer"></label>
             <div class="col-sm-4 col-sm-offset-8" style="text-align: center;">
              <span class="show-current-point-offer">予定合計ポイント : 0P</span>
              <hr style="border: 1px #0000006e dashed;width: 65%;">
             </div>
          </div>
          <div class="clear_fix"></div>
          <div class="col-lg-12">
            <div class="button-confirm-offer" style="text-align: right;" >
                @if(isset($data['offer_id']))
                <button class="btn btn-accept"><a href="{{ route('admin.offers.edit', $data['offer_id'] ) }}" style="color: white">戻る</a></button>
                @else
                <button class="btn btn-accept"><a href="{{ route('admin.offers.create' ) }}" style="color: white">戻る</a></button>
                @endif
                <button data-toggle="modal" data-target="#list-guests" class="btn btn-accept show-list-guests" style="color: white" >
                  Androidユーザーへ送信する
                </button>
                <button data-toggle="modal" data-target="#line_url" class="btn btn-accept" style="color: white">LINEへ送信する</button>
                <button data-toggle="modal" data-target="#save_url" class="btn btn-accept" style="color: white">URL発行する</button>
                <button data-toggle="modal" data-target="#save_temporarily" class="btn btn-accept" style="color: white">仮保存する</button>
            </div>
          </div>
        </div>
      </div>
    <!--/col-->
    </div>
  <!--/row-->
  </div>
  <div class="modal fade" id="save_url" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <p>URLを発行しますか？</p>
        </div>
        <form action="{{ route('admin.offers.store') }}" method="POST">
          {{ csrf_field() }}
          <div class="modal-footer" style="text-align: center;">
            <button type="button" class="btn btn-canceled" data-dismiss="modal">いいえ</button>
            <button type="submit" class="btn btn-accept del-order">はい</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="line_url" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <p>LINEへ送信しますか？</p>
        </div>
        <form action="{{ route('admin.offers.store') }}" method="POST">
          {{ csrf_field() }}
          <input type="hidden" value="1" name="line_offer">
          <div class="modal-footer" style="text-align: center;">
            <button type="button" class="btn btn-canceled" data-dismiss="modal">いいえ</button>
            <button type="submit" class="btn btn-accept del-order">はい</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="save_temporarily" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <p>仮保存しますか？</p>
        </div>
        <form action="{{ route('admin.offers.store') }}" method="POST">
          {{ csrf_field() }}
          <input type="hidden" value="1" name="save_temporarily">
          <div class="modal-footer" style="text-align: center;">
            <button type="button" class="btn btn-canceled" data-dismiss="modal">いいえ</button>
            <button type="submit" class="btn btn-accept del-order">はい</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="list-guests" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form action="{{ route('admin.offers.store') }}" method="POST" id="form-send-line">
        {{ csrf_field() }}
        <input type="hidden" value="" name="choose_guest" class="choose-guests">
        <input type="hidden" value="{{ App\Enums\DeviceType::ANDROID }}" name="device_type">
      </form>
      <div class="modal-content">
        <div class="modal-body">
          <p>ゲストを選択して下さい</p>
          <div class="panel-body handling">
            <div class="search">
                <input type="text" class="form-control input-search-guest"
                       placeholder="ユーザーID,名前" value="">
            </div>
          </div>
          <div class="wrapper-table">
            <table class="table table-striped table-bordered bootstrap-datatable table-sm"
                 id="candidation-table">
              <thead>
              <tr>
                  <th class="column-checkbox"></th>
                  <th>ユーザーID</th>
                  <th>ゲスト名</th>
              </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル
            </button>
            <button data-toggle="modal" data-target="#err-choose-guests" class="btn btn-accept btn-choose-guests" id="btn-choose-guests" type="button">
              このゲストに送信する
            </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="choose-guests" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <p>このゲストに送信しますか？</p>
        </div>
        <div class="modal-footer" style="text-align: center;">
          <button type="button" class="btn btn-canceled" data-dismiss="modal">いいえ</button>
          <button type="button" class="btn btn-accept " id="send-line-to-guest">はい</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="err-choose-guests" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <p>ゲストが選択されていません。</p>
        </div>
        <div class="modal-footer" style="text-align: center;">
          <button type="button" class="btn btn-canceled" data-dismiss="modal" style="background-color: #00B0E7;">はい</button>
        </div>
      </div>
    </div>
  </div>

@endsection
@section('admin.js')
  <script src="/assets/admin/js/offer/offer.js"></script>
@stop

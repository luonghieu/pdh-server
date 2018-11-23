@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <label style="margin-left: 30px">選択しているキャスト</label>
              <span class="total-cast-offer" style="font-size: 12px">現在選択しているキャスト: 0名</span>
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
            <label for="comment-offer" class="lb-comment-offer">訴求コメントを入力する</label>
            <div class="col-sm-12 ">
              <p class="confirm-message-offer" style="font-size: 12px">{{ $data['comment_offer'] }}</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <label class="lb-date-offer">訴求コメントを入力する</label>
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
            <label class="">ギャラ飲みの時間を選択する</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-4 ">
                <p class="confirm-duration-offer">{{ $data['duration_offer'] }}時間</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code lb-date-offer">
            <label class="">エリアを選択する</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-4 ">
              <p class="confirm-area-offer">東京</p>
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
            <div class="col-sm-4 col-sm-offset-8" style="text-align: center;">
                @if(isset($data['offer_id']))
                <button class="btn btn-accept"><a href="{{ route('admin.offers.edit', $data['offer_id'] ) }}" style="color: white">戻る</a></button>
                @else
                <button class="btn btn-accept"><a href="{{ route('admin.offers.create' ) }}" style="color: white">戻る</a></button>
                @endif
                <button data-toggle="modal" data-target="#save_url" class="btn btn-accept" style="color: white">URL発行する</button>
                <button data-toggle="modal" data-target="#save_temporarily" class="btn btn-accept" style="color: white">確認画面へ</button>
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

  <div class="modal fade" id="save_temporarily" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <p>チェックした予約を無効しますか？</p>
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

@endsection
@section('admin.js')
  <script src="/assets/admin/js/offer/offer.js"></script>
@stop

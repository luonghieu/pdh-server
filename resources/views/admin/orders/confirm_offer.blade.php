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
              <p class="confirm-area-offer">{{ $data['area_offer'] }}</p>
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
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-2 col-sm-offset-9">
            <label class="lb-date-offer"></label>
              <form action="">
                <button type="submit" class="btn btn-accept">確認画面へ</button>
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
  <script src="/assets/admin/js/offer/offer.js"></script>
@stop

@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <label style="margin-left: 30px">選択しているキャスト</label>
              <span class="total-cast-offer">現在選択しているキャスト: {{ count($offer->cast_ids) }} 名</span>
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
              <p class="confirm-message-offer" >{!! nl2br($offer->comment) !!}</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <label class="lb-date-offer">開始日時</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-2 ">
                {{ Carbon\Carbon::parse($offer->date)->format('Y年m月d日') }}
               <p class="confirm-date-offer"></p>
            </div>
            <div class="col-sm-4">
              {{ Carbon\Carbon::parse($offer->start_time_from)->format('H:i') }} &nbsp ~ &nbsp
              @php
                $startHour = (int)Carbon\Carbon::parse($offer->start_time_from)->format('H');
                $endHour = (int)Carbon\Carbon::parse($offer->start_time_to)->format('H');
                $endMinute = (int)Carbon\Carbon::parse($offer->start_time_to)->format('i');

                if ($endHour < $startHour) {
                  switch ($endHour) {
                  case 0:
                  $endHour = 24;
                  break;
                  case 1:
                  $endHour = 25;
                  break;
                  case 2:
                  $endHour = 26;
                  break;
                  }
                }
              @endphp
                {{ $endHour . ':' . $endMinute }}
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code lb-date-offer">
            <label class="">ギャラ飲みの時間</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-4 ">
                <p class="confirm-duration-offer">{{ $offer->duration }}時間</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code lb-date-offer">
            <label class="">エリア</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-4 ">
              <p class="confirm-area-offer">東京</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code lb-date-offer">
            <label class="">オファーの応募締切期限</label>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <div class="col-sm-4 ">
              <p class="">{{ Carbon\Carbon::parse($offer->expired_date)->format('Y年m月d日 H:i') }}</p>
            </div>
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <label class="lb-date-offer"></label>
            <hr style="border: 1px #0000006e dashed;">
          </div>
          <div class="col-lg-12 wrap-qr-code">
            <label class="lb-date-offer"></label>
             <div class="col-sm-4 col-sm-offset-8" style="text-align: center;">
              <span class="show-current-point-offer">予定合計ポイント : {{ number_format($offer->temp_point) }}P~</span>
              <hr style="border: 1px #0000006e dashed;width: 65%;">
             </div>
          </div>
          <div class="clear_fix"></div>
          <div class="col-lg-12">
            <div class="col-sm-4 col-sm-offset-8" style="text-align: center;">
                <button class="btn btn-accept"><a href="{{ route('admin.offers.index') }}" style="color: white">戻る</a></button>
                @if(App\Enums\OfferStatus::TIMEOUT != $offer->status)
                    <button class="btn btn-accept"><a href="{{ route('admin.offers.edit', $offer->id ) }}" style="color: white">編集する</a></button>
                @endif
                <button data-toggle="modal" data-target="#delete-offer" class="btn btn-accept" style="color: white">削除する</button>
            </div>
          </div>
        </div>
      </div>
    <!--/col-->
    </div>
  <!--/row-->
  </div>
  <div class="modal fade" id="delete-offer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <p>削除しますか？</p>
        </div>
        <form action="{{ route('admin.offers.delete', $offer->id) }}" method="POST">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
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

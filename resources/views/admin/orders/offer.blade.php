@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.offer.index')}}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{request()->search}}">
              <button type="submit" class="fa fa-search btn-search"></button>
              <span class="total-cast-offer">現在選択しているキャスト: 0名</span>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <form action="{{route('admin.offer.offer_confirm')}}" method="POST">
            {{ csrf_field() }}
            <div class="col-lg-12 wrap-qr-code">
              @if(count($casts))
                @foreach($casts as $cast)
                <div class="list-avatar icon-cast">
                <a href="{{ route('admin.users.show', ['id' => $cast['id'] ]) }}" class="cast-link cast-detail">
                  @if (@getimagesize($cast['avatars'][0]['thumbnail']))
                    <img src="{{ $cast['avatars'][0]['thumbnail'] }}" alt="" class="adaf">
                    @else
                    <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
                  @endif
                </a>
                <div class="custom-checkbox" id="list-casts-offer">
                  <input type="checkbox" name="casts_offer[]" value="{{ $cast['id'] }}" id="{{ $cast['id'] }}" class="cb-casts-offer">
                </div>
                </div>
                @endforeach
              @endif
              <div class="pagination-outter">
                <ul class="pagination">
                  {{ $casts->appends(request()->all())->links() }}
                </ul>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <label for="comment-offer" class="lb-comment-offer">訴求コメントを入力する</label>
              <div class="col-sm-12 ">
                <textarea class="form-control" rows="5" id="comment-offer" name='comment_offer' placeholder="キャストからのコメントを入力する"></textarea>
              </div>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <label class="lb-date-offer">訴求コメントを入力する</label>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <div class="col-sm-2 ">
                 @php
                    $arrMonth = [11,12,1,2];
                  @endphp
                <select id="" name="date_offer" class="form-control select-time date-offer">
                  @foreach ($arrMonth as $month)
                    @php
                      $data['month'] = $month;
                      if (1 == $month || 2 == $month || 3 == $month ) {
                        $data['year'] = 2019;
                      } else {
                         $data['year'] = 2018;
                      }

                      $data['month'] = $month;
                    @endphp
                    @foreach(listDate($data) as $date)
                    <option value="{{ $data['year']. '-' .$month . '-' . $date }}" data-name ="{{ $data['year'] }}年{{ $month }}月{{ $date }}日" >{{ $data['year'] }}年{{ $month }}月{{ $date }}日</option>
                    @endforeach
                  @endforeach
                </select>
              </div>
              @php
                $start = "00:00";
                  $end = "23:59";

                  $tStart = strtotime($start);
                  $tEnd = strtotime($end);
                  $tNow = $tStart;

                  $arrTime = [];
                  while($tNow <= $tEnd){
                    array_push($arrTime, date("H:i",$tNow));
                    $tNow = strtotime('+1 minutes',$tNow);
                  }
              @endphp
              <div class="col-sm-4 col-sm-offset-1">
                <select id="start_time_offer" name="start_time_offer" class="form-control select-time select-time-offer">
                  @foreach ($arrTime as $time)
                    <option value="{{ $time }}">{{ $time }}</option>
                  @endforeach
                </select>
                &nbsp;&nbsp;&nbsp; ~ &nbsp;&nbsp;&nbsp;
                <select id="end_time_offer" name="end_time_offer" class="form-control select-time select-time-offer">
                  @foreach ($arrTime as $time)
                    <option value="{{ $time }}">{{ $time }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-lg-12 wrap-qr-code lb-date-offer">
              <label class="">ギャラ飲みの時間を選択する</label>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <div class="col-sm-4 ">
                <select id="duration_offer" name="duration_offer" class="form-control select-time date-offer">
                  @foreach (range(1,10) as $duration)
                    <option value="{{ $duration }}">{{ $duration }}時間</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-lg-12 wrap-qr-code lb-date-offer">
              <label class="">エリアを選択する</label>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <div class="col-sm-4 ">
                @php
                  $arrArea =['東京'];
                @endphp
                <select id="area_offer" name="area_offer" class="form-control select-time date-offer">
                  @foreach ($arrArea as $area)
                    <option value="{{ $area }}">{{ $area }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <label class="lb-date-offer"></label>
              <hr style="border: 1px #0000006e dashed;">
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <label class="lb-date-offer"></label>
               <div class="col-sm-4 col-sm-offset-8" style="text-align: center;">
                <input type="hidden" name="current_point_offer" id="current-point-offer" value="">
                <span class="show-current-point-offer">予定合計ポイント : 0P</span>
                <hr style="border: 1px #0000006e dashed;width: 65%;">
               </div>
            </div>
            <div class="clear_fix"></div>
            <div class="col-lg-12 wrap-qr-code">
              <div class="col-sm-2 col-sm-offset-9">
              <label class="lb-date-offer"></label>
                  <button type="submit" class="btn btn-accept">確認画面へ</button>
              </div>
            </div>
          </form>
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

@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.offers.edit', $offer->id )}}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{request()->search}}">
              <select class="form-control search-point-type class_id-offer" name="cast_class" style="margin-right: 15px;" id="class-id-offer">
                @foreach ($castClasses as $castClass)
                  <option value="{{ $castClass->id }}" {{ request()->cast_class == $castClass->id ? 'selected' : '' }}>{{ $castClass->name }}</option>
                @endforeach
              </select>
              <button type="submit" class="fa fa-search btn-search" id="sbm-offer"></button>
              <span class="total-cast-offer">現在選択しているキャスト: {{ count($offer->cast_ids) }} 名</span>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <form action="{{route('admin.offers.confirm')}}" method="POST">
            {{ csrf_field() }}
            <div class="col-lg-12 wrap-qr-code">
              @if(count($casts))
                @php
                  $castIds = implode(',', $offer->cast_ids);
                @endphp
                <input type="hidden" value="{{ $offer->prefecture_id }}" name="prefecture_id" class="prefecture_id-edit">
                <input type="hidden" value="{{ $castIds }}" name="list_cast_ids" class="cast-ids-edit">
                <input type="hidden" value="{{ $offer->temp_point }}" class="temp_point-edit">
                <input type="hidden" value="{{ $offer->class_id }}" class="class_id-edit">
                <input type="hidden" value="{{ $offer->id }}" name="offer_id">
                <input type="hidden" value="{{ $offer->date }}" class="date-offer-edit">
                <input type="hidden" value="{{ Carbon\Carbon::parse($offer->start_time_from)->format('H:i') }}" class="start_time_from-edit">
                <input type="hidden" value="{{ Carbon\Carbon::parse($offer->expired_date)->format('Y-m-d') }}" class="expired-date-edit">
                <input type="hidden" value="{{ Carbon\Carbon::parse($offer->expired_date)->format('H:i') }}" class="expired-time-edit">
                @php
                  $startTimeFrom = explode(":", $offer->start_time_from);
                  $startTimeTo = explode(":", $offer->start_time_to);
                  if ($startTimeFrom[0] > $startTimeTo[0]) {
                      switch ($startTimeTo[0]) {
                          case 0:
                              $hour = 24;
                              break;
                          case 1:
                              $hour = 25;
                              break;
                          case 2:
                              $hour = 26;
                              break;
                      }

                      $time = $hour . ':' . $startTimeTo[1];
                  } else {
                      $time = Carbon\Carbon::parse($offer->start_time_to)->format('H:i');
                  }
                @endphp
                <input type="hidden" value="{{ $time }}" class="start_time_to-edit">
                <input type="hidden" value="{{ $offer->duration }}" class="duration-edit">
                <input type="hidden" value="{{ $offer->comment }}" class="comment-edit">

                @foreach($casts as $cast)
                <div class="list-avatar icon-cast">
                <a href="{{ route('admin.users.show', ['id' => $cast['id'] ]) }}" class="cast-link cast-detail" target = "_blank">
                  @if (@getimagesize($cast['avatars'][0]['thumbnail']))
                    <img src="{{ $cast['avatars'][0]['thumbnail'] }}" alt="">
                    @else
                    <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
                  @endif
                </a>
                <p>ユーザーID: {{ $cast['id'] }}</p>
                <p class="nickname-offer">{{ $cast['nickname'] ? $cast['nickname'] : '...' }}</p>
                <div class="custom-checkbox">
                  <input type="checkbox" name="casts_offer[]" data-id='{{ $cast['class_id'] }}' value="{{ $cast['id'] }}" id="{{ $cast['id'] }}" class="cb-casts-offer">
                </div>
                </div>
                @endforeach
              @else
                {{ trans('messages.cast_not_found') }}
              @endif
              <input type="hidden" value="" name="class_id_offer" class="class-id-offer">
              <input type="hidden" value="" name="cast_ids_offer" class="cast-ids-offer">
              <div class="pagination-outter">
                <ul class="pagination">
                  {{ $casts->appends(request()->all())->links() }}
                </ul>
              </div>
              <div class="clearfix"></div>
            </div>
            @if(Session::has('cast_not_found'))
              <div class="form-group error-end-coupon" >
                <div class="alert alert-danger fade in col-sm-4">
                  <button data-dismiss="alert" class="close close-sm" type="button">
                    <i class="icon-remove"></i>
                  </button>
                  <strong>
                    {{trans('messages.cast_not_found') }}
                  </strong>
                </div>
              </div>
            @endif
            <div class="col-lg-12 wrap-qr-code">
              <label for="comment-offer" class="lb-comment-offer">訴求コメントを入力する</label>
              <div class="col-sm-12 ">
                <textarea class="form-control" rows="5" id="comment-offer" name='comment_offer' placeholder="キャストからのコメントを入力する"></textarea>
              </div>
            </div>
            @if(Session::has('message_exits'))
            <div class="col-lg-12 wrap-qr-code">
              <div class="form-group error-end-coupon" >
                <div class="alert alert-danger fade in col-sm-4">
                  <button data-dismiss="alert" class="close close-sm" type="button">
                      <i class="icon-remove"></i>
                    </button>
                    <strong>
                      {{trans('messages.message_exits') }}
                    </strong>
                  </div>
                </div>
              </div>
            @endif
            @if(Session::has('message_invalid'))
            <div class="col-lg-12 wrap-qr-code">
              <div class="form-group error-end-coupon" >
                <div class="alert alert-danger fade in col-sm-4">
                  <button data-dismiss="alert" class="close close-sm" type="button">
                      <i class="icon-remove"></i>
                    </button>
                    <strong>
                      訴求コメントは、80文字以内で入力してください
                    </strong>
                  </div>
                </div>
              </div>
            @endif
            <div class="col-lg-12 wrap-qr-code">
              <label class="lb-date-offer">訴求コメントを入力する</label>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <div class="col-sm-2 ">
                  @php
                  $arrMonth = [];
                  $currentYear = (int)Carbon\Carbon::now()->format('Y');
                  $currentMonth = (int)Carbon\Carbon::now()->format('m');

                  for ($i = $currentMonth; $i <=12; $i++) {
                    array_push($arrMonth, $i);
                  }
                  @endphp
                <select name="date_offer" class="form-control select-time date-offer" id="select-date-offer">
                  @foreach ($arrMonth as $month)
                    @php
                      $data['month'] = $month;
                      $data['year'] = (int)Carbon\Carbon::now()->format('Y');
                    @endphp
                    @foreach(listDate($data) as $date)
                    <option value="{{ $data['year']. '-' .(($month < 10) ? '0'.$month : $month) . '-' . (($date < 10) ? '0'.$date : $date) }}" >{{ $data['year'] }}年{{ (($month < 10) ? '0'.$month : $month) }}月{{ (($date < 10) ? '0'.$date : $date) }}日</option>
                    @endforeach
                  @endforeach

                  @foreach([1,2] as $month)
                    @php
                      $data['month'] = $month;
                      $data['year'] = (int)Carbon\Carbon::now()->format('Y') +1;
                    @endphp
                    @foreach(listDate($data) as $date)
                    @php
                    @endphp
                    <option value="{{ $data['year']. '-' .(($month < 10) ? '0'.$month : $month) . '-' . (($date < 10) ? '0'.$date : $date) }}" >{{ $data['year'] }}年{{ (($month < 10) ? '0'.$month : $month) }}月{{ (($date < 10) ? '0'.$date : $date) }}日</option>
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
                    $tNow = strtotime('+30 minutes',$tNow);
                  }
              @endphp
              <div class="col-sm-6 col-sm-offset-1">
                <select id="start_time_offer" name="start_time_offer" class="form-control select-time select-time-offer">
                  @foreach ($arrTime as $time)
                    <option value="{{ $time }}">{{ $time }}</option>
                  @endforeach
                </select>
                &nbsp;&nbsp;&nbsp; ~ &nbsp;&nbsp;&nbsp;
                <select id="end_time_offer" name="end_time_offer" class="form-control select-time select-time-offer">
                  @foreach ($arrTime as $time)
                    @if($time != '00:00' && $time != '00:30')
                    <option value="{{ $time }}">{{ $time }}</option>
                    @endif
                  @endforeach
                  @foreach (['24:00', '24:30','25:00', '25:30', '26:00'] as $time)
                    <option value="{{ $time }}">{{ $time }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @if(Session::has('date_not_valid'))
            <div class="col-lg-12 wrap-qr-code">
              <div class="form-group error-end-coupon" >
                <div class="alert alert-danger fade in col-sm-4">
                  <button data-dismiss="alert" class="close close-sm" type="button">
                      <i class="icon-remove"></i>
                    </button>
                    <strong>
                      {{trans('messages.date_not_valid') }}
                    </strong>
                  </div>
                </div>
              </div>
            @endif
            <div class="col-lg-12 wrap-qr-code lb-date-offer">
              <label class="">ギャラ飲みの時間を選択する</label>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <div class="col-sm-4 ">
                <select id="duration_offer" name="duration_offer" class="form-control select-time date-offer">
                  @foreach (range(1,10) as $duration)
                    <option value="{{ $duration }}" >{{ $duration }}時間</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-lg-12 wrap-qr-code lb-date-offer">
              <label class="">エリアを選択する</label>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <div class="col-sm-4 ">
                <select id="area_offer" name="area_offer" class="form-control select-time date-offer">
                  @foreach ($prefectures as $prefecture)
                    <option value="{{ $prefecture['id'] }}">{{ $prefecture['name'] }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <label class="lb-date-offer">オファーの応募締切期限</label>
            </div>
            <div class="col-lg-12 wrap-qr-code">
              <div class="col-sm-2 wrap-input-date-offer">
                @php
                  $start_date = Carbon\Carbon::now();
                  $end_date = $start_date->copy()->addWeek();
                @endphp

                <select name="expired_date_offer" class="form-control select-time date-offer" id="expired_date_offer">
                    @php
                      while(!$start_date->gt($end_date))
                      {
                    @endphp
                    <option value="{{ $start_date->format('Y-m-d') }}" >
                      {{ $start_date->format('Y年m月d日') }}
                    </option>
                    @php
                        $start_date->addDay();
                      }
                    @endphp
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
              <div class="col-sm-6 col-sm-offset-1">
                <select id="expired_time_offer" name="expired_time_offer" class="form-control select-time select-time-offer">
                  @foreach ($arrTime as $time)
                    <option value="{{ $time }}">{{ $time }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @if(Session::has('expired_date_not_valid'))
            <div class="col-lg-12 wrap-qr-code">
              <div class="form-group error-end-coupon" >
                <div class="alert alert-danger fade in col-sm-4">
                  <button data-dismiss="alert" class="close close-sm" type="button">
                    <i class="icon-remove"></i>
                  </button>
                  <strong>
                    {{ Session::get('expired_date_not_valid') }}
                  </strong>
                </div>
              </div>
            </div>
            @endif
            @if(Session::has('time_out'))
            <div class="col-lg-12 wrap-qr-code">
              <div class="form-group error-end-coupon" >
                <div class="alert alert-danger fade in col-sm-4">
                  <button data-dismiss="alert" class="close close-sm" type="button">
                    <i class="icon-remove"></i>
                  </button>
                  <strong>
                    {{ Session::get('time_out') }}
                  </strong>
                </div>
              </div>
            </div>
            @endif
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
              <div class="col-sm-2 col-sm-offset-9" style="text-align: center;">
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

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
              <form action="{{ route('admin.coupons.update', ['coupon' => $coupon->id]) }}" method="POST">
                {{ csrf_field() }}
                <p>クーポン設定</p>
                <table class="table table-bordered table-coupon">
                  <!--  table-striped -->
                  <tr>
                    <th>クーポンID</th>
                    <td>{{$coupon->id}}</td>
                  </tr>
                  <tr>
                    <th>クーポン名*</th>
                    <td>
                      <div class="wrap-td-coupon">
                        <input type="text" name="name" value="{{$coupon->name}}" placeholder="クーポン名を入力してください">
                        @if ($errors->has('name'))
                          <div class="error pull-left">
                            <span>{{ $errors->first('name') }}</span>
                          </div>
                        @endif
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>適用対象*</th>
                    <td>
                      <div class="wrap-td-coupon">
                        <div class="wrap-radio-coupon">
                          <input type="radio" name="type" value="1" {{$coupon->type == App\Enums\CouponType::POINT ? 'checked': ''}}> ポイント数<br>
                          <div class="wrap-object-coupon coupon-point">
                            <input type="number" class="object-coupon {{$coupon->type != App\Enums\CouponType::POINT ? 'invalid-element-coupon-input': ''}}" name="point" placeholder="0" value="{{$coupon->point}}" {{$coupon->type == App\Enums\CouponType::POINT ? '': 'readOnly'}}>
                            @if ($errors->has('point'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('point') }}</span>
                              </div>
                            @endif
                            <span class="{{$coupon->type != App\Enums\CouponType::POINT ? 'invalid-element-coupon': ''}}">ポイント引き</span>
                          </div>
                        </div>
                        <div class="wrap-radio-coupon">
                          <input type="radio" name="type" value="2" {{$coupon->type == App\Enums\CouponType::TIME ? 'checked': ''}}> 時間<br>
                          <div class="wrap-object-coupon coupon-time">
                            <input type="number" class="object-coupon {{$coupon->type != App\Enums\CouponType::TIME ? 'invalid-element-coupon-input': ''}}" name="time" placeholder="0" value="{{$coupon->time}}" {{$coupon->type == App\Enums\CouponType::TIME ? '': 'readOnly'}} min="1" max="9999">
                            @if ($errors->has('time'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('time') }}</span>
                              </div>
                            @endif
                            <span class="{{$coupon->type != App\Enums\CouponType::TIME ? 'invalid-element-coupon': ''}}">分無料</span>
                          </div>
                        </div>
                        <div class="wrap-radio-coupon">
                          <input type="radio" name="type" value="3" {{$coupon->type == App\Enums\CouponType::PERCENT ? 'checked': ''}}>%<br>
                          <div class="wrap-object-coupon coupon-percent">
                            <input type="number" class="object-coupon {{$coupon->type != App\Enums\CouponType::PERCENT ? 'invalid-element-coupon-input': ''}}" name="percent" placeholder="0" value="{{$coupon->percent}}" {{$coupon->type == App\Enums\CouponType::PERCENT ? '': 'readOnly'}}>
                            @if ($errors->has('percent'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('percent') }}</span>
                              </div>
                            @endif
                            <span class="{{$coupon->type != App\Enums\CouponType::PERCENT ? 'invalid-element-coupon': ''}}">%Off</span>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>クーポン適用最高上限額 (ポイント)</th>
                    <td>
                      <div class="wrap-td-coupon">
                        <div class="wrap-radio-coupon">
                          <div class="wrap-object-coupon">
                            <input type="number" class="object-coupon" name="max_point" placeholder="0" value="{{$coupon->max_point}}" min="1" max="99999">
                            @if ($errors->has('max_point'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('max_point') }}</span>
                              </div>
                            @endif
                            <span>ポイント</span>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>備考</th>
                    <td>
                      <div class="wrap-td-coupon">
                        <textarea rows="4" cols="50" name="note">{{$coupon->note}}</textarea>
                        @if ($errors->has('note'))
                          <div class="error pull-left">
                            <span>{{ $errors->first('note') }}</span>
                          </div>
                        @endif
                      </div>
                    </td>
                  </tr>
                </table>
                <p>オプショナルフィルター</p>
                <table class="table table-bordered">
                  <!--  table-striped -->
                  <tr>
                    <th>対象ゲスト</th>
                    <td>
                      <div class="wrap-td-coupon">
                        <label class="switch switch-primary">
                          <input type="checkbox" class="switch-input" {{$coupon->is_filter_after_created_date? 'checked=""': ''}} id="checkbox-after-created-date-filter" name="is_filter_after_created_date" value="1">
                          <span class="switch-label" data-on="On" data-off="Off"></span>
                          <span class="switch-handle"></span>
                        </label>
                        <p class="title-filter_after_created_date {{$coupon->is_filter_after_created_date? '': 'invalid-element-coupon'}}">登録時から</p>
                        <div class="wrap-object-coupon after-created-date">
                          <input type="text" class="object-coupon {{$coupon->is_filter_after_created_date? '': 'invalid-element-coupon-input'}}" name="filter_after_created_date" placeholder="0" value="{{$coupon->filter_after_created_date ? $coupon->filter_after_created_date : '0'}}" {{$coupon->is_filter_after_created_date ? '' : 'readOnly'}}>
                          @if ($errors->has('filter_after_created_date'))
                            <div class="error pull-left">
                              <span>{{ $errors->first('filter_after_created_date') }}</span>
                            </div>
                          @endif
                          <span class="{{$coupon->is_filter_after_created_date? '': 'invalid-element-coupon'}}">日間以内</span>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>予約時間</th>
                    <td>
                      <div class="wrap-td-coupon">
                        <label class="switch switch-primary">
                          <input type="checkbox" class="switch-input" {{$coupon->is_filter_order_duration? 'checked=""': ''}} id="checkbox-time-order-filter" name="is_filter_order_duration" value="1">
                          <span class="switch-label" data-on="On" data-off="Off"></span>
                          <span class="switch-handle"></span>
                        </label>
                        <div class="wrap-object-coupon time-order-filter">
                          <select class="object-coupon" name="filter_order_duration" {{$coupon->is_filter_order_duration ? '' : 'disabled'}}>
                            @for($i = 0.5; $i <= 10; $i += 0.5)
                              <option value="{{ $i }}" {{$coupon->filter_order_duration == $i ? 'selected="selected"': ''}}>{{ $i  }}</option>
                            @endfor
                          </select>
                          <span class="{{$coupon->is_filter_order_duration? '': 'invalid-element-coupon'}}">時間以上</span>
                        </div>
                      </div>
                    </td>
                  </tr>
                </table>
                <div class="btn-create-coupon">
                  <button type="submit" class="btn btn-info">保存する</button>
                </div>
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
  <script src="/assets/admin/js/coupon/coupon.js"></script>
@stop
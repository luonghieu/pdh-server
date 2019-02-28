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
              <form action="{{ route('admin.coupons.store') }}" method="POST">
                {{ csrf_field() }}
                <p>クーポン設定</p>
                <table class="table table-bordered">
                  <!--  table-striped -->
                  <tr>
                    <th>クーポン名*</th>
                    <td>
                      <div class="wrap-td-coupon">
                        <input type="text" name="name" placeholder="クーポン名を入力してください">
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
                          <input type="radio" name="type" value="1" checked> ポイント数<br>
                          <div class="wrap-object-coupon coupon-point">
                            <input type="text" class="object-coupon" name="point" placeholder="0">
{{--                            @if ($errors->has('point'))--}}
                              <div class="error pull-left">
                                <span>{{ $errors->first('point') }}</span>
                              </div>
                            {{--@endif--}}
                            <span>ポイント引き</span>
                          </div>
                        </div>
                        <div class="wrap-radio-coupon">
                          <input type="radio" name="type" value="2"> 時間<br>
                          <div class="wrap-object-coupon coupon-time">
                            <input type="text" class="object-coupon" name="time" readonly placeholder="0">
                            @if ($errors->has('time'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('time') }}</span>
                              </div>
                            @endif
                            <span class="invalid-element-coupon">分無料</span>
                          </div>
                        </div>
                        <div class="wrap-radio-coupon">
                          <input type="radio" name="type" value="3">%<br>
                          <div class="wrap-object-coupon coupon-percent">
                            <input type="text" class="object-coupon" name="percent" readonly placeholder="0">
                            @if ($errors->has('percent'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('percent') }}</span>
                              </div>
                            @endif
                            <span class="invalid-element-coupon">%Off</span>
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
                            <input type="text" class="object-coupon" name="max_point" placeholder="0">
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
                        <textarea rows="4" cols="50" name="note"></textarea>
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
                          <input type="checkbox" class="switch-input" checked="" id="checkbox-after-created-date-filter" name="is_filter_after_created_date" value="1">
                          <span class="switch-label" data-on="On" data-off="Off"></span>
                          <span class="switch-handle"></span>
                        </label>
                        <p>登録時から</p>
                        <div class="wrap-object-coupon after-created-date">
                          <input type="text" class="object-coupon" name="filter_after_created_date" placeholder="0">
                          @if ($errors->has('filter_after_created_date'))
                            <div class="error pull-left">
                              <span>{{ $errors->first('filter_after_created_date') }}</span>
                            </div>
                          @endif
                          <span>日間以内</span>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>予約時間</th>
                    <td>
                      <div class="wrap-td-coupon">
                        <label class="switch switch-primary">
                          <input type="checkbox" class="switch-input" checked="" id="checkbox-time-order-filter" name="is_filter_order_duration" value="1">
                          <span class="switch-label" data-on="On" data-off="Off"></span>
                          <span class="switch-handle"></span>
                        </label>
                        <div class="wrap-object-coupon time-order-filter">
                          <select class="object-coupon" name="filter_order_duration">
                            @for($i = 0.5; $i <= 10; $i += 0.5)
                            <option value="{{ $i }}">{{ $i  }}</option>
                            @endfor
                          </select>
                          <span>時間以上</span>
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
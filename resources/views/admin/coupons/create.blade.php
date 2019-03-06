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
                <table class="table table-bordered table-coupon">
                  <!--  table-striped -->
                  <tr>
                    <th>クーポン名*</th>
                    <td>
                      <div class="wrap-td-coupon">
                        <input type="text" name="name" placeholder="クーポン名を入力してください" value="{{request()->old('name')}}">
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
                          <input type="radio" name="type" value="1" {{request()->old('type') == '' ? 'checked' : (request()->old('type') == App\Enums\CouponType::POINT ? 'checked': '')}}> ポイント数<br>
                          <div class="wrap-object-coupon coupon-point">
                            <input type="number" class="object-coupon {{request()->old('type') == '' ? '' : (request()->old('type') != App\Enums\CouponType::POINT ? 'invalid-element-coupon-input': '')}}" name="point" placeholder="0" value="{{request()->old('point')}}">
                            @if ($errors->has('point'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('point') }}</span>
                              </div>
                            @endif
                            <span class="{{request()->old('type') != App\Enums\CouponType::POINT ? 'invalid-element-coupon' : ''}}">ポイント引き</span>
                          </div>
                        </div>
                        <div class="wrap-radio-coupon">
                          <input type="radio" name="type" value="2" {{request()->old('type') == App\Enums\CouponType::TIME ? 'checked': ''}}> 時間<br>
                          <div class="wrap-object-coupon coupon-time">
                            <input type="number" class="object-coupon {{request()->old('type') != App\Enums\CouponType::TIME ? 'invalid-element-coupon-input': ''}}" name="time" value="{{request()->old('time')}}" readonly placeholder="0" min="1" max="9999">
                            @if ($errors->has('time'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('time') }}</span>
                              </div>
                            @endif
                            <span class="{{request()->old('type') != App\Enums\CouponType::TIME ? 'invalid-element-coupon' : ''}}">分無料</span>
                          </div>
                        </div>
                        <div class="wrap-radio-coupon">
                          <input type="radio" name="type" value="3" {{request()->old('type') == App\Enums\CouponType::PERCENT ? 'checked': ''}}>%<br>
                          <div class="wrap-object-coupon coupon-percent">
                            <input type="number" class="object-coupon {{request()->old('type') != App\Enums\CouponType::PERCENT ? 'invalid-element-coupon-input': ''}}" name="percent" value="{{request()->old('percent')}}" readonly placeholder="0">
                            @if ($errors->has('percent'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('percent') }}</span>
                              </div>
                            @endif
                            <span class="{{request()->old('type') != App\Enums\CouponType::PERCENT ? 'invalid-element-coupon' : ''}}">%Off</span>
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
                            <input type="number" class="object-coupon" name="max_point" value="{{request()->old('max_point')}}"  placeholder="0" min="1" max="99999">
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
                        <textarea rows="4" cols="50" name="note">{{request()->old('note')}}</textarea>
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
                <table class="table table-bordered table-coupon">
                  <!--  table-striped -->
                  <tr>
                    <th>対象ゲスト</th>
                    @php
                      $checkedIsFilterAfterCreatedDate = '';
                      $classInvalidInputCreateDate = '';
                      $classInvalidSpanCreateDate = '';
                      $readOnlyIsFilterAfterCreatedDateInput = '';
                      $textIsFilterAfterCreatedDate = '';
                      if (request()->old('is_filter_after_created_date') != null) {
                        $checkedIsFilterAfterCreatedDate = 'checked';
                      } else {
                        $classInvalidInputCreateDate = 'invalid-element-coupon-input';
                        $classInvalidSpanCreateDate = 'invalid-element-coupon';
                        $readOnlyIsFilterAfterCreatedDateInput = 'readOnly';
                        $textIsFilterAfterCreatedDate = 'invalid-element-coupon';
                      }
                    @endphp
                    <td>
                      <div class="wrap-td-coupon">
                        <label class="switch switch-primary">
                          <input type="checkbox" class="switch-input" id="checkbox-after-created-date-filter" name="is_filter_after_created_date" value="1" {{$checkedIsFilterAfterCreatedDate}}>
                          <span class="switch-label" data-on="On" data-off="Off"></span>
                          <span class="switch-handle"></span>
                        </label>
                        <p class="title-filter_after_created_date {{$textIsFilterAfterCreatedDate}}">登録時から</p>
                        <div class="wrap-object-coupon after-created-date">
                          <input type="number" class="object-coupon {{$classInvalidInputCreateDate}}" name="filter_after_created_date" value="{{request()->old('filter_after_created_date')}}" placeholder="0" min="1" max="7" {{$readOnlyIsFilterAfterCreatedDateInput}}>
                          @if ($errors->has('filter_after_created_date'))
                            <div class="error pull-left">
                              <span>{{ $errors->first('filter_after_created_date') }}</span>
                            </div>
                          @endif
                          <span class="{{$classInvalidSpanCreateDate}}">日間以内</span>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>予約時間</th>
                    @php
                      $checkedIsFilterOrderDuration = '';
                      $classInvalidInputOrderDuration = '';
                      $classInvalidSpanOrderDuration = '';
                      $disableInput = '';
                      if (request()->old('is_filter_order_duration') != null) {
                        $checkedIsFilterOrderDuration = 'checked';
                      } else {
                        $classInvalidInputOrderDuration = 'invalid-element-coupon-input';
                        $classInvalidSpanOrderDuration = 'invalid-element-coupon';
                        $disableInput = 'disabled';
                      }
                    @endphp
                    <td>
                      <div class="wrap-td-coupon">
                        <label class="switch switch-primary">
                          <input type="checkbox" class="switch-input" id="checkbox-time-order-filter" name="is_filter_order_duration" value="1" {{$checkedIsFilterOrderDuration}}>
                          <span class="switch-label" data-on="On" data-off="Off"></span>
                          <span class="switch-handle"></span>
                        </label>
                        <div class="wrap-object-coupon time-order-filter">
                          <select class="object-coupon" name="filter_order_duration" {{$disableInput}}>
                            @for($i = 0.5; $i <= 10; $i += 0.5)
                            <option value="{{ $i }}" {{request()->old('filter_order_duration') == $i ? 'selected' : ''}}>{{ $i  }}</option>
                            @endfor
                          </select>
                          <span class="{{$classInvalidSpanOrderDuration}}">時間以上</span>
                        </div>
                      </div>
                    </td>
                  </tr>
                </table>
                <div class="btn-create-coupon">
                  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#create_coupon_modal" >保存する</button>
                </div>
                <div class="modal fade" id="create_coupon_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-body">
                        <p>クーポンを保存しますか？</p>
                        <p>"はい"をタップすると、クーポンが新しく作成されます</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-accept">はい</button>
                      </div>
                    </div>
                  </div>
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
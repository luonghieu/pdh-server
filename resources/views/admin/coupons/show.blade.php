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
                        <input type="text" name="name" value="{{request()->old('name') == '' ? $coupon->name : request()->old('name')}}" placeholder="クーポン名を入力してください">
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
                          @php
                              $checkedPoint = '';
                              $classInvalidInputPoint = '';
                              $classInvalidSpanPoint = '';
                              $readOnlyPoint = '';
                              if (request()->old('type') == '') {
                                if ($coupon->type == App\Enums\CouponType::POINT) {
                                  $checkedPoint = 'checked';
                                } else {
                                  $readOnly = 'readOnly';
                                  $classInvalidInputPoint = 'invalid-element-coupon-input';
                                  $classInvalidSpanPoint = 'invalid-element-coupon';
                                }
                              } else {
                                if (request()->old('type') == App\Enums\CouponType::POINT) {
                                  $checkedPoint = 'checked';
                                } else {
                                  $readOnly = 'readOnly';
                                  $classInvalidInputPoint = 'invalid-element-coupon-input';
                                  $classInvalidSpanPoint = 'invalid-element-coupon';
                                }
                              }
                          @endphp
                          <input type="radio" name="type" value="1" {{$checkedPoint}}> ポイント数<br>
                          <div class="wrap-object-coupon coupon-point">
                            <input type="number" class="object-coupon {{$classInvalidInputPoint}}" name="point" placeholder="0" value="{{request()->old('point') == '' ? $coupon->point: request()->old('point')}}" {{$readOnlyPoint}}>
                            @if ($errors->has('point'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('point') }}</span>
                              </div>
                            @endif
                            <span class="{{$classInvalidSpanPoint}}">ポイント引き</span>
                          </div>
                        </div>
                        <div class="wrap-radio-coupon">
                          @php
                            $checkedTime = '';
                            $classInvalidInputTime = '';
                            $classInvalidSpanTime = '';
                            $readOnlyTime = '';
                            if (request()->old('type') == '') {
                              if ($coupon->type == App\Enums\CouponType::TIME) {
                                $checkedTime = 'checked';
                              } else {
                                $readOnlyTime = 'readOnly';
                                $classInvalidInputTime = 'invalid-element-coupon-input';
                                $classInvalidSpanTime = 'invalid-element-coupon';
                              }
                            } else {
                              if (request()->old('type') == App\Enums\CouponType::TIME) {
                                $checkedTime = 'checked';
                              } else {
                                $readOnlyTime = 'readOnly';
                                $classInvalidInputTime = 'invalid-element-coupon-input';
                                $classInvalidSpanTime = 'invalid-element-coupon';
                              }
                            }
                          @endphp
                          <input type="radio" name="type" value="2" {{$checkedTime}}> 時間<br>
                          <div class="wrap-object-coupon coupon-time">
                            <input type="number" class="object-coupon {{$classInvalidInputTime}}" name="time" placeholder="0" value="{{request()->old('time') == '' ? $coupon->time: request()->old('time')}}" {{$readOnlyTime}} min="1" max="9999">
                            @if ($errors->has('time'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('time') }}</span>
                              </div>
                            @endif
                            <span class="{{$classInvalidSpanTime}}">分無料</span>
                          </div>
                        </div>
                        <div class="wrap-radio-coupon">
                          @php
                            $checkedPercent = '';
                            $classInvalidInputPercent = '';
                            $classInvalidSpanPercent = '';
                            $readOnlyPercent = '';
                            if (request()->old('type') == '') {
                              if ($coupon->type == App\Enums\CouponType::PERCENT) {
                                $checkedPercent = 'checked';
                              } else {
                                $readOnlyPercent = 'readOnly';
                                $classInvalidInputPercent = 'invalid-element-coupon-input';
                                $classInvalidSpanPercent = 'invalid-element-coupon';
                              }
                            } else {
                              if (request()->old('type') == App\Enums\CouponType::PERCENT) {
                                $checkedPercent = 'checked';
                              } else {
                                $readOnlyPercent = 'readOnly';
                                $classInvalidInputPercent = 'invalid-element-coupon-input';
                                $classInvalidSpanPercent = 'invalid-element-coupon';
                              }
                            }
                          @endphp
                          <input type="radio" name="type" value="3" {{$checkedPercent}}>%<br>
                          <div class="wrap-object-coupon coupon-percent">
                            <input type="number" class="object-coupon {{$classInvalidInputPercent}}" name="percent" placeholder="0" value="{{request()->old('percent') == '' ? $coupon->percent : request()->old('percent')}}" {{$readOnlyPercent}}>
                            @if ($errors->has('percent'))
                              <div class="error pull-left">
                                <span>{{ $errors->first('percent') }}</span>
                              </div>
                            @endif
                            <span class="{{$classInvalidSpanPercent}}">%Off</span>
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
                            <input type="number" class="object-coupon" name="max_point" placeholder="0" value="{{request()->old('max_point') == '' ? $coupon->max_point: request()->old('max_point')}}" min="1" max="99999">
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
                        <textarea rows="4" cols="50" name="note">{{request()->old('note') == '' ? $coupon->note : request()->old('note')}}</textarea>
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
                    @php
                      $checkedIsFilterAfterCreatedDate = '';
                      $textIsFilterAfterCreatedDate = '';
                      $inputIsFilterAfterCreatedDate = '';
                      $spanIsFilterAfterCreatedDate = '';
                      $readOnlyIsFilterAfterCreatedDate = '';

                      if (request()->old('is_filter_after_created_date') == '') {
                        if ($coupon->is_filter_after_created_date) {
                          $checkedIsFilterAfterCreatedDate = 'checked';
                        } else {
                          $textIsFilterAfterCreatedDate = 'invalid-element-coupon';
                          $inputIsFilterAfterCreatedDate = 'invalid-element-coupon-input';
                          $spanIsFilterAfterCreatedDate = 'invalid-element-coupon';
                          $readOnlyIsFilterAfterCreatedDate = 'readOnly';
                        }
                      } else {
                        $checkedIsFilterAfterCreatedDate = 'checked';
                      }
                    @endphp
                    <td>
                      <div class="wrap-td-coupon">
                        <label class="switch switch-primary">
                          <input type="checkbox" class="switch-input" {{$checkedIsFilterAfterCreatedDate}} id="checkbox-after-created-date-filter" name="is_filter_after_created_date" value="1">
                          <span class="switch-label" data-on="On" data-off="Off"></span>
                          <span class="switch-handle"></span>
                        </label>
                        <p class="title-filter_after_created_date {{$textIsFilterAfterCreatedDate}}">登録時から</p>
                        <div class="wrap-object-coupon after-created-date">
                          <input type="text" class="object-coupon {{$inputIsFilterAfterCreatedDate}}" name="filter_after_created_date" placeholder="0" value="{{request()->old('filter_after_created_date') == '' ? ($coupon->filter_after_created_date ? $coupon->filter_after_created_date : '0') : (request()->old('filter_after_created_date') ? request()->old('filter_after_created_date') : '0')}}" {{$readOnlyIsFilterAfterCreatedDate}}>
                          @if ($errors->has('filter_after_created_date'))
                            <div class="error pull-left">
                              <span>{{ $errors->first('filter_after_created_date') }}</span>
                            </div>
                          @endif
                          <span class="{{$spanIsFilterAfterCreatedDate}}">日間以内</span>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>予約時間</th>
                    @php
                      $checkedIsFilterOrderDuration = '';
                      $textIsFilterOrderDuration = '';
                      $inputIsFilterOrderDuration = '';
                      $spanIsFilterOrderDuration = '';
                      $readOnlyIsFilterOrderDuration = '';
                      $disableInput = '';
                      if (request()->old('filter_order_duration')) {
                        $valueFilterOrderDuration = request()->old('filter_order_duration');
                      } else {
                        $valueFilterOrderDuration = $coupon->filter_order_duration;
                      }

                      if (request()->old('is_filter_order_duration') == '') {
                        if ($coupon->is_filter_order_duration) {
                          $checkedIsFilterOrderDuration = 'checked';
                        } else {
                          $textIsFilterOrderDuration = 'invalid-element-coupon';
                          $inputIsFilterOrderDuration = 'invalid-element-coupon-input';
                          $spanIsFilterOrderDuration = 'invalid-element-coupon';
                          $readOnlyIsFilterOrderDuration = 'readOnly';
                          $disableInput = 'disabled';
                        }
                      } else {
                        $checkedIsFilterOrderDuration = 'checked';
                      }
                    @endphp
                    <td>
                      <div class="wrap-td-coupon">
                        <label class="switch switch-primary">
                          <input type="checkbox" class="switch-input" {{$checkedIsFilterOrderDuration}} id="checkbox-time-order-filter" name="is_filter_order_duration" value="1">
                          <span class="switch-label" data-on="On" data-off="Off"></span>
                          <span class="switch-handle"></span>
                        </label>
                        <div class="wrap-object-coupon time-order-filter">
                          <select class="object-coupon" name="filter_order_duration" {{$disableInput}}>
                            @for($i = 0.5; $i <= 10; $i += 0.5)
                              <option value="{{ $i }}" {{$valueFilterOrderDuration == $i ? 'selected="selected"': ''}}>{{ $i  }}</option>
                            @endfor
                          </select>
                          <span class="{{$spanIsFilterAfterCreatedDate}}">時間以上</span>
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
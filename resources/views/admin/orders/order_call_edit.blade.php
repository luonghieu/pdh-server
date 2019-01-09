@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading" data-original-title>
          <h2><span class="break"></span>コール/コール内指名_予約詳細</h2>
        </div>
        <div class="panel-body">
          <div class="display-title">
            <p><b>予約ID:</b> {{ $order->id }}</p>
          </div>
        </div>
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <div class="clearfix"></div>
          <div class="info-table col-lg-10">
            <form action="">
              <table class="table table-bordered change-width-th">
                <!--  table-striped -->
                <tr>
                  <th>予約者ID</th>
                  <td>
                    <a href="{{ route('admin.users.show', ['user' => $order->user->id]) }}">
                    {{ $order->user->id }}
                    </a>
                  </td>
                </tr>
                <tr>
                  <th>予約者名</th>
                  <td>{{ $order->user->fullname }}</td>
                </tr>
                <tr>
                  <th>予約区分</th>
                  <td>{{ App\Enums\OrderType::getDescription($order->type) }}</td>
                </tr>
                <tr>
                  <th>ルームID</th>
                  <td>
                    @if ($order->status >= App\Enums\OrderStatus::ACTIVE && $order->room)
                    <a href="{{ route('admin.rooms.messages_by_room', ['room' => $order->room->id]) }}">
                    {{ $order->room->id }}
                    </a>
                    @else
                    <span>-</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>キャストを呼ぶ場所</th>
                  <td>{{ $order->address }}</td>
                </tr>
                <tr>
                  <th>キャストとの合流時間</th>
                  <td>
                    <div class="col-lg-3 input-group date edit-datetime-order-call" id="datetimepicker">
                      <input type="text" name="date_time" class="form-control" data-date-format="YYYY/MM/DD HH:mm" placeholder="{{ Carbon\Carbon::parse($order->date.' '.$order->start_time)->format('Y/m/d H:i') }}" />
                      <span class="input-group-addon init-border">
                      <span class="glyphicon glyphicon-calendar init-glyphicon"></span>
                      </span>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th>キャストの呼ぶ人数</th>
                  <td>
                    <select name="total_cast" id="">
                    @for ($i = 1; $i < 11; $i++)
                    <option value="{{ $i }}" {{ $i == $order->total_cast ? 'selected':''}}>{{ $i.'人' }}</option>
                    @endfor
                    </select>
                  </td>
                </tr>
                <tr>
                  <th>キャストを呼ぶ時間</th>
                  <td>
                    <select name="duration" id="">
                    @for ($i = 1; $i < 11; $i++)
                    <option value="{{ $i }}" {{ $i == $order->duration ? 'selected':''}}>{{ $i.'時間' }}</option>
                    @endfor
                    </select>
                  </td>
                </tr>
                <tr>
                  <th>キャストクラス</th>
                  <td>
                    <select class="cast-class" name="class_id">
                    @foreach ($castClasses as $castClass)
                    <option value="{{ $castClass->id }}" {{ ($order->castClass->id == $castClass->id) ? 'selected' : '' }}>{{ $castClass->name }}</option>
                    @endforeach
                    </select>
                  </td>
                </tr>
                <tr>
                  <th>　予定合計ポイント</th>
                  <td>
                    @if (in_array($order->status, [App\Enums\OrderStatus::ACTIVE, App\Enums\OrderStatus::PROCESSING, App\Enums\OrderStatus::DONE]))
                    @php
                    $tempPoint = 0;
                    foreach ($order->casts as $cast) {
                    $tempPoint+=$cast->pivot->temp_point;
                    }
                    @endphp
                    {{ number_format($tempPoint).'P' }}
                    @else
                    {{ number_format($order->temp_point).'P' }}
                    @endif
                </tr>
                <tr>
                  <th>ステータス</th>
                  <td class="wrap-status">
                    @if ($order->payment_status != null)
                    @if ($order->status == App\Enums\OrderStatus::PROCESSING)
                    <span>{{ App\Enums\OrderStatus::getDescription($order->status) }}</span>
                    @else
                    <span>{{ App\Enums\OrderPaymentStatus::getDescription($order->payment_status) }}</span>
                    @endif
                    @else
                    @if (App\Enums\OrderStatus::DENIED == $order->status || App\Enums\OrderStatus::CANCELED == $order->status)
                    @if ($order->type == App\Enums\OrderType::NOMINATION && (count($order->nominees) > 0 ? empty($order->nominees[0]->pivot->accepted_at) : false))
                    <span>提案キャンセル</span>
                    @else
                    @if ($order->cancel_fee_percent == 0)
                    <span>確定後キャンセル (キャンセル料なし)</span>
                    @else
                    <span>確定後キャンセル (キャンセル料あり)</span>
                    @endif
                    @endif
                    @else
                    <span>{{ App\Enums\OrderStatus::getDescription($order->status) }}</span>
                    @endif
                    @endif
                    @if (App\Enums\OrderPaymentStatus::EDIT_REQUESTING == $order->payment_status)
                    <button class="change-time payment-request btn-order-call" data-toggle="modal" data-target="#payment-request">ステータスを売上申請待ちに切り替える</button>
                    <button class="change-time btn-pay-point" data-toggle="modal" data-target="#pay-point">ステータスをポイント決済完了に切り替える</button>
                    @endif
                  </td>
                </tr>
              </table>
              <div class="panel-body">
                <div class="display-title">
                  <p>指名中キャスト一覧</p>
                </div>
                <div class="display-title change-cast-order-call">
                  <a href="javascript::void(0)" data-toggle="modal" data-target="#choose-cast-nominee">+別のキャストを追加する</a>
                </div>
              </div>
              <table class="table table-striped table-bordered bootstrap-datatable">
                <thead>
                  <tr>
                    <th>ユーザーID</th>
                    <th>キャスト名</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @if (count($order->nominees) == 0)
                  <tr>
                    <td colspan="8">{{ trans('messages.cast_not_found') }}</td>
                  </tr>
                  @else
                  @foreach ($order->nominees as $nominee)
                  <tr>
                    <td>{{ $nominee->id }}</td>
                    <td>{{ $nominee->nickname }}</td>
                    <td><button class=" btn btn-detail">このキャストを削除する</button></td>
                  </tr>
                  @endforeach
                  @endif
                </tbody>
              </table>
              <div class="panel-body">
                <div class="display-title">
                  <p>応募中キャスト一覧</p>
                </div>
                <div class="display-title change-cast-order-call">
                  <a href="javascript::void(0)" data-toggle="modal" data-target="#choose-cast-candidate">+別のキャストを追加する</a>
                </div>
              </div>
              <table class="table table-striped table-bordered bootstrap-datatable">
                <thead>
                  <tr>
                    <th>ユーザーID</th>
                    <th>キャスト名</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @if (count($order->candidates) == 0)
                  <tr>
                    <td colspan="8">{{ trans('messages.cast_not_found') }}</td>
                  </tr>
                  @else
                  @foreach ($order->candidates as $candidate)
                  <tr>
                    <td>{{ $candidate->id }}</td>
                    <td>{{ $candidate->nickname }}</td>
                    <td><button class=" btn btn-detail">このキャストを削除する</button></td>
                  </tr>
                  @endforeach
                  @endif
                </tbody>
              </table>
              @if (count($castsMatching) >= 1)
              <div class="panel-body">
                <div class="display-title">
                  <p>マッチングしているキャスト一覧</p>
                </div>
                <div class="display-title change-cast-order-call">
                  <a href="javascript::void(0)" data-toggle="modal" data-target="#choose-cast-matching">+別のキャストを追加する</a>
                </div>
              </div>
              <table class="table table-striped table-bordered bootstrap-datatable">
                <thead>
                  <tr>
                    <th>ユーザーID</th>
                    <th>キャスト名</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($castsMatching as $castMatching)
                  <tr>
                    <td>{{ $castMatching->id }}</td>
                    <td>{{ $castMatching->nickname }}</td>
                    @if (!$castMatching->pivot->started_at)
                    <td><button class=" btn btn-detail">このキャストを削除する</button></td>
                    @endif
                  </tr>
                  @endforeach
                </tbody>
              </table>
              @endif
              <div class="wrapper-button">
                <a href="{{ route('admin.orders.call', ['order' =>$order->id]) }}" class="btn btn-info">戻る</a>
                <button type="submit" class="btn btn-info">予約内容を変更する</button>
              </div>
            </form>
            <div class="modal fade" id="choose-cast-nominee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <p>キャストを選択してください</p>
                    <div class="panel-body handling">
                      <div class="search">
                        <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="">
                      </div>
                    </div>
                    <div class="wrapper-table">
                      <table class="table table-striped table-bordered bootstrap-datatable">
                        <thead>
                          <tr>
                            <th class="column-checkbox"></th>
                            <th>ユーザーID</th>
                            <th>キャスト名</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-accept">このキャストを選択する</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="choose-cast-candidate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <p>キャストを選択してください</p>
                    <div class="panel-body handling">
                      <div class="search">
                        <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="">
                      </div>
                    </div>
                    <div class="wrapper-table">
                      <table class="table table-striped table-bordered bootstrap-datatable table-sm">
                        <thead>
                          <tr>
                            <th class="column-checkbox"></th>
                            <th>ユーザーID</th>
                            <th>キャスト名</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-accept">このキャストを選択する</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="choose-cast-matching" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <p>キャストを選択してください</p>
                    <div class="panel-body handling">
                      <div class="search">
                        <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="">
                      </div>
                    </div>
                    <div class="wrapper-table">
                      <table class="table table-striped table-bordered bootstrap-datatable">
                        <thead>
                          <tr>
                            <th class="column-checkbox"></th>
                            <th>ユーザーID</th>
                            <th>キャスト名</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                          <tr>
                            <td class="select-checkbox">
                              <input class="verify-checkboxs"
                                type="checkbox"
                                value=""
                                name="casts_id[]">
                            </td>
                            <td>1</td>
                            <td>2</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-accept">このキャストを選択する</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection

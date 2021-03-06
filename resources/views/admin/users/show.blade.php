@extends('layouts.admin')
@section('admin.modal')
<div class="modal fade" id="popup-img" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <a href="#" class="init-close" data-dismiss="modal">&times;</a>
      <div class="clearfix"></div>
      <div class="mt-btn">
        <!-- set avatar default -->
        <form action="" method="POST" id="set-avatar-default">
          {{ csrf_field() }}
          {{ method_field('PATCH') }}
          <div class="modal-body">
            <button type="submit" class="btn btn-default init-w-default" data-toggle="modal"><span>メインにする</span></button>
          </div>
        </form><!--  -->
        <!-- delete avatar -->
        <form action="" method="POST" id="delete-avatar">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <div class="modal-body">
            <button type="submit" class="btn btn-default init-w-default" data-toggle="modal"><span>削除する</span></button>
          </div>
        </form><!--  -->
        <!-- update avatar -->
        <div class="modal-body">
          <label class="img-default btn btn-default init-w-default">
            <span>変更する</span>
            <input type="file" data-toggle="modal" name="image" id="update-avatar" accept="image/*" style="display: none">
          </label>
          <div class="popup-error-message"></div>
        </div><!--  -->
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="campaign_participated" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p>このゲストは11月キャンペーンを利用しましたか？</p>
        <p>「はい」をタップすると、キャンペーン告知のポップアップが表示されなくなります。</p>
      </div>
      <div class="modal-footer">
        <form action="{{ route('admin.users.campaign_participated',['user' => $user->id]) }}" method="post">
          {{ csrf_field() }}
          <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-accept">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="register_cast" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p>このゲストを、キャストへ変更しますか？</p>
      </div>
      <div class="modal-footer">
        <form action="{{ route('admin.casts.register',['user' => $user->id]) }}" method="get">
          <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-accept">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="register_guest" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p>このユーザーのステータスをゲストに変更しますか？</p>
      </div>
      <div class="modal-footer">
        <form action="{{ route('admin.users.register_guest',['user' => $user->id]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-accept">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="inactiveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p>本当にこのアカウントを凍結しますか？</p>
        <p>実行すると、アプリ上で予約機能を使うことができなくなります</p>
      </div>
      <div class="modal-footer">
        <form action="" method="POST">
          <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-accept">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="activeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p>本当にこのアカウントの凍結を解除しますか？</p>
      </div>
      <div class="modal-footer">
        <form action="" method="POST">
          <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-accept">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="btn-qr-code" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        @if ($user->line_qr)
        <img src="{{ $user->line_qr}}" alt="">
        @else
        <p>QRコードが登録されていません</p>
        @endif
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="btn-id-image" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        @if (!$user->front_id_image)
        <p>画像が見つかりません</p>
        @else
          @if (@getimagesize($user->front_id_image))
          <img src="{{ $user->front_id_image }}" alt="">
          @else
          <p>エラーが発生しました</p>
          @endif
        @endif
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="delete_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p>本当にこのアカウントを削除しますか</p>
      </div>
      <div class="modal-footer">
        <form action="{{ route('admin.users.delete',['user' => $user->id]) }}" method="POST">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-accept">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
@php
  $resigned = true;
  if($user->resign_status != \App\Enums\ResignStatus::APPROVED) {
    $resigned = false;
  }
@endphp
@if($user->is_guest)
<div class="modal fade" id="change-payment-method" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        @if(!$user->is_multi_payment_method)
        <p>現金決済を可能にしますか？</p>
        @else
          <p>現金決済を不可にしますか？</p>
        @endif
      </div>
      <div class="modal-footer">
        <form action="{{ route('admin.users.change_payment_method',['user' => $user->id]) }}" method="post">
          {{ csrf_field() }}
          <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-accept">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif
@stop
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  @if(Session::has('error'))
    <div class="alert alert-danger fade in" id="flash">
      <a href="#" class="close" data-dismiss="alert">&times;</a>
      {{ Session::get('error') }}
    </div>
  @endif
  @if(Session::has('message'))
  <div class="alert alert-success fade in" id="flash">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    {{ Session::get('message') }}
  </div>
  @endif
  <div class="message-alert"></div>
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @if ($user->is_cast)
          @include('admin.partials.menu-tab-cast',compact('user'))
        @else
          @include('admin.partials.menu-tab',compact('user'))
        @endif
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          @if (!$resigned)
          <div class="col-lg-6 wrap-qr-code">
            <div class="list-avatar">
              @include('admin.users.content_image', ['avatars' => $user->avatars])
            </div>
            <div class="clear"></div>
            @if ($user->is_cast)
            <div class="btn-qr">
              <button type="button" data-toggle="modal" data-target="#btn-qr-code" class="btn btn-info">QRコードを表示する</button>
              <button type="button" data-toggle="modal" data-target="#btn-id-image" class="btn btn-info">身分証明書を表示する</button>
            </div>
            @endif
          </div>
          @endif
          <div class="clearfix"></div>
          <div class="info-table col-lg-8">
            <table class="table table-bordered">
              <!--  table-striped -->
              <tr>
                <th>ユーザーID</th>
                <td>{{ $user->id }}</td>
              </tr>
              <tr>
                <th>利用サービス</th>
                <td>{{ (!$resigned) ? App\Enums\DeviceType::getDescription($user->device_type) : '' }}</td>
              </tr>
              @if ($user->is_cast)
                <tr>
                  <th>メールアドレス</th>
                  <td>{{ (!$resigned) ? $user->email : '' }}</td>
                </tr>
                <tr>
                  <th>氏名</th>
                  <td>{{ (!$resigned) ? $user->fullname : '' }}</td>
                </tr>
                <tr>
                  <th>ふりがな</th>
                  <td>{{ (!$resigned) ? $user->fullname_kana : '' }}</td>
                </tr>
                <tr>
                  <th>キャストクラス</th>
                  <td>
                    @if(!$resigned)
                      @php
                        $classes = config('common.default_cost_rate');
                      @endphp
                      <form action="{{ route('admin.users.change_cast_class', ['user' => $user->id]) }}" class="form-cast-class" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" id="input-cost-rate" name="input_cost_rate">
                        <select class="cast-class" id="class-id" name="cast_class">
                          @foreach ($castClasses as $castClass)
                            <option value="{{ $castClass->id }}" {{ ($user->class_id == $castClass->id) ? 'selected' : '' }}>{{ $castClass->name }}</option>
                          @endforeach
                        </select>
                        <button type="submit" class="btn btn-info btn-sm mt-2">変更する</button>
                      </form>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>報酬</th>
                  <td>
                    @if(!$resigned)
                    <form action="{{ route('admin.casts.update_cost_rate', ['user' => $user->id]) }}" class="w-form" method="POST">
                      {{ csrf_field() }}
                      {{ method_field('PUT') }}
                      <select class="w-option" id="cost-rate" name="cost_rate">
                        @foreach ($editableCostRates as $editableCostRate)
                          <option value="{{ $editableCostRate }}" {{ ($user->cost_rate == $editableCostRate) ? 'selected' : '' }}>{{ $editableCostRate * 100 }}%</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-info btn-sm mt-2">変更する</button>
                    </form>
                    @endif
                  </td>
                </tr>
                @if ($prefectures->count())
                <tr>
                  <th style="color: red;">エリア</th>
                  <td>
                    @if(!$resigned)
                    <form action="{{ route('admin.users.change_prefecture', ['user' => $user->id]) }}" class="form-cast-class" method="post">
                      {{ csrf_field() }}
                      <select class="cast-class" name="prefecture">
                        @foreach ($prefectures as $prefecture)
                          <option value="{{ $prefecture->id }}" {{ ($user->prefecture_id == $prefecture->id) ? 'selected' : '' }}>{{ $prefecture->name }}</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-info btn-sm mt-2">変更する</button>
                    </form>
                    @endif
                  </td>
                </tr>
                @endif
              @endif
              <tr>
                <th>ニックネーム</th>
                <td>{{ $user->nickname }}</td>
              </tr>
              @if($user->is_guest)
              <tr>
                <th>電話番号</th>
                <td>{{ (!$resigned) ? ($user->phone ? $user->phone : '-') : '' }}</td>
              </tr>
              @endif
              @if ($user->is_cast)
              <tr>
                <th>30分あたりのポイント</th>
                <td>
                  @if(!$resigned)
                    @php
                      $arrCost = [];
                      for($i =0; $i<15000; $i+=100) {
                        array_push($arrCost, $i);
                      }

                      for($i =15000; $i<=75000; $i+=5000) {
                        array_push($arrCost, $i);
                      }

                      sort($arrCost);
                    @endphp
                    <form action="{{ route('admin.users.change_cost', ['user' => $user->id]) }}" class="form-cast-class" method="post">
                      {{ csrf_field() }}
                      <select class="cast-class" name="cast_cost">
                        @foreach($arrCost as $cost)
                          <option value="{{ $cost }}" {{ $user->cost == $cost ? 'selected' : ''}}>{{number_format($cost) }}</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-info btn-sm mt-2">変更する</button>
                    </form>
                  @endif
                </td>
              </tr>
              <tr>
                <th>キャスト一覧表示優先ランク</th>
                <td>
                  @if(!$resigned)
                    @php
                      $arrRank = App\Enums\UserRank::toSelectArray();
                      krsort($arrRank);
                    @endphp
                    <form action="{{ route('admin.users.change_rank', ['user' => $user->id]) }}" class="form-cast-class" method="post">
                      {{ csrf_field() }}
                      <select class="cast-class" name="cast_rank">
                        @foreach($arrRank as $key => $rank)
                          <option value="{{ $key }}" {{ $user->rank == $key ? 'selected' : ''}}>{{ $rank }}</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-info btn-sm mt-2">変更する</button>
                    </form>
                  @endif
                </td>
              </tr>
              @endif
              <tr>
                <th>性別</th>
                <td>{{ (!$resigned) ? App\Enums\UserGender::getDescription($user->gender) : '' }}</td>
              </tr>
              <tr>
                <th>生年月日</th>
                <td>{{ (!$resigned) ? (($user->date_of_birth) ? Carbon\Carbon::parse($user->date_of_birth)->format('Y年m月d日') : "") : '' }}</td>
              </tr>
              <tr>
                <th>年齢</th>
                <td>{{ (!$resigned) ? $user->age : '' }}</td>
              </tr>
              @if ($user->is_cast)
              <tr>
                <th>電話番号</th>
                <td>{{ (!$resigned) ? $user->phone : '' }}</td>
              </tr>
              <tr>
                <th>LINE ID</th>
                <td>{{ (!$resigned) ? $user->line_id : '' }}</td>
              </tr>
              @endif
              <tr>
                <th>基本情報：身長</th>
                <td>{{ (!$resigned) ? (($user->height === null) ? '':getUserHeight($user->height)) : '' }}</td>
              </tr>
              <tr>
                <th>基本情報：年収</th>
                <td>{{ (!$resigned) ? ($user->salary ? $user->salary->name : "") : '' }}</td>
              </tr>
              <tr>
                <th>基本情報：体型</th>
                <td>{{ (!$resigned) ? ($user->bodyType ? $user->bodyType->name : "") : '' }}</td>
              </tr>
              <tr>
                <th>基本情報：稼働エリア</th>
                <td>{{ (!$resigned) ? (($user->prefecture_id) ? $user->prefecture->name : "") : '' }}</td>
              </tr>
              <tr>
                <th>基本情報：出身地</th>
                <td>{{ (!$resigned) ? ($user->hometown ? $user->hometown->name : "") : '' }}</td>
              </tr>
              <tr>
                <th>基本情報：お仕事</th>
                <td>
                  {{ (!$resigned) ? ($user->job ? $user->job->name : "") : '' }}
                </td>
              </tr>
              <tr>
                <th>基本情報：お酒</th>
                <td>{{ (!$resigned) ? (App\Enums\DrinkVolumeType::getDescription($user->drink_volume_type)) : '' }}</td>
              </tr>
              <tr>
                <th>基本情報：タバコ</th>
                <td>{{ (!$resigned) ? (App\Enums\SmokingType::getDescription($user->smoking_type)) : '' }}</td>
              </tr>
              <tr>
                <th>基本情報：兄弟姉妹</th>
                <td>{{ (!$resigned) ? (App\Enums\SiblingsType::getDescription($user->siblings_type)) : '' }}</td>
              </tr>
              <tr>
                <th>基本情報：同居人</th>
                <td>{{ (!$resigned) ? (App\Enums\CohabitantType::getDescription($user->cohabitant_type)) : '' }}</td>
              </tr>
              <tr>
                <th>自己紹介</th>
                <td>{{ (!$resigned) ? $user->description : '' }}</td>
              </tr>
              <tr>
                <th>ひとこと</th>
                <td>{{ (!$resigned) ? $user->intro : '' }}</td>
              </tr>
              <tr>
                <th>会員区分</th>
                <td>
                  @if(!$resigned)
                    @php
                        $textCastTemp = '';
                        if ($user->cast_transfer_status == App\Enums\CastTransferStatus::VERIFIED_STEP_ONE
                            || $user->cast_transfer_status == App\Enums\CastTransferStatus::PENDING
                            || $user->cast_transfer_status == App\Enums\CastTransferStatus::APPROVED
                            || ($user->cast_transfer_status == App\Enums\CastTransferStatus::DENIED
                                && $user->gender == App\Enums\UserGender::FEMALE)) {
                            $textCastTemp = '(仮)';
                        }
                    @endphp
                    {{ App\Enums\UserType::getDescription($user->type) }}{{ $textCastTemp }}
                  @endif
                </td>
              </tr>
              <tr>
                <th>ステータス</th>
                <td>
                  @if(!$resigned)
                    @if($user->status == App\Enums\Status::ACTIVE)
                      {{ App\Enums\Status::getDescription($user->status) }}
                    @else
                      @if($user->resign_status == App\Enums\ResignStatus::APPROVED)
                        退会
                      @else
                        凍結
                      @endif
                    @endif
                  @endif
                </td>
              </tr>
              @if ($user->is_cast)
              <tr>
                <th>備考</th>
                <td>
                  @if(!$resigned)
                    <form action="{{ route('admin.casts.update_note', ['user' => $user->id]) }}" method="POST">
                      {{ csrf_field() }}
                      {{ method_field('PUT') }}
                      <textarea name="note" class="h-5" placeholder="入力してください">{!! $user->note !!}</textarea>
                      <button type="submit" class="pull-right btn btn-info btn-sm">変更する</button>
                    </form>
                  @endif
                </td>
              </tr>
              <tr>
                <th>キャスト登録日時</th>
                <td>{{ ($user->accept_request_transfer_date == null) ? '' : Carbon\Carbon::parse($user->accept_request_transfer_date)->format('Y/m/d H:i') }}</td>
              </tr>
              @endif
              <tr>
                <th>登録日時</th>
                <td>{{ Carbon\Carbon::parse($user->created_at)->format('Y/m/d H:i') }}</td>
              </tr>
            </table>
          </div>
          @if(!$resigned)
            <div class="col-lg-9">
              @if (!$user->deleted_at)
              <div class="delete-user pull-left">
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_user">退会済みにする</button>
              </div>
              @endif
              <div class="active-user pull-right">
                @php
                  if($user->status == App\Enums\Status::ACTIVE) {
                    $nameId = "#inactiveModal";
                    $title = "アカウントを凍結する";
                  } else {
                    $nameId = "#activeModal";
                    $title = "凍結を解除する";
                  }
                @endphp
                @if($user->is_guest)
                  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#change-payment-method">
                    @if(!$user->is_multi_payment_method)
                    銀行振込を可能にする
                    @else
                    銀行振込を不可にする
                    @endif
                  </button>
                  <button type="button" class="btn {{ !$user->campaign_participated ? 'btn-info' : 'btn-default' }}"
                    data-toggle="modal" data-target="#campaign_participated" {{ !$user->campaign_participated ?: 'disabled' }}>
                    11月キャンペーン利用完了
                  </button>
                  @if ($user->gender == App\Enums\UserGender::MALE && $user->cast_transfer_status == App\Enums\CastTransferStatus::DENIED)
                  @else
                  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#register_cast">キャストへ変更する</button>
                  @endif
                @endif
                @if($user->is_cast)
                  <button type="button" class="btn btn-info" data-toggle="modal" data-target="#register_guest">ゲストに変更する</button>
                @endif

                <button type="submit" class="btn btn-info" data-toggle="modal" data-target="{{ $nameId }}">{{ $title }}</button>
              </div>
            </div>
          @endif
        </div>

      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
@section('admin.js')
  <input type="hidden" id="user_id" value="{{ $user->id }}" />
  <input type="hidden" id="url-upload" value="{{ route('admin.avatars.upload', $user->id) }}" />
  <script src="/assets/admin/js/pages/upload_image.js"></script>

  <script type="text/javascript">
    const classes = JSON.parse('<?php echo json_encode(config('common.default_cost_rate')); ?>');

    $('body').on('click', '#class-id', function () {
      Object.keys(classes).forEach(function(key) {
        if ($('#class-id').val() == key) {
          $('#cost-rate').val(classes[key]);

          $('#input-cost-rate').val($('#cost-rate').val());
        }
      });
    });
  </script>
@stop

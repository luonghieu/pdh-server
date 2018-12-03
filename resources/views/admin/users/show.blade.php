@extends('layouts.admin')
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
          <div class="col-lg-6 wrap-qr-code">
            <div class="list-avatar">
              @include('admin.users.content_image', ['avatars' => $user->avatars])
            </div>
            <div class="clear"></div>
            @if ($user->is_cast)
            <div class="btn-qr">
              <button type="button" data-toggle="modal" data-target="#btn-qr-code" class="btn-detail">QRコードを表示する</button>
            </div>
            @endif
          </div>
          <div class="clearfix"></div>
          <div class="info-table col-lg-6">
            <table class="table table-bordered">
              <!--  table-striped -->
              <tr>
                <th>ユーザーID</th>
                <td>{{ $user->id }}</td>
              </tr>
              @if ($user->is_cast)
                <tr>
                  <th>メールアドレス</th>
                  <td>{{ $user->email }}</td>
                </tr>
                <tr>
                  <th>氏名</th>
                  <td>{{ $user->fullname }}</td>
                </tr>
                <tr>
                  <th>ふりがな</th>
                  <td>{{ $user->fullname_kana }}</td>
                </tr>
                <tr>
                  <th>キャストクラス</th>
                  <td>
                    <form action="{{ route('admin.users.change_cast_class', ['user' => $user->id]) }}" class="form-cast-class" method="post">
                      {{ csrf_field() }}
                      <select class="cast-class" name="cast_class">
                        @foreach ($castClasses as $castClass)
                          <option value="{{ $castClass->id }}" {{ ($user->class_id == $castClass->id) ? 'selected' : '' }}>{{ $castClass->name }}</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn-change-cast-class">変更する</button>
                    </form>
                  </td>
                </tr>
                @if ($prefectures->count())
                <tr>
                  <th style="color: red;">エリア</th>
                  <td>
                    <form action="{{ route('admin.users.change_prefecture', ['user' => $user->id]) }}" class="form-cast-class" method="post">
                      {{ csrf_field() }}
                      <select class="cast-class" name="prefecture">
                        @foreach ($prefectures as $prefecture)
                          <option value="{{ $prefecture->id }}" {{ ($user->prefecture_id == $prefecture->id) ? 'selected' : '' }}>{{ $prefecture->name }}</option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn-change-cast-class">変更する</button>
                    </form>
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
                <td>{{ $user->phone ? $user->phone : '-' }}</td>
              </tr>
              @endif
              @if ($user->is_cast)
              <tr>
                <th>30分あたりのポイント</th>
                <td>
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
                    <button type="submit" class="btn-change-cast-class">変更する</button>
                  </form>
                </td>
              </tr>
              <tr>
                <th>キャスト一覧表示優先ランク</th>
                <td>
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
                    <button type="submit" class="btn-change-cast-class">変更する</button>
                  </form>
                </td>
              </tr>
              @endif
              <tr>
                <th>性別</th>
                <td>{{ App\Enums\UserGender::getDescription($user->gender) }}</td>
              </tr>
              <tr>
                <th>生年月日</th>
                <td>{{ ($user->date_of_birth) ? Carbon\Carbon::parse($user->date_of_birth)->format('Y年m月d日') : "" }}</td>
              </tr>
              <tr>
                <th>年齢</th>
                <td>{{ $user->age }}</td>
              </tr>
              @if ($user->is_cast)
              <tr>
                <th>電話番号</th>
                <td>{{ $user->phone }}</td>
              </tr>
              <tr>
                <th>LINE ID</th>
                <td>{{ $user->line_id }}</td>
              </tr>
              @endif
              <tr>
                <th>基本情報：身長</th>
                <td>{{ ($user->height === null) ? '':getUserHeight($user->height) }}</td>
              </tr>
              <tr>
                <th>基本情報：年収</th>
                <td>{{ $user->salary ? $user->salary->name : "" }}</td>
              </tr>
              <tr>
                <th>基本情報：体型</th>
                <td>{{ $user->bodyType ? $user->bodyType->name : "" }}</td>
              </tr>
              <tr>
                <th>基本情報：居住地</th>
                <td>{{ ($user->prefecture_id) ? $user->prefecture->name : "" }}</td>
              </tr>
              <tr>
                <th>基本情報：出身地</th>
                <td>{{ $user->hometown ? $user->hometown->name : "" }}</td>
              </tr>
              <tr>
                <th>基本情報：お仕事</th>
                <td>
                  {{ $user->job ? $user->job->name : "" }}
                </td>
              </tr>
              <tr>
                <th>基本情報：お酒</th>
                <td>{{ App\Enums\DrinkVolumeType::getDescription($user->drink_volume_type) }}</td>
              </tr>
              <tr>
                <th>基本情報：タバコ</th>
                <td>{{ App\Enums\SmokingType::getDescription($user->smoking_type) }}</td>
              </tr>
              <tr>
                <th>基本情報：兄弟姉妹</th>
                <td>{{ App\Enums\SiblingsType::getDescription($user->siblings_type) }}</td>
              </tr>
              <tr>
                <th>基本情報：同居人</th>
                <td>{{ App\Enums\CohabitantType::getDescription($user->cohabitant_type) }}</td>
              </tr>
              <tr>
                <th>自己紹介</th>
                <td>{{ $user->description }}</td>
              </tr>
              <tr>
                <th>ひとこと</th>
                <td>{{ $user->intro}}</td>
              </tr>
              <tr>
                <th>会員区分</th>
                <td>{{ App\Enums\UserType::getDescription($user->type) }}</td>
              </tr>
              <tr>
                <th>ステータス</th>
                <td>{{ App\Enums\Status::getDescription($user->status) }}</td>
              </tr>
              <tr>
                <th>登録日時</th>
                <td>{{ Carbon\Carbon::parse($user->created_at)->format('Y/m/d H:i') }}</td>
              </tr>
            </table>
          </div>
          <div class="col-lg-9">
            @if (!$user->deleted_at)
            <div class="delete-user pull-left">
              <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete_user">アカウントを削除する</button>
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
                <button type="button" class="btn {{ !$user->campaign_participated ? 'btn-info' : 'btn-default' }}"
                  data-toggle="modal" data-target="#campaign_participated" {{ !$user->campaign_participated ?: 'disabled' }}>
                  11月キャンペーン利用完了
                </button>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#register_cast">キャストへ変更する</button>
              @endif
              @if($user->is_cast)
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#register_guest">ゲストに変更する</button>
              @endif

              <button type="submit" class="btn btn-info" data-toggle="modal" data-target="{{ $nameId }}">{{ $title }}</button>
            </div>
          </div>
        </div>
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
        <div class="modal fade" id="delete_user" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
               aria-hidden="true">
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
@stop

@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab',compact('user'))
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <div class="info-table col-lg-6">
            <div class="list-avatar">
              @foreach ($user->avatars as $avatar)
                <img src="{{ $avatar->path }}" alt="avatar">
              @endforeach
            </div>
            <table class="table table-bordered">
              <!--  table-striped -->
              <tr>
                <th>ユーザーID</th>
                <td>{{ $user->id}}</td>
              </tr>
              <tr>
                <th>ニックネーム</th>
                <td>{{ $user->nickname }}</td>
              </tr>
              <tr>
                <th>性別</th>
                <td>{{ App\Enums\UserGender::getDescription($user->gender) }}</td>
              </tr>
              <tr>
                <th>生年月日</th>
                <td>{{ Carbon\Carbon::parse($user->date_of_birth)->format('Y年m月d日') }}</td>
              </tr>
              <tr>
                <th>年齢層</th>
                <td>{{ $user->age }}</td>
              </tr>
              <tr>
                <th>基本情報：身長</th>
                <td>{{ getUserHeight($user->height) }}</td>
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
                <td>{{ $user->prefecture ? $user->prefecture->name : "" }}</td>
              </tr>
              <tr>
                <th>基本情報；出身地</th>
                <td>{{ $user->prefecture ? $user->prefecture->name : "" }}</td>
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
                <td>{{ App\Enums\CohabitantType::getDescription($user->cohabitant_type) }}</td>
              </tr>
              <tr>
                <th>自己紹介</th>
                <td>{{ $user->intro }}</td>
              </tr>
              <tr>
                <th>ひとこと</th>
                <td>{{ $user->description }}</td>
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
            <div class="active-user">
              @php
                if($user->status == App\Enums\Status::ACTIVE) {
                  $nameId = "#inactiveModal";
                  $title = "アカウントを凍結する";
                } else {
                  $nameId = "#activeModal";
                  $title = "凍結を解除する";
                }
              @endphp
                <button type="submit" class="btn btn-info" data-toggle="modal" data-target="{{ $nameId }}">{{ $title }}</button>
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
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection

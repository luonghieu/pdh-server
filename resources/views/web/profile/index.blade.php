@section('title', 'プロフィール')
@section('screen.id', 'gm1')

@extends('layouts.web')
@section('web.extra')
<div class="modal_wrap">
  <input id="trigger3" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger3" class="modal_trigger" id="profile-popup"></label>
      <div class="modal_content modal_content-btn3">
        <div class="content-in" id="profile-message">
          <h2></h2>
        </div>
      </div>
    </div>
</div>
@endsection
@section('web.content')
<div class="cast-profile">
  <section class="profile-photo">
    <div class="profile-photo__top">
      @if ($profile['avatars'] && @getimagesize($profile['avatars'][0]['thumbnail']))
      <img class="init-image-radius" src="{{ $profile['avatars'][0]['thumbnail'] }}" alt="">
      @else
      <img class="init-image-radius" src="{{ asset('assets/web/images/ge1/user_icon.svg') }}" alt="">
      @endif
    </div>
    <div class="profile-photo__list">
      <ul>
        @foreach ($profile['avatars'] as $avatar)
          @if (@getimagesize($avatar['thumbnail']))
          <li class="css-img"><img src="{{ $avatar['thumbnail'] }}" alt=""></li>
          @else
          <li class="css-img"><img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt=""></li>
          @endif
        @endforeach
      </ul>
    </div>
  </section>
  <!-- profile-photos -->

  <section class="portlet">
    <div class="portlet-header">
      <h2 class="portlet-header__title">ひとこと</h2>
    </div>
    <div class="portlet-content">
      @if (!$profile['intro'])
      <p class="portlet-header__title">ひとこと設定されていません</p>
      @else
      <p class="portlet-content__text">{{ $profile['intro'] }}</p>
      @endif
    </div>
  </section>
  <!-- profile-word -->

  <section class="portlet">
    <div class="portlet-header">
      <h2 class="portlet-header__title">自己紹介</h2>
    </div>
    <div class="portlet-content">
      @if (!$profile['description'])
      <p class="portlet-header__title">自己紹介設定されていません</p>
      @else
      <p class="portlet-content__text">{{ $profile['description'] }}</p>
      @endif
    </div>
  </section>
  <!-- profile-introduction -->

  <section class="portlet">
    <div class="portlet-header">
      <h2 class="portlet-header__title">基本情報</h2>
    </div>
    <div class="portlet-content">
      <ul class="portlet-content__list">
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">ニックネーム</p>
          <p class="portlet-content__value">{{ $profile['nickname'] or '未設定' }}</p>
        </li>
        @php
        switch ($profile['gender']) {
            case '0':
                $gender = '非公開';
                break;
            case '1':
                $gender = '男性';
                break;
            case '2':
                $gender = '女性';
                break;

            default:
                $gender = '未設定';
                break;
        }
        @endphp
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">性別</p>
          <p class="portlet-content__value">{{ $gender }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">生年月日</p>
          <p class="portlet-content__value">
            {{ (!$profile['date_of_birth']) ? '未設定' : \Carbon\Carbon::parse($profile['date_of_birth'])->format('Y年m月d日') }}
          </p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">年齢</p>
          <p class="portlet-content__value">{{ $profile['age'] or '未設定' }}{{ (!$profile['age']) ? '' : '歳' }}</p>
        </li>
        @php
        switch ($profile['height']) {
            case '0':
                $height = '非公開';
                break;

            default:
                $height = (!$profile['height']) ? '' : $profile['height'] . 'cm';
                break;
        }
        @endphp
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">身長</p>
          <p class="portlet-content__value">{{ $height or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">年収</p>
          <p class="portlet-content__value">
            {{ (!$profile['salary']) ? '未設定' : $profile['salary'] }}
          </p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">体型</p>
          <p class="portlet-content__value">
            {{ (!$profile['body_type']) ? '未設定' : $profile['body_type'] }}
          </p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">居住地</p>
          <p class="portlet-content__value">
            {{ (!$profile['prefecture']) ? '未設定' : $profile['prefecture'] }}
          </p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">出身地</p>
          <p class="portlet-content__value">{{ (!$profile['hometown']) ? '未設定' : $profile['hometown'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">お仕事</p>
          <p class="portlet-content__value">{{ (!$profile['job']) ? '未設定' : $profile['job'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">お酒</p>
          <p class="portlet-content__value">{{ (!$profile['drink_volume']) ? '未設定' : $profile['drink_volume'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">タバコ</p>
          <p class="portlet-content__value">{{ (!$profile['smoking']) ? '未設定' : $profile['smoking'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">兄弟</p>
          <p class="portlet-content__value">{{ (!$profile['siblings']) ? '未設定' : $profile['siblings'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">同居人</p>
          <p class="portlet-content__value">{{ (!$profile['cohabitant']) ? '未設定' : $profile['cohabitant'] }}</p>
        </li>
      </ul>
    </div>
  </section>
  <!-- profile-word -->

  <div class="btn-l"><a href="{{ route('profile.edit') }}">修正</a></div>
</div>
@endsection

@section('web.script')
<script>
    $(function () {
      var popup_profile = window.sessionStorage.getItem('popup_profile');

      if (popup_profile) {
        $('#profile-popup').trigger('click');
        $('#profile-message h2').html(popup_profile);

        window.sessionStorage.removeItem('popup_profile');
      }
    })
  </script>
@endsection

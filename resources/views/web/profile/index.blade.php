@section('title', 'プロフィール')
@section('screen.id', 'gm1')

@extends('layouts.web')
@section('web.content')
<div class="cast-profile">
  <section class="profile-photo">
    <div class="profile-photo__top">
      @if ($profile['avatars'] && $profile['avatars'][0]['thumbnail'])
      <img class="init-image-radius" src="{{ $profile['avatars'][0]['thumbnail'] }}" alt="">
      @else
      <img class="init-image-radius" src="{{ asset('assets/web/images/ge1/user_icon.svg') }}" alt="">
      @endif
    </div>
    <div class="profile-photo__list">
      <ul>
        @foreach ($profile['avatars'] as $avatar)
          @if ($avatar['thumbnail'])
          <li class="css-img"><img src="{{ $avatar['thumbnail'] }}" alt=""></li>
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
      <p class="portlet-content__text">{{ $profile['intro'] or '未設定' }}</p>
    </div>
  </section>
  <!-- profile-word -->

  <section class="portlet">
    <div class="portlet-header">
      <h2 class="portlet-header__title">自己紹介</h2>
    </div>
    <div class="portlet-content">
      <p class="portlet-content__text">{{ $profile['description'] or '未設定' }}</p>
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
            case 0:
                $gender = '非公開';
                break;
            case 1:
                $gender = '男性';
                break;
            case 2:
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
            {{ ($profile['date_of_birth']) ? \Carbon\Carbon::parse($profile['date_of_birth'])->format('Y年m月d日') : '未設定' }}
          </p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">年齢</p>
          <p class="portlet-content__value">{{ $profile['age'] or '未設定' }}{{ (!$profile['age']) ? '' : '歳' }}</p>
        </li>

        <li class="portlet-content__item">
          <p class="portlet-content__text--list">身長</p>
          <p class="portlet-content__value"><span>{{ !($profile['height'] > 0) ? '未設定' : $profile['height'] . 'cm' }}</span></p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">年収</p>
          <p class="portlet-content__value"><span>{{ ($profile['salary']) ? $profile['salary'] : '未設定' }}</span></p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">体型</p>
          <p class="portlet-content__value">{{ $profile['body_type'] or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">居住地</p>
          <p class="portlet-content__value">{{ $profile['prefecture'] or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">出身地</p>
          <p class="portlet-content__value">{{ $profile['hometown'] or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">お仕事</p>
          <p class="portlet-content__value">{{ $profile['job'] or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">お酒</p>
          <p class="portlet-content__value">{{ $profile['drink_volume'] or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">タバコ</p>
          <p class="portlet-content__value">{{ $profile['smoking'] or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">兄弟</p>
          <p class="portlet-content__value">{{ $profile['siblings'] or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">同居人</p>
          <p class="portlet-content__value">{{ $profile['cohabitant'] or '未設定' }}</p>
        </li>
      </ul>
    </div>
  </section>
  <!-- profile-word -->

  <div class="btn-l"><a href="{{ route('profile.edit') }}">修正</a></div>
</div>
@endsection

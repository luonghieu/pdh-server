@section('title', 'プロフィール')
@section('screen.id', 'gm1')

@extends('layouts.web')
@section('web.content')
<div class="cast-profile">
  <section class="profile-photo">
    <div class="profile-photo__top"><img id="avatar" src="{{ $profile['avatars'][0]['path'] }}" alt=""></div>
    <div class="profile-photo__list">
      <ul>
        @foreach ($profile['avatars'] as $avatar)
        <li class="profile-photo__item"><img src="{{ $avatar['path'] }}" alt=""></li>
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
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">身長</p>
          <p class="portlet-content__value"><span>{{ $profile['height'] or '未設定' }}</span>cm</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">体型</p>
          <p class="portlet-content__value">{{ $profile['body_type'] or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">居住地</p>
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
</div>
@endsection

@section('title', 'キャスト詳細')
@section('screen.id', 'gf2')

@extends('layouts.web')
@section('web.content')
<div class="cast-call">
  <section class="cast-photo">
    <div class="slider cast-photo__show">
      @foreach ($cast['avatars'] as $avatar)
      <img src="{{ $avatar['path'] }}" alt="">
      @endforeach
    </div>
  </section>

  <div class="cast-set">
    <section class="cast-info">
      <ul class="cast-info__list">
        <li class="cast-info__item">●●{{ $cast['nickname'] or '未設定' }}●●</li>
        <li class="cast-info__item">{{ $cast['age'] or '未設定' }}歳</li>
        <li class="cast-info__item--level">{{ $cast['class'] or '未設定' }}</li>
      </ul>
      <p class="cast-info__signature">保育士 | ばっち飯ですのでご一緒どうでしょうか？</p>
      <p class="cast-info__price">30分あたりの料金<span>{{ $cast['cost'] ? number_format($cast['cost']) : '未設定' }}P</span></p>
    </section>

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">自己紹介</h2>
      </div>
      <div class="portlet-content">
        <p class="portlet-content__text">{{ $cast['intro'] or '未設定' }}</p>
      </div>
    </section>

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">基本情報</h2>
      </div>
      <div class="portlet-content">
        <ul class="portlet-content__list">
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">身長</p>
            <p class="portlet-content__value"><span>{{ $cast['height'] or '未設定' }}</span>cm</p>
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">体型</p>
            <p class="portlet-content__value">{{ $cast['body_type'] or '未設定' }}</p>
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">居住地</p>
            <p class="portlet-content__value">{{ $cast['prefecture'] or '未設定' }}</p>
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">出身地</p>
            <p class="portlet-content__value">{{ $cast['hometown'] or '未設定' }}</p>
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お仕事</p>
            <p class="portlet-content__value">{{ $cast['job'] or '未設定' }}</p>
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お酒</p>
            <p class="portlet-content__value">{{ $cast['drink_volume'] or '未設定' }}</p>
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">同居人</p>
            <p class="portlet-content__value">{{ $cast['cohabitant'] or '未設定' }}</p>
          </li>
        </ul>
      </div>
    </section>
    <!-- profile-word -->
  </div>
</div>
<div class="cast-call-btn">
  <button><img src="{{ asset('assets/web/images/common/msg2.svg') }}"></button>
  <div class="btn-l"><a href="{{ route('guest.orders.nominate',['id' => $cast['id'] ]) }}">指名予約する</a></div>
</div>
@endsection
@section('web.script')
<script>
localStorage.clear();
</script>
@stop

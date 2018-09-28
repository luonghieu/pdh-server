@section('title', 'キャスト詳細')
@section('screen.id', 'gf2')

@extends('layouts.web')
@section('web.content')
<div class="cast-call">
  <section class="cast-photo">
    <div class="slider cast-photo__show">
      @foreach ($cast['avatars'] as $avatar)
        @if ($avatar['thumbnail'])
        <img src="{{ $avatar['thumbnail'] }}" alt="">
        @endif
      @endforeach
    </div>
  </section>

  <div class="cast-set">
    <section class="cast-info">
      <ul class="cast-info__list">
        <li class="cast-info__item text-ellipsis text-nickname">{{ $cast['nickname'] }}</li>
        <li class="cast-info__item"><b>{{ (!$cast['age']) ? '' : ($cast['age'] . "歳") }}</b></li>
        <li class="cast-info__item--level">{{ (!$cast['class']) ? '未設定' : $cast['class'] }}</li>
      </ul>
      <p class="cast-info__signature">{{ $cast['job'] }} | {{ $cast['intro'] }}</p>
      <p class="cast-info__price">30分あたりの料金<span>{{ $cast['cost'] ? number_format($cast['cost']) : '未設定' }}P</span></p>
    </section>

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">自己紹介</h2>
      </div>
      <div class="portlet-content">
        <p class="portlet-content__text">{{ (!$cast['intro']) ? '' : $cast['intro'] }}</p>
      </div>
    </section>

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">基本情報</h2>
      </div>
      @php
        switch ($cast['height']) {
            case '0':
                $height = '非公開';
                break;

            default:
                $height = $cast['height'] . 'cm';
                break;
        }
      @endphp
      <div class="portlet-content">
        <ul class="portlet-content__list">
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">身長</p>

            <p class="portlet-content__value"><span>{{ $height or '未設定' }}</span></p>
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">体型</p>
            @if (!$cast['body_type'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['body_type'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">居住地</p>
            @if (!$cast['prefecture'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['prefecture'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">出身地</p>
            @if (!$cast['hometown'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['hometown'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お仕事</p>
            @if (!$cast['job'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['job'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お酒</p>
            @if (!$cast['drink_volume'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['drink_volume'] }}</p>
            @endif
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">同居人</p>
            @if (!$cast['cohabitant'])
            <p class="portlet-content__text--list">未設定</p>
            @else
            <p class="portlet-content__value">{{ $cast['cohabitant'] }}</p>
            @endif
          </li>
        </ul>
      </div>
    </section>
    <!-- profile-word -->
  </div>
</div>
<div class="cast-call-btn">
  <a href="{{ route('message.index') }}"><img src="{{ asset('assets/web/images/common/msg2.svg') }}"></a>
  <div class="btn-l"><a href="{{ route('guest.orders.nominate',['id' => $cast['id'] ]) }}">指名予約する</a></div>
</div>
@endsection

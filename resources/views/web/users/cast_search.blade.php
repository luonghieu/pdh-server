@section('title', '絞込み検索')
@section('screen.id', 'gf3')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ mix('assets/web/css/gf_3.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/jRange/jquery.range.css') }}">
@endsection
@section('web.content')
  <div class="page-header">
    <h1 class="text-bold">絞込み検索</h1>
  </div>
  @php 
    $url = route('cast.list_casts') . '?schedule=' . request()->schedule . '&prefecture_id=' . request()->prefecture_id . '&class_id=' . request()->class_id . '&point=' . request()->point;
  @endphp
  <form action="{{ $url }}" method="get">
    <input type="hidden" name="schedule" value="{{ request()->schedule }}" />
    <div class="cast-search">
      <section class="search">
        <div class="search-header">
          <h2 class="search-header__title">絞り込み条件</h2>
        </div>
        <div class="search-content">
          <ul class="search-content__list">
            <li class="search-content__item">
              <p class="search-content__text--list">地域</p>
              <div class="selectbox">
                <select class="init-select" name="prefecture_id" id="prefecture-id" dir="rtl">
                  <option value="">選択してください</option>
                  @foreach ($prefectures as $prefecture )
                    <option value="{{ $prefecture['id'] }}" {{ $prefecture['id'] == request()->prefecture_id ? 'selected' : '' }}>{{ $prefecture['name'] }}</option>
                  @endforeach
                </select>
                <i class="init-ml-2"></i>
              </div>
            </li>
            <li class="search-content__item">
              <p class="search-content__text--list">キャストクラス</p>
              <div class="selectbox">
                <select class="init-select" name="class_id" id="class-id" dir="rtl">
                  <option value="">選択してください</option>
                  @foreach ($castClasses as $castClass )
                  <option value="{{ $castClass['id'] }}" {{ $castClass['id'] == request()->class_id ? 'selected' : '' }}>{{ $castClass['name'] }}</option>
                  @endforeach
                </select>
                <i class="init-ml-2"></i>
              </div>
            </li>
          </ul>
        </div>
      </section>

      <section class="search">
        <div class="search-header">
          <h2 class="search-header__title">30分あたりのポイント</h2>
        </div>
        <div class="search-content">
          @php
            $minPoint = !request()->point ? '0' : explode(',', request()->point)[0];
            $maxPoint = !request()->point ? '15000' : explode(',', request()->point)[1];
          @endphp
          <input type="hidden" class="range-slider" name="point" value="{{ $minPoint }},{{ $maxPoint }}" />
        </div>
      </section>
    </div>

    <div class="search-bottom">
      <div class="form_footer">
        <button type="submit" class="init-color">この条件で検索する</button>
      </div>
    </div>
  </form>
@endsection
@section('web.extra_js')
  <script src="{{ asset('assets/web/js/jRange/jquery.range.js') }}"></script>
  <script src="{{ mix('assets/web/js/gf-3.min.js') }}"></script>
  <script>
    $(function () {
      if ($('#prefecture-id').val()) {
        $('#prefecture-id').css('color', '#222222');
      }

      if ($('#class-id').val()) {
        $('#class-id').css('color', '#222222');
      }
    });
  </script>
@endsection

@section('title', '絞込み検索')
@section('screen.id', 'gf3')
@extends('layouts.web')
@section('web.extra_css')
  <link rel="stylesheet" href="{{ asset('assets/web/css/gf_3.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/web/css/plugin/jRange/jquery.range.css') }}">
@endsection
@section('web.content')
  <div class="page-header">
    <a href="{{ route('cast.list_casts') }}" class="prev"><i><img src="{{ asset('assets/web/images/common/prev.svg') }}" alt=""></i></a>
    <h1 class="text-bold">絞込み検索</h1>
  </div>

  <form action="{{ route('cast.list_casts') }}" method="get">
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
                <select class="init-select" name="prefecture_id" dir="rtl">
                  <option value="" selected>選択してください</option>
                  @foreach ($prefectures as $prefecture )
                    <option value="{{ $prefecture['id'] }}">{{ $prefecture['name'] }}</option>
                  @endforeach
                </select>
                <i class="init-ml-2"></i>
              </div>
            </li>
            <li class="search-content__item">
              <p class="search-content__text--list">キャストクラス</p>
              <div class="selectbox">
                <select class="init-select" name="class_id" dir="rtl">
                  <option value="" selected>選択してください</option>
                  @foreach ($castClasses as $castClass )
                  <option value="{{ $castClass['id'] }}">{{ $castClass['name'] }}</option>
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
          <input type="hidden" class="range-slider" name="point" value="0,15000" />
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
  <script src="{{ asset('assets/web/js/gf-3.js') }}"></script>
@endsection

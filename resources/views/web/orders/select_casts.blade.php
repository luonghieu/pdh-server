@section('title', 'キャスト予約')
@section('screen.class', 'ge2-3')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ asset('assets/web/css/ge_2_3.css') }}">
@endsection
@section('web.content')
  <h2>指名したいキャストがいる場合は選択してください</h2>
  <p class="message">※ご希望に<span>添えない可能性</span>もございます。<br/>※指名料が1人あたり15分毎に500Pが別途発生します。</p>
  <form action="{{ route('guest.orders.post_step3') }}" method="POST" class="create-call-form" id="" name="select_casts_form">
    {{ csrf_field() }}
    <div class="">
      <div class="form-grpup"><!-- フォーム内容 -->
        @if(count($casts['data']))
        @foreach($casts['data'] as $cast)
        <div class="cast_block" id="cb-casts">
          <input type="checkbox" name="casts[]" value="{{ $cast['id'] }}" {{ (isset($currentCasts) && in_array($cast['id'], $currentCasts) ) ? 'checked="checked"' : '' }} id="{{ $cast['id'] }}" class="select-casts">
          <div class="icon">
            <p><img src="{{ $cast['avatars'][0]['thumbnail'] }}" alt=""></p>
          </div>
          <span class="sp-name-cast">{{ $cast['nickname'] }}</span>
          <label for="{{ $cast['id'] }}">指名する</label>
        </div>
        @endforeach
        @endif
      </div>
      @if(isset($castNumbers))
      <input type="hidden" value="{{ $castNumbers }}" class="cast-numbers">
      @endif
    </div>
    <button type="submit" class="form_footer ct-button" id="sb-select-casts">次に進む(3/4)</button>
  </form>
@endsection

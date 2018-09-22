@section('title', 'Cheers')
@extends('layouts.web')
@section('web.extra')
  <div class="modal_wrap">
    <input id="trigger" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn1">
      <div class="text-box">
        <h2>タイトルが入りますタイトルが</h2>
        <p>ここにテキストが入りますここにテキストが入りますここにテキストが入ります</p>
      </div>
        <label for="trigger" class="close_button">キャンセルする</label>
      </div>
    </div>
  </div>
@endsection
@section('web.content')
  <section class="button-box">
    <label for="trigger" class="open_button button-settlement">モーダル１(ボタン1つ)</label>
  </section>
  <div class="top-header">
    <div class="user-data">
      <div class="user-icon">
        <img src="{{ asset('assets/web/images/ge1/user_icon.svg') }}" alt="">
      </div>
      <a href="#" class="edit-button">
        <img src="{{ asset('assets/web/images/ge1/pencil.svg') }}" alt="">
      </a>
    </div>
    <span class="user-name">Kotaro</span>
  </div>
  <a href="#" class="cast-call">今すぐキャストを呼ぶ<span>最短20分で合流!</span></a>
  @if($token)
    <script>
        window.localStorage.setItem('access_token', '{{ $token }}');
    </script>
  @endif
@endsection

@section('title', 'キャスト予約')
@section('screen.class', 'ge2-1-a-3')
@extends('layouts.web')
<div id="page">
@section('web.content')
@if(!Auth::check())
<a href="{{ route('auth.line') }}">
  <img src="{{ asset('images/btn_login_base.png') }}" alt="">
</a>
@else
  <p class="cancel_title">予約確定後のキャンセルはキャンセル規定に<br class="sp-none">
    基づいたキャンセル料が発生致します</p>
  <p class="cancel_txt">予約確定後は、日時変更もキャンセル対象です。<br>
    日時変更をする場合はキャンセル手続き後、新たに予約を
    行ってください。</p>

  <table>
    <tbody>
      <tr>
        <td>施述予定日の8日前まで</td>
        <td class="right">キャンセル料なし</td>
      </tr>
      <tr>
        <td>施述予定日の7日前</td>
        <td class="right">施術料の30％</td>
      </tr>
      <tr>
        <td>施述予定日の1日前</td>
        <td class="right">施術料の50％</td>
      </tr>
      <tr>
        <td>施述予定日の当日</td>
        <td class="right">施術料の100％</td>
      </tr>
    </tbody>
  </table>
@endif
@endsection

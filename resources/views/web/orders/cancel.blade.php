@section('title', 'キャスト予約')
@section('screen.class', 'ge2-1-a-3')
@extends('layouts.web')
@section('web.content')
  <p class="cancel_title">予約確定後のキャンセルはキャンセル規定に<br class="sp-none">
    基づいたキャンセル料が発生致します</p>
  <p class="cancel_txt">予約確定後は、日時変更もキャンセル対象です。<br>
    日時変更をする場合はキャンセル手続き後、新たに予約を
    行ってください。</p>

  <table>
    <tbody>
      <tr>
        <td>合流予定日の8日前まで</td>
        <td class="right">キャンセル料なし</td>
      </tr>
      <tr>
        <td>合流予定日の7日前</td>
        <td class="right">予定合計ポイントの30％</td>
      </tr>
      <tr>
        <td>合流予定日の1日前</td>
        <td class="right">予定合計ポイントの50％</td>
      </tr>
      <tr>
        <td>合流予定日の当日</td>
        <td class="right">予定合計ポイントの100％</td>
      </tr>
    </tbody>
  </table>
@endsection

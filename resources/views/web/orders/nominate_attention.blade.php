@section('title', '予約確定前注意出項')
@section('screen.class', '')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ asset('assets/web/css/ge_2_4.css') }}">
@endsection
@section('web.content')
  <div class="ge2-4-block">
    <h2>キャンセル料金</h2>
    <p>予約確定後のキャンセルは、100％有償のキャンセルとなります。<br />詳しくは<a href="{{ route('guest.orders.cancel') }}">キャンセルポリシー</a>をご確認ください。</p>
  </div>
  <div class="ge2-4-block">
    <h2>深夜料金</h2>
    <p>キャストとの合流時間に0〜4時が含まれる場合、タクシー代の代としてキャスト1名あたり4000pointの深夜料金が発生します。</p>
  </div>
  <div class="ge2-4-block">
    <h2>延長料金</h2>
    <p>キャストとの合流後、終了時刻が過ぎた場合は自動的に延長となり延長料金が発生します。延長料金はキャストクラスによって異なります。</p>
    <ul>
      <li><span>ダイヤモンド</span><span>8750P/15分</span></li>
      <li><span>プラチナ</span><span>3500P/15分</span></li>
      <li><span>ブロンズ</span><span>1750P/15分</span></li>
    </ul>
  </div>
  <div class="ge2-4-block">
    <h2>コール内指名料金</h2>
    <p>コール予約内でのキャスト指名は、キャスト1人あたり500P/15分が発生します。</p>
  </div>
  <div class="ge2-4-block">
    <h2>オートチャージ</h2>
    <p>ご利用後の決済の際、ポイントの不足分はご登録いただいたクレジットカードから、自動的に3000P単位でオートチャージされます。</p>
  </div>
@endsection

@section('title', '予定確定前注意事項')
@section('screen.class', '')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/ge_2_4.min.css') }}">
@endsection
@section('web.content')
  <div class="ge2-4-block">
    <h2>最低人数について</h2>
    <p>男性の人数に対してキャストの人数が少ないのはNGです。<br />※違反が発覚した場合は最低人数分の料金をお支払いただきます。</p>
  </div>
  <div class="ge2-4-block">
    <h2>キャンセル料金</h2>
    <p>予約確定後の当日キャンセルは、100％のキャンセルとなります。<br />詳しくは<a href="{{ route('guest.orders.cancel') }}">キャンセルポリシー</a>をご確認ください。</p>
  </div>
  <div class="ge2-4-block">
    <h2>深夜料金</h2>
    <p>キャストとの合流時間に0〜4時が含まれる場合、タクシー代としてキャスト1名あたり4000Pの深夜料金が発生します。</p>
  </div>
  <div class="ge2-4-block">
    <h2>延長料金</h2>
    <p>キャストとの合流後、終了予定時刻を過ぎた場合は自動的に延長となり延長料金が15分単位で発生します。延長料金は下記のとおりです。</p>
  </div>
  <div class="ge2-4-block">
    <p>コール予約の場合</p>
    <ul>
      <li><span>ダイヤモンド</span><span>8750P/15分</span></li>
      <li><span>プラチナ</span><span>3500P/15分</span></li>
      <li><span>ブロンズ</span><span>1750P/15分</span></li>
    </ul>
  </div>
  <div class="ge2-4-block">
    <p class="mb-text">指名予約の場合</p>
    <p class="mb-text">指名キャストの[30分あたりのポイント]の1.4倍となります。</p>
    <div class="text-ge-2-4">
      <p>(例) 3,000P/30分のキャストを1時間、指名予約した場合</p>
      <p class="ml-p">最初の1時間 = 6,000P</p>
      <p class="ml-p">延長15分 = 2,100P(1,500P×1.4)となります。</p>
    </div>
  </div>
  <div class="ge2-4-block">
    <h2>コール内指名料金</h2>
    <p>コール予約内でのキャスト指名は、キャスト1人あたり500P/15分が発生します。</p>
  </div>
  <div class="ge2-4-block">
    <h2>オートチャージ</h2>
    <p>ご利用後、評価と決済確定作業を行っていただいておりますが、決済確定作業を行われなかった場合は、ポイントの不足分をご登録いただいたクレジットカードから、自動決済させていただきます。(1P = 1.1円)</p>
  </div>
    <button type="button" class="form_footer ct-button">
      <a href="{{ route('guest.orders.confirm') }}">次に進む(3/3)</a>
    </button>

@endsection
<script>
  if(!localStorage.getItem("order_call")){
    window.location.href = '/mypage';
  }
</script>

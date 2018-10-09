@section('title', '予定確定前注意事項')
@section('screen.class', '')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ asset('assets/web/css/ge_2_4.css') }}">
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
  <form action="{{ route('guest.orders.post_confirm') }}" method="POST" class="create-call-form" id="" name="attention_form">
    {{ csrf_field() }}
    <button type="submit" class="form_footer ct-button">次に進む(4/4)</button>
  </form>
@endsection

@section('web.script')
<script>
  if(localStorage.getItem("select_cast")){
    var backLink = localStorage.getItem("select_cast");
    if (window.history && window.history.pushState) {
      window.history.pushState(null, null, null);

      window.onpopstate = function(event) {
        setTimeout(function() {
          document.location.href = backLink;
        },250);
       localStorage.removeItem("select_cast");
      }
    }
  }
</script>
@endsection

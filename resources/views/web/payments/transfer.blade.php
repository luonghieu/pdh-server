@section('title', 'プロフィール編集')
@section('screen.id', 'gm1')
@section('screen.class', 'gm1-edit')

@extends('layouts.web')
@section('web.extra')

@endsection
@section('web.content')
  @php
    $backUrl = \URL::previous();
  @endphp
  @if(!preg_match('/select_payment_methods/', $backUrl))
    <p class="transfer_title">ポイントが不足しております。 <br> 不足分のポイントをご購入ください。</p>
  @endif
<div id="detail-point-order">
  <section class="show-point">
    <div class="details-list__line"></div>
    <div class="details-list__header">
    </div>
    <div class="grade-list transfer-order">
      <div class="transfer-left">
        <p>購入ポイント</p>
      </div>
      <div class="transfer-right">
        <p>{{ number_format(request()->get('point')).' P' }}</p>
      </div>
    </div>
  </section>
  <section class="show-point">
    <div class="details-list__line below"><p></p></div>
    <div class="details-list__header">
    </div>
    <div class="grade-list transfer-order">
      <div class="transfer-amount-title">
        <p>振込金額</p>
      </div>
      <div class="transfer-right transfer-amount-point">
        <p>{{ number_format(request()->get('point') * 1.1).' 円' }}</p>
      </div>
    </div>
  </section>
</div>
<p class="transfer_title">下記の口座にお振込みください。</p>
  <div class="cast-profile transfer-content">
    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">振込先銀行口座</h2>
      </div>
      <div class="portlet-content">
        <ul class="portlet-content__list">
          <li class="portlet-content__item">
            <p class="transfer-left">銀行名</p>
            <label class="time-input">
              <p class="transfer-right">東京三協信用金庫</p>
            </label>
          </li>
          <li class="portlet-content__item">
            <p class="transfer-left">支店名</p>
            <label class="time-input">
              <p class="transfer-right">新宿支店（012)</p>
            </label>
          </li>
          <li class="portlet-content__item">
            <p class="transfer-left">預金科目</p>
            <label class="time-input">
              <p class="transfer-right">普通</p>
            </label>
          </li>
          <li class="portlet-content__item">
            <p class="transfer-left">口座番号</p>
            <label class="time-input">
              <p class="transfer-right">1023474</p>
            </label>
          </li>
          <li class="portlet-content__item">
            <p class="transfer-left">口座名義</p>
            <label class="time-input">
              <p class="transfer-right">リスティル（カ</p>
            </label>
          </li>
        </ul>
        <div class="transfer-note">
        ※振込名義は、申告された名義をご入力ください。 <br>
        ※申告された名義を忘れの場合は、入金後に入力された振込名義を運営者チャットにてご連絡ください。
        </div>
      </div>
    </section>
    <!-- profile-word -->
  </div>
    <div class="cast-profile transfer-content">
    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">銀行振込の際の注意事項</h2>
      </div>
      <div class="portlet-content " id="transfer-comment">
        <p>
          ※15時以前に弊社指定の銀行口座に入金されたものを同日16時頃に着金確認します。その後1営業日以内にポイント付与・決済処理を行います。<br>
          お時間に余裕を持ってご振込をお願いします。
        </p> <br>
        <p>
          ※年末年始、GWなどの大型連休のポイント付与につきましては、別途ご案内させていただきます。
        </p> <br>
        <p>
          ※振込名義は、Cheers運営局に申告された名義（カタカナ）のみをそのままご入力ください。
        </p><br>
        <p>
          ※申告された名義以外で入金を行った場合は、弊社にて入金確認ができない可能性がありますので、お手数ですが、入金後弊社までご連絡くださいますようお願いいたします。
        </p>
      </div>

      <div class="btn-m top-page">
        <a href="{{ route('web.index') }}">TOPに戻る</a>
      </div>

    </section>

    <!-- profile-word -->
  </div>
@endsection
@section('web.extra_js')
  <script>
      $(document).ready(function () {
          localStorage.removeItem("buy_point");
      })
  </script>
@endsection

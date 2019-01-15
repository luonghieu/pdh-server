<!DOCTYPE html>
<html>
<head>
<title>Cheers</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ mix('assets/webview/css/style.min.css') }}"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link href="{{ mix('assets/web/css/web.css') }}" rel="stylesheet">
</head>
<body>
  <header class="border-bottom">
      <div class="btn-back header-item">
        <a href="cheers://back"><img src="/assets/webview/images/back.png" alt=""></a>
      </div>
      <div class="title-main header-item">
        <span>クレジットカード登録</span>
      </div>
      <div class="btn-register header-item">
        <a {{-- id="btn-create" --}} class="btn-stop-create-card">登録</a>
      </div>
  </header>
  <div class="image-main">
    <img src="/assets/web/images/card/allCard.png" alt="">
  </div>
  <div class="notify">
    <span></span>
  </div>
  <div class="content">
    <form action="{{ route('webview.add_card') }}" id="payment-form" method="POST">
      {{ csrf_field() }}
    <div class="sub-title">
      <p>カード情報</p>
    </div>
    <div class="card-number border-bottom">
      <span class="left">カード番号</span>
      <div class="right number">
        <span id="error">カード番号を正しく入力してください</span>
        <input type="tel" pattern="[0-9]*" name="number_card" id="number-card" onkeyup="creditValidate()" placeholder="0000 0000 0000 0000">
      </div>
    </div>
    <div class="clear"></div>
    <div class="expiration-date border-bottom">
      <span class="left">有効期限</span>
      <div class="date-select right">
        <select name="month" id="month">
          @for ($i = 1; $i < 13; $i++)
          <option value="{{ $i }}">{{ $i }}月</option>
          @endfor
        </select>
          @php
            $currenYear = \Carbon\Carbon::now()->format('Y');
          @endphp
        <select name="year" id="year">
          @for ($i = $currenYear; $i <= $currenYear+20; $i++)
          <option value="{{ $i }}">{{ $i }}年</option>
          @endfor
        </select>
      </div>
    </div>
    <div class="sub-title">
      <p>セキュリティコード</p>
    </div>
    <div class="security-code border-bottom">
      <img src="/assets/webview/images/ic_card_cvv.png" alt="" class="left">
      <input type="tel" pattern="[0-9]*" placeholder="3桁または4桁の数字" class="right" name="card_cvv" onkeyup="addColor()" onkeydown="return numberCvvLength(event)" id="card-cvv">
    </div>
    </form>
  </div>
  <div class="modal_wrap">
    <input id="popup-stop-create-card" type="checkbox">
    <div class="modal_overlay">
        <div class="modal_content modal_content-btn1">
            <div class="text-box">
                <p>*現在クレジットカード情報はシステムメンテナンスのため、編集することができません。</p>
                <p>※只今、クレジットカードの登録なしで予約をすることができます。</p>
                <p>※ご不明な点がある場合は、運営までお問い合わせください。</p>
            </div>
            <label for="popup-stop-create-card" class="close_button">OK</label>
        </div>
    </div>
  </div>
  <script src="{{ mix('assets/webview/js/script.min.js') }}"></script>
  <script src="{{ mix('assets/webview/js/create_card.min.js') }}"></script>
  <script src="/assets/webview/js/lib/payment.js"></script>
  <script>
    $(document).ready(function() {
      $('.btn-stop-create-card').click(function(event) {
        $('#popup-stop-create-card').trigger('click');
      });
    });
  </script>
</body>
</html>

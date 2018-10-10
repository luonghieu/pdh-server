<!DOCTYPE html>
<html>
<head>
<title>Cheers</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="/assets/webview/css/style.css"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
        <a id="btn-create">登録</a>
      </div>
  </header>
  <div class="image-main">
    <img src="/assets/webview/images/ic_credit_cards@2x.png" alt="">
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
        <input type="text" pattern="[0-9]*" name="number_card" id="number-card" onkeyup="creditValidate()" onkeydown="return numberCardLength(event)">
        <span id="number-card-display">0000 0000 0000 0000</span>
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
      <input type="text" pattern="[0-9]*" placeholder="3桁または4桁の数字" class="right" name="card_cvv" onkeyup="addColor()" onkeydown="return numberCvvLength(event)" id="card-cvv">
    </div>
    </form>
  </div>
  <script src="/assets/webview/js/script.js"></script>
  <script src="/assets/webview/js/create_card.js"></script>
</body>
</html>
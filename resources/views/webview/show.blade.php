<!DOCTYPE html>
<html>
<head>
<title>Cheers</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="/assets/webview/css/style.css"/>
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
        <a href="{{ route('webview.edit', ['card' => $card->id]) }}" class="btn-redirect-edit">編集</a>
      </div>
  </header>
  <div class="image-main">
    <img src="/assets/webview/images/ic_credit_cards@2x.png" alt="">
  </div>
  @if(Session::has('err'))
  <div class="error">
    <span>{{ Session::get('err') }}</span>
  </div>
  @endif
  <div class="content">
    <div class="sub-title">
      <p>カード情報</p>
    </div>
    <div class="card-number border-bottom">
      <span class="left">カード番号</span>
      <div class="right number">
        <span id="error">カード番号を正しく入力してください</span>
        <input type="number" name="number_card" id="number-card" readonly>
        <span id="number-card-display" class="color-show-page">下4桁{{ $card->last4 }}</span>
      </div>
    </div>
    <div class="clear"></div>
    <div class="expiration-date border-bottom">
      <span class="left">有効期限</span>
      <div class="date-select right">
        <select name="month" class="color-show-page" disabled>
          <option  value="{{ $card->exp_month }}">{{ $card->exp_month }}月</option>
        </select>
        <select name="year" class="color-show-page" disabled>
          <option value="{{ $card->exp_year }}">{{ $card->exp_year }}年</option>
        </select>
      </div>
    </div>
    <div class="sub-title">
      <p>セキュリティコード</p>
    </div>
    <div class="security-code border-bottom">
      <img src="/assets/webview/images/ic_card_cvv.png" alt="" class="left">
      <input type="password" placeholder="3桁または4桁の数字" class="right color-show-page" name="card_cvv" value="0000" id="card-cvv" readonly>
    </div>
  </div>
</body>
</html>

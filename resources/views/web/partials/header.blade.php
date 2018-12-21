<header class="header">
  <div class="h-wrapper">
    <div class="h-logo">
      <a href="{{ route('web.index') }}"><img src="{{ asset('assets/web/images/common/logo.svg') }}" alt="cheers"></a>
    </div>
    @if (Auth::check() && Auth::user()->status)
      <a href="#menu" class="hamburger"><span></span></a>
    @endif
  </div>
</header>

<nav id="menu">
  <ul>
    @if (Auth::check())
      @if (Auth::user()->is_guest)
      <li><a href="{{ route('web.index') }}"><i><img src="{{ asset('assets/web/images/common/man.svg') }}"></i>マイページ</a></li>
      <li><a href="{{ route('guest.orders.call') }}"><i><img src="{{ asset('assets/web/images/common/woman.svg') }}"></i>今すぐキャストを呼ぶ</a></li>
      <li><a href="{{ route('cast.list_casts') }}"><i><img src="{{ asset('assets/web/images/common/seach2.svg') }}"></i>キャストをさがす</a></li>
      <li><a href="{{ route('message.index') }}"><i><img src="{{ asset('assets/web/images/common/msg.svg') }}"></i>メッセージ</a></li>
      <li><a href="{{ route('guest.orders.reserve') }}"><i><img src="{{ asset('assets/web/images/common/glass.svg') }}"></i>予約一覧</a></li>
      <li><a href="{{ route('purchase.index') }}"><i><img src="{{ asset('assets/web/images/common/point.svg') }}"></i>POINT購入</a></li>
      <li><a href="{{ route('points.history') }}"><i><img src="{{ asset('assets/web/images/common/date.svg') }}"></i>予約履歴</a></li>
      <li><a href="{{ route('credit_card.index') }}"><i><img src="{{ asset('assets/web/images/common/card.svg') }}"></i>クレジットカード情報</a></li>
      <li><span><i><img src="{{ asset('assets/web/images/common/help.svg') }}"></i>ヘルプ</span>
        <ul>
          <li><a href="{{ url('/service/guest_qa') }}">よくある質問</a></li>
          <li><a href="{{ url('/service/law') }}">利用規約</a></li>
          <li><a href="{{ url('/service/policy') }}">プライバシーポリシー</a></li>
          <li><a href="{{ url('/service/sct_law') }}">特定商取引法に基づく表記について</a></li>
          <li><a href="{{ url('/service/guest_ht') }}">ご利用方法</a></li>
          <li><a href="{{ url('/service/contact') }}">お問い合わせ</a></li>
          <li><a href="{{ route('web.logout') }}">ログアウト</a></li>
        </ul>
      </li>
      @endif
      @if (Auth::user()->is_cast)
      <li><a href="{{ route('web.index') }}"><i><img src="{{ asset('assets/web/images/common/woman.svg') }}"></i>マイページ</a></li>
      <li><a href="{{ route('cast_mypage.bank_account.index') }}"><i><img src="{{ asset('assets/web/images/common/icon-cash-bk.png') }}"></i>振込口座</a></li>
      <li><a href="{{ route('cast.transfer_history') }}"><i><img src="{{ asset('assets/web/images/common/icon-point-bk.png') }}"></i>振込履歴</a></li>
      <li><a href="<?php echo env('APP_URL') . '/service/cast_qa' ?>"><i><img src="{{ asset('assets/web/images/common/icon-question-wt-bk.png') }}"></i>よくある質問</a></li>
      <li class="logout-web"><a href="javascript:void(0)"><i><img src="{{ asset('assets/web/images/common/logout.jpeg') }}"></i>ログアウト</a></a></li>
      @endif
    @else
      <li><span><i><img src="{{ asset('assets/web/images/common/help.svg') }}"></i>ヘルプ</span>
        <ul>
          <li><a href="{{ url('/service/guest_qa') }}">よくある質問</a></li>
          <li><a href="{{ url('/service/law') }}">利用規約</a></li>
          <li><a href="{{ url('/service/policy') }}">プライバシーポリシー</a></li>
          <li><a href="{{ url('/service/sct_law') }}">特定商取引法に基づく表記について</a></li>
          <li><a href="{{ url('/service/guest_ht') }}">ご利用方法</a></li>
          <li><a href="{{ url('/service/contact') }}">お問い合わせ</a></li>
        </ul>
      </li>
    @endif
  </ul>
</nav>
<div class="modal_wrap">
  <input id="confirm-logout" type="checkbox">
  <div class="modal_overlay">
    <label for="trigger2" class="modal_trigger"></label>
    <div class="modal_content modal_content-btn2">
      <div class="text-box">
        <h2>ログアウトしますか？</h2>
      </div>
      <div class="close_button-box">
        <div class="close_button-block">
          <label for="confirm-logout" class="close_button  left">キャンセル</label>
        </div>
        <div class="close_button-block">
          <a href="{{ route('web.logout') }}"><label class="close_button right">ログアウトする</label></a>
        </div>
      </div>
    </div>
  </div>
</div>

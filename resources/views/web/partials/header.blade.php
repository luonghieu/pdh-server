<header class="header">
  <div class="h-wrapper">
    <div class="h-logo">
      <a href="{{ route('web.index') }}"><img src="{{ asset('assets/web/images/common/logo.svg') }}" alt="cheers"></a>
    </div>
    @if (Auth::check())
      @if(isset(Auth::user()->phone))
      <a href="#menu" class="hamburger"><span></span></a>
      @endif
    @endif
  </div>
</header>
<nav id="menu">
  <ul>
    @if (Auth::check())
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

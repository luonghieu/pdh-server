<header class="header">
  <div class="h-wrapper">
    <div class="h-logo">
      <a href="#"><img src="{{ asset('assets/web/images/common/logo.svg') }}" alt="cheers"></a>
    </div>
    <a href="#menu" class="hamburger"><span></span></a>
  </div>
</header>
<nav id="menu">
  <ul>
    @if (Auth::check())
      <li><a href="#"><i><img src="{{ asset('assets/web/images/common/man.svg') }}"></i>マイページ</a></li>
      <li><a href="#"><i><img src="{{ asset('assets/web/images/common/woman.svg') }}"></i>今すぐキャストを呼ぶ</a></li>
      <li><a href="{{ route('message.index') }}"><i><img src="{{ asset('assets/web/images/common/msg.svg') }}"></i>メッセージ</a></li>
      <li><a href="#"><i><img src="{{ asset('assets/web/images/common/glass.svg') }}"></i>予約一覧</a></li>
      <li><a href="{{ route('purchase.index') }}"><i><img src="{{ asset('assets/web/images/common/point.svg') }}"></i>POINT購入</a></li>
      <li><a href="#"><i><img src="{{ asset('assets/web/images/common/date.svg') }}"></i>予約履歴</a></li>
      <li><a href="#"><i><img src="{{ asset('assets/web/images/common/card.svg') }}"></i>クレジットカード情報</a></li>
      <li><span><i><img src="{{ asset('assets/web/images/common/help.svg') }}"></i>ヘルプ</span>
        <ul>
          <li><a href="{{ url('/service/quest_qa') }}">よくある質問</a></li>
          <li><a href="{{ url('/service/terms') }}">利用規約</a></li>
          <li><a href="{{ url('/service/privacy_policy') }}">プライバシーポリシー</a></li>
          <li><a href="{{ url('/service/law') }}">特定商取引法に基づく表記について</a></li>
          <li><a href="{{ url('/service/howto') }}">ご利用方法</a></li>
          <li><a href="{{ url('/service/inquiry') }}">お問い合わせ</a></li>
          <li><a href="{{ route('web.logout') }}">ログアウト</a></li>
        </ul>
      </li>
      @else
      <li><span><i><img src="{{ asset('assets/web/images/common/help.svg') }}"></i>ヘルプ</span>
        <ul>
          <li><a href="{{ url('/service/quest_qa') }}">よくある質問</a></li>
          <li><a href="{{ url('/service/terms') }}">利用規約</a></li>
          <li><a href="{{ url('/service/privacy_policy') }}">プライバシーポリシー</a></li>
          <li><a href="{{ url('/service/law') }}">特定商取引法に基づく表記について</a></li>
          <li><a href="{{ url('/service/howto') }}">ご利用方法</a></li>
          <li><a href="{{ url('/service/inquiry') }}">お問い合わせ</a></li>
        </ul>
      </li>
    @endif
  </ul>
</nav>

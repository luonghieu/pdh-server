@section('title', 'Cheers')
@extends('layouts.web')
@section('web.content')
@if(!Auth::check())
@section('controller.id', 'login-controller')
@section('screen.id', 'login')
@section('web.extra')
  @if ($error = Session::pull('error'))
    @alert(['triggerId' => 'login-error'])
    @slot('content')
      {{ $error }}
    @endslot
    @endalert
    <script>
        document.getElementById('login-error').click();
    </script>
  @endif
@endsection
  <div class="wrapper">
    <div class="wrapper-inner">
      <div class="feature-title">
        <img src="{{ asset('assets/web/images/line/feature-title.svg') }}" alt="いつでも、どこでも">
      </div>
      <!-- feature-title -->

      <div class="service-list">
        <img src="{{ asset('assets/web/images/line/service-list.svg') }}" alt="">
      </div>
      <!-- service-list -->

      <div class="line-button">
        <button type="button" name="" class="line-button line-button__login" onclick="window.location.href
                = '{{ action('Auth\LineController@login') }}'">
          <img src="{{ asset('assets/web/images/line/line-button__login.png') }}" alt="">
        </button>
      </div>
      <!-- line-register -->

      <p class="usage-contract">Cheersに登録するにあたって、<a href="#">利用規約</a>に同意することとします。</p>
      <!-- usage-contract -->

    </div>
  </div>
@else
  <section class="button-box">
    <label for="trigger" class="open_button button-settlement">モーダル１(ボタン1つ)</label>
  </section>

  <section class="button-box">
    <label for="trigger2" class="open_button button-settlement">モーダル2(ボタン2つ)</label>
  </section>

  <section class="button-box">
    <label for="trigger3" class="open_button button-settlement">モーダル3</label>
  </section>

  <style>
    .button-box {
      margin: 4em auto 0 auto;
      text-align: center;
    }

    .button-settlement {
      width: 100%;
      height: 50px;
      font-size: 16px;
      border: 0px;
      color: #fff;
      line-height: 1;
      letter-spacing: 2px;
      text-align: center;
      max-width: 280px;
      margin: 1em auto 0 auto;
      border-radius: 50px;
      background: #13c8c8;
      background: -webkit-gradient(linear, left top, right top, from(#13c8c8), to(#38dfb4));
      background: -o-linear-gradient(left, #13c8c8 0%, #38dfb4 100%);
      background: linear-gradient(left, #13c8c8 0%, #38dfb4 100%);
      background: -webkit-linear-gradient(left, #13c8c8 0%, #38dfb4 100%);
      background: linear-gradient(to right, #13c8c8 0%, #38dfb4 100%);
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#13c8c8', endColorstr='#38dfb4', GradientType=1);
    }
  </style>
  @if($token)
    <script>
        window.localStorage.setItem('access_token', '{{ $token }}');
    </script>
  @endif
@endsection

@section('web.extra')
  @modal(['triggerId' => 'trigger', 'triggerClass' =>''])
  @slot('title')
    タイトルが入りますタイトルが
  @endslot

  @slot('content')
    ここにテキストが入りますここにテキストが入りますここにテキストが入ります
  @endslot
  @endmodal

  @confirm(['triggerId' => 'trigger2', 'triggerClass' =>''])
  @slot('title')
    タイトルが入りますタイトルが
  @endslot

  @slot('content')
    ここにテキストが入りますここにテキストが入りますここにテキストが入ります
  @endslot
  @endconfirm

  @alert(['triggerId' => 'trigger3'])
  @slot('content')
    ここにタイトルが入ります
  @endslot
  @endalert
@endif
@endsection

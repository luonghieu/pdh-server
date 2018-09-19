@section('title', 'Cheers')
@section('screen.id', '')

@extends('layouts.web')
@section('web.content')
@if(!Auth::check())
<a href="{{ route('auth.line') }}">
  <img src="{{ asset('images/btn_login_base.png') }}" alt="">
</a>
@else
<h1>hello, {{ Auth::user()->fullname }}</h1>
@endif

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
  @modal(['triggerId' => 'trigger'])
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
@endsection

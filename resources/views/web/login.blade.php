@section('title', 'Cheers Login')
@section('controller.id', 'login-controller')
@section('screen.id', 'login')
@extends('layouts.web')
@section('web.extra')
    @if ($error = Session::pull('error'))
        @alert(['triggerId' => 'login-error', 'labelId' => 'login-error-label'])
        @slot('content')
            {{ $error }}
        @endslot
        @endalert
        <script>
            document.getElementById('login-error-label').click();
        </script>
    @endif
@endsection
@section('web.content')
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
                <button type="button" name="" class="line-button line-button__login" onclick="dataLayer.push
                        ({'event': 'line_resistration'}); window.location.href = '{{ action('Auth\LineController@login') }}'">
                    <img src="{{ asset('assets/web/images/line/line-button__login.png') }}" alt="">
                </button>
            </div>
            <!-- line-register -->
            <p class="usage-contract">Cheersに登録するにあたって、<a href="{{ url('/service/law') }}">利用規約</a>に同意することとします。</p>
            <!-- usage-contract -->
        </div>
    </div>
@endsection
<script>

    if(localStorage.getItem("order_call")){
      localStorage.removeItem("order_call");
    }

    if (localStorage.getItem("order_offer")) {
      localStorage.removeItem("order_offer");
    }

    if(localStorage.getItem("order_params")){
      localStorage.removeItem("order_params");
    }

    if (localStorage.getItem("cast_offer")) {
      localStorage.removeItem("cast_offer");
    }

</script>

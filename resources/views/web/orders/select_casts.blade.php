@section('title', 'キャストを指名しますか?')
@section('screen.class', 'ge2-3')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ asset('assets/web/css/ge_2_3.css') }}">
@endsection
@section('web.content')
  <h2>指名したいキャストがいる場合は選択してください</h2>
  <p class="message">※ご希望に<span>添えない可能性</span>もございます。<br/>※指名料が1人あたり15分毎に500Pが別途発生します。</p>
  <form action="{{ route('guest.orders.post_step3') }}" method="POST" class="create-call-form" id="" name="select_casts_form">
    {{ csrf_field() }}
    <div class="">
      <div class="form-grpup"><!-- フォーム内容 -->
        @if(count($casts['data']))
          @include('web.orders.load_more_list_casts', compact('casts'))
          <input type="hidden" id="next_page" value="{{ $casts['next_page_url'] }}">
        @endif
      </div>
      @if(isset($castNumbers))
      <input type="hidden" value="{{ $castNumbers }}" class="cast-numbers">
      @endif
    </div>
    <button type="submit" class="form_footer ct-button" id="sb-select-casts">次に進む(3/4)</button>
  </form>
@endsection

@section('web.script')
  <script>
    $(function () {
      var requesting = false;
      var currentPage = 1;
      $(document).on('scroll', function () {
        if ($(window).scrollTop() + $(window).height() == $(document).height() && requesting == false) {
          var url = $('#next_page').val();
          if (url) {
            requesting = true;
            window.axios.get('/step3/load_more', {
              params: { next_page: url },
            }).then(function (res) {
              res = res.data;
              $('#next_page').val(res.next_page || '');
              $('#next_page').before(res.view);
              requesting = false;
            }).catch(function () {
              requesting = false;
            });
          }
        }
      });
    });
  </script>
@endsection

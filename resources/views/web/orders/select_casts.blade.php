@section('title', 'キャストを指名しますか?')
@section('screen.class', 'ge2-3')
@extends('layouts.web')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/ge_2_3.min.css') }}">
@endsection
@section('web.extra')
  <div class="modal_wrap">
    <input id="max-cast" type="checkbox">
    <div class="modal_overlay">
      <label for="max-cast" class="modal_trigger" id="lb-max-cast"></label>
      <div class="modal_content modal_content-btn1">
        <div class="text-box" id="content-message">
          <h2></h2>
          <p>追加でキャストを指名したい場合は、<br> キャストの人数を追加してください</p>
        </div>
        <label for="max-cast" class="close_button">OK</label>
      </div>
    </div>
  </div>
@endsection

@section('web.content')
  <h2>指名したいキャストがいる場合は選択してください</h2>
  <p class="message">※ご希望に<span>添えない可能性</span>もございます。<br/>※指名料が1人あたり15分毎に500Pが別途発生します。</p>
    <div class="form-grpup" id="list-cast-order"></div>
    <button type="button" class="form_footer ct-button" id="sb-select-casts">
      <a href="{{ route('guest.orders.get_step4') }}">希望リクエストせずに進む(3/4)</a>
    </button>
@endsection
<script>
  var avatarsDefault = "<?php echo asset('assets/web/images/gm1/ic_default_avatar@3x.png'); ?>";
  var link = "<?php echo env('APP_URL') . '/cast/' ?>";
  var loadMore = "<?php echo env('APP_URL') . '/step3/load_more' ?>";
</script>

@section('web.script')

@endsection

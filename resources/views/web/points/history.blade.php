@section('title', 'ポイント履歴')
@section('screen.id', 'gl2-1')

@extends('layouts.web')
@section('web.extra')
<form action="#" method="post" id="form-receipt">
  {{ csrf_field() }}
  <div class="modal_wrap modal5">
    <input id="trigger5" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger5" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn5">
        <div class="text-box">
          <h2>領収書を発行する</h2>
          <div>
            <p class="p1">宛名(任意)</p>
            <label data-field="name" id="name-error" class="error help-block" for="name"></label>
            <input class="m5-text-potiton-1" type="text" id="name" name="name" placeholder="例：株式会社チアーズ">
          </div>
          <div>
            <p class="p2">但し書き(任意)</p>
            <label data-field="content" id="content-error" class="error help-block" for="content"></label>
            <input class="m5-text-potiton-2" type="text" id="content" name="content" placeholder="例：飲食代">
            <input type="hidden" name="point_id" value="" />
          </div>
        </div>
        <div class="close_button-box">
          <div class="close_button-block left">
            <label for="trigger5" class="btn4">キャンセル</label>
          </div>
          <div class="close_button-block">
            <button type="submit" for="trigger5" class="btn btn-bg bd-none">発行する</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<div class="modal_wrap">
  <a href="" id='mailto'></a>
  <input id="trigger2" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger2" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
        <img id="img-pdf" name="pdf" alt="">
        <div class="mb-10">
          <div class="close_button-block">
            <button type="submit" id="send-mail" img-file="" class="btn btn-bg bd-none">メールで送信</button>
          </div>
          <div class="close_button-block">
            <a class="btn btn-bg bd-none" id="img-download" download>画像を保存</a>
          </div>
        </div>
      </div>
  </div>
</div>
@endsection
@section('web.content')
@if (!$points['data'])
<div class="list_wrap">
  <div class="point-empty">
    <img src="{{ asset('assets/web/images/gl2-1/ic_point_gray.png') }}" alt="">
    <span>ポイント履歴はまだありません</span>
  </div>
</div>
@else
  <label for="trigger2" class="open_button button-settlement"></label>
  <div class="list_wrap">
    @include('web.points.list_point', compact('points'))
    <input type="hidden" id="next_page" value="{{ $points['next_page_url'] }}">
  </div>  <!-- /list_wrap -->
@endif
@endsection
@section('web.script')
<script>
  $(function () {
    var requesting = false;
    $(window).on('scroll', function () {
      if ($(window).scrollTop() + $(window).height() == $(document).height() && requesting == false) {
        var url = $('#next_page').val();

        if (url) {
          requesting = true;
          window.axios.get("<?php echo env('APP_URL')  . '/point_history/more' ?>", {
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

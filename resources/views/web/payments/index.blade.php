@section('title', '振込履歴')
@section('controller.id', 'gl2-1')
@section('screen.id', 'gl4')
@extends('layouts.web')
@section('web.extra')
<div class="modal_wrap">
    <input id="buypoint-alert" type="checkbox">
    <div class="modal_overlay">
        <label for="buypoint-alert" class="modal_trigger" id="buypoint-alert-label"></label>
        <div class="modal_content modal_content-btn3">
            <div class="content-in">
                <h2 id="buypoint-alert-content"></h2>
            </div>
        </div>
    </div>
</div>

@endsection
@section('web.content')
<div class="list_wrap" id="list-payment">
    <div class="point_list_wrap">
        @if (!empty($payments['data']))
            @include('web.payments.list_payments', compact('payments'))
            <input type="hidden" id="next_page" value="{{ $payments['next_page_url'] }}">
        @else
            <div class="list_wrap">
              <div class="point-empty">
                <img src="{{ asset('assets/web/images/gl2-1/ic_email_gray@3x.png') }}" alt="">
                <span>振込履歴はありません</span>
              </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('web.script')
<script>
  $(function () {
    var requesting = false;
    $(document).on('scroll', function () {
      if ($(window).scrollTop() + $(window).height() == $(document).height() && requesting == false) {
        var url = $('#next_page').val();

        if (url) {
          requesting = true;
          window.axios.get("<?php echo env('APP_URL') . '/payments/load_more' ?>", {
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

@section('title', '予約一覧')
@section('screen.class', 'gn-1')

@extends('layouts.web')
@section('web.content')
  <div class="list_wrap">
  @if(count($orders['data']))
    @include('web.orders.load_more_list_orders', compact('orders'))
    <input type="hidden" id="next_page" value="{{ $orders['next_page_url'] }}">
    <section class="modal-cancel-order">
      <label for="md-cancel" class="lb-modal-cancel" >キャンセル</label>
    </section>
  @endif
 </div>  <!-- /list_wrap -->
@endsection

@section('web.extra')
  @confirm(['triggerId' => 'cancel', 'buttonRight' =>'はい',
   'buttonLeft' =>'いいえ', 'triggerCancel' =>'','triggerSuccess' =>'cf-cancel-order'])
    @slot('title')
      この日程をキャンセルしますか？
    @endslot

    @slot('content')
    @endslot
  @endconfirm

  @modal(['triggerId' => 'md-cancel', 'triggerClass' =>'md-cancel-order'])
    @slot('title')
      予約キャンセルが完了しました
    @endslot

    @slot('content')
    @endslot
  @endmodal
@endsection

@section('web.script')
<script>
  window.onpageshow = function () {
    var requesting = false;
    $(document).on('scroll', function () {
      if ($(window).scrollTop() + $(window).height() == $(document).height() && requesting == false) {
        var url = $('#next_page').val();
        if (url) {
          requesting = true;
          window.axios.get("<?php echo env('APP_URL') . '/orders/load_more' ?>", {
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
  };
</script>
@endsection

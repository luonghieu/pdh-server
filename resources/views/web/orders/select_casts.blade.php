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
  <form action="{{ route('guest.orders.post_step3') }}" method="POST" class="create-call-form" id="" name="select_casts_form">
    {{ csrf_field() }}
    <div class="">
      <div class="form-grpup" id="list-cast-order"><!-- フォーム内容 -->
        @if(isset($casts['data']))
          @if(count($casts['data']))
            @include('web.orders.load_more_list_casts', compact('casts'))
            <input type="hidden" id="next_page" value="{{ $casts['next_page_url'] }}">
          @endif
        @endif
      </div>
      @if(isset($castNumbers))
      <input type="hidden" value="{{ $castNumbers }}" class="cast-numbers">
      @endif
      <input type="hidden" value="" class="cast-ids" name="cast_ids">
    </div>
    <button type="submit" class="form_footer ct-button" id="sb-select-casts">指名せずに進む(3/4)</button>
  </form>
@endsection

@section('web.script')
  <script>
    $(function () {
      function checkedCasts() {
        if(localStorage.getItem("order_call")){
          var arrIds = JSON.parse(localStorage.getItem("order_call")).arrIds;
          if(arrIds) {
            if(arrIds.length) {
              const inputCasts = $('.select-casts');
              $.each(inputCasts,function(index,val){
                if(arrIds.indexOf(val.value) > -1) {
                  $(this).prop('checked',true);
                  $(this).parent().find('.cast-link').addClass('cast-detail');
                  $('.label-select-casts[for='+  val.value  +']').text('指名中');
                }
              })

              $(".cast-ids").val(arrIds.toString());
              $('#sb-select-casts').text('次に進む(3/4)');
            }
          }
        }
      }

      /*Load more list cast order*/
      var requesting = false;
      var windowHeight = $(window).height();

      function needToLoadmore() {
        return requesting == false && $(window).scrollTop() >= $(document).height() - windowHeight - 100;
      }

      function handleOnLoadMore() {
        // Improve load list image
        $('.lazy').lazy({
            placeholder: "data:image/gif;base64,R0lGODlhEALAPQAPzl5uLr9Nrl8e7..."
        });

        if (needToLoadmore()) {
          var url = $('#next_page').val();

          if (url) {
            requesting = true;
            window.axios.get("<?php echo env('APP_URL') . '/step3/load_more' ?>", {
              params: { next_page: url },
            }).then(function (res) {
              res = res.data;
              $('#next_page').val(res.next_page || '');
              $('#next_page').before(res.view);
              checkedCasts();
              requesting = false;
            }).catch(function () {
              requesting = false;
            });
          }
        }
      }

      $(document).on('scroll', handleOnLoadMore);
      $(document).ready(handleOnLoadMore);
      /*!----*/

      checkedCasts();

      if (localStorage.getItem("order_call")) {
        var countIds = JSON.parse(localStorage.getItem("order_call")).countIds;
        if (localStorage.getItem("full")) {
            var text = ' 指名できるキャストは'+ countIds + '名です';
            $('#content-message h2').text(text);
            $('#max-cast').prop('checked',true);
            localStorage.removeItem("full");
        }
      }
    });
  </script>
@endsection

@section('title', 'タイムライン')
@section('controller.id', 'time-line-index-controller')

@extends('layouts.web')
@section('web.extra')
    <div class="modal_wrap modal-confirm">
        <input id="timeline-del" type="checkbox">
        <div class="modal_overlay">
            <label for="timeline-del" class="modal_trigger"></label>
            <div class="modal_content modal_content-btn2">
                <div class="text-box">
                    <h2>投稿を削除しますか？</h2>
                </div>
                <div class="close_button-box">
                    <div class="close_button-block">
                        <label for="timeline-del" class="close_button  left ">キャンセル</label>
                    </div>
                    <div class="close_button-block">
                        <label for="timeline-del" data-id='' class="close_button right" id="btn-del-timeline">削除</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal_wrap">
        <input id="timeline-not-found" type="checkbox">
        <div class="modal_overlay">
          <label for="timeline-not-found" class="modal_trigger"></label>
          <div class="modal_content modal_content-btn1">
            <div class="text-box show-message-order-call">
              <p>タイムラインません</p>
            </div>
            <label for="timeline-not-found" class="close_button">OK</label>
          </div>
        </div>
    </div>

@endsection

@section('web.content')
    <div class="page-header-timeline">
        <h1 class="text-bold">タイムライン</h1>
    </div>
    <div class="timeline">
        <section class="portlet">
            <div class="portlet-content--timeline">
                <input type="hidden" value="{{ Auth::user()->id }}" id="user_id_login">
                @if(isset($userId))
                <input type="hidden" name="user_id" value="{{ $userId }}" id="user_id_timelines">
                @endif
                <div class="timeline-list" id="timeline-index"></div>
            </div>
        </section>
        @if(!isset($userId))
        <section class="timeline-button">
            <a href="">
                <button class="timeline-button__edit">
                    <a href="{{ route('web.timelines.create') }}">
                        <img src="{{ asset('assets/web/images/common/timeline-button_edit.svg') }}">
                    </a>
                </button>
            </a>
        </section>
        @endif
    </div>
@endsection

@section('web.script')
    <script>
        var btnNotLike = "<?php echo asset('assets/web/images/common/like-icon.svg'); ?>";
        var btnLike = "<?php echo asset('assets/web/images/common/like-icon_on.svg'); ?>";
        var loadMoreTimelines = "<?php echo env('APP_URL') . '/timelines/load_more' ?>";
        var showDetail = "<?php echo env('APP_URL') . '/timelines' ?>";
        var avatarsDefault = "<?php echo asset('assets/web/images/gm1/ic_default_avatar@3x.png'); ?>";
        var castDetail = "<?php echo env('APP_URL') . '/cast' ?>";
        var guestDetail = "<?php echo env('APP_URL') . '/guest' ?>";
        var btnTimelineDel = "<?php echo asset('assets/web/images/common/timeline-like-button_del.svg'); ?>";
    </script>
@endsection

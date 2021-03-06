@section('title', '退会申請フォーム')
@extends('layouts.web')
@section('web.extra')
<div class="modal_overlay" id="popup-resign">
  <div class="modal-leave">
    <div class="modal-leave__top">
      <div class="modal-leave__title">ご確認ください</div>
      <div class="modal-leave__text">決済が完了していない予約があるため、
        <br>退会ができません。マイページから決済を
        <br>行ってください。</div>
    </div>
    <div class="modal-leave__bottom"><a href="{{ route('web.index') }}">マイページへ戻る  </a>
    </div>
  </div>
</div>
@endsection
@section('web.content')
<div class="page-header-timeline">
  <h1 class="text-bold">退会申請フォーム</h1>
</div>
<div class="leave">
  <div class="leave-header">注意事項を確認の上、退会申請をしてください</div>
  <div class="leave-content">
    <section class="portlet">
      <div class="portlet-header">
        <div class="portlet-header__title">注意事項</div>
      </div>
      <div class="portlet-content">
        <div class="leave-note-list">
          <div class="leave-note-list__item">決済が完了していない予約がある場合は退会できません</div>
          <div class="leave-note-list__item">ポイントの残高は払い戻されずに失効します</div>
          <div class="leave-note-list__item">過去の予約やトークルームにアクセスできなくなります</div>
        </div>
      </div>
    </section>
  </div>
  <div class="leave-footer">
    <div class="leave-footer__check">
      <label class="checkbox">
        <input type="checkbox" class="cb-cancel" id="check-agree"><span></span>注意事項に同意する
      </label>
    </div>
    <button class="leave-submit" id="withdraw" disabled>退会する</button>
  </div>
</div>
@stop
@section('web.script')
  <script src="{{ mix('assets/web/js/leave.min.js') }}"></script>
@endsection


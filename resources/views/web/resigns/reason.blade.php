@section('title', '退会申請フォーム')
@extends('layouts.web')
@section('web.content')
<div class="page-header-timeline">
  <h1 class="text-bold">退会申請フォーム</h1>
</div>
<div class="leave">
  <div class="leave-content">
    <section class="portlet">
      <div class="portlet-header">
        <div class="portlet-header__title">退会理由を教えてください(複数選択可)</div>
      </div>
      <div class="portlet-content">
        <div class="leave-reason-list">
          <div class="leave-reason-list__item">
            <label class="checkbox" id="lab-reason1">
              <input class="cb-cancel" type="checkbox" id="reason1"><span></span>サービスの使い方が分からない
            </label>
          </div>
          <div class="leave-reason-list__item">
            <label class="checkbox" id="lab-reason2">
              <input class="cb-cancel" type="checkbox" id="reason2"><span></span>金額が高すぎる
            </label>
          </div>
          <div class="leave-reason-list__item">
            <label class="checkbox" id="lab-reason3">
              <input class="cb-cancel" type="checkbox" id="reason3"><span></span>一緒に飲みたいキャストがいない
            </label>
          </div>
          <div class="leave-reason-list__item">
            <label class="checkbox">
              <input class="cb-cancel" id="textareaCheck" type="checkbox"><span></span>その他の理由
            </label>
          </div>
        </div>
      </div>
    </section>
    <section class="portlet">
      <div class="portlet-content">
        <div class="leave-comment">
          <div class="leave-comment__input">
            <textarea rows="8" maxlength="180" id="description" name="description" placeholder="退会理由をお聞かせいただきますか?"
                      disabled></textarea>
          </div>
          <div class="js-resign-message color-error"></div>
          <div class="leave-comment__sum">
            <p>0</p>
          </div>
        </div>
      </div>
    </section>
  </div>
  <div class="leave-footer">
    <button class="leave-submit" id="leaveSubmit" disabled>次へ</button>
  </div>
</div>
@stop
@section('web.script')
  <script src="{{ mix('assets/web/js/leave.min.js') }}"></script>
@endsection

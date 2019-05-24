@section('title', '退会申請フォーム')
@extends('layouts.web')
@section('web.content')
<div class="page-header-timeline">
  <h1 class="text-bold">退会申請フォーム</h1>
</div>
<div class="leave">
  <form>
    <div class="leave-content">
      <section class="portlet">
        <div class="portlet-content">
          <div class="leave-done">
            <div class="leave-done__logo">
              <img src="{{ asset('assets/web/images/common/logo-leave.svg') }}">
            </div>
            <div class="leave-done__title">退会申請が完了しました。</div>
            <div class="leave-done__link"><a href="{{ route('web.index') }}">Topページへ戻る</a>
            </div>
          </div>
        </div>
      </section>
    </div>
  </form>
</div>
@stop

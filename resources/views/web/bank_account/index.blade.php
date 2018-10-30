@section('title', '振込口座登録')
@section('screen.id', 'ci7')
@section('controller.id', 'bank_accounts')
@extends('layouts.web')
@section('web.content')
<div class="title">
  <div class="btn-back">
    <a href="{{ route('web.index') }}" class="prev"><i><img src="/assets/web/images/common/prev.svg" alt=""></i></a>
  </div>
  <div class="title-name">振込口座登録</div>
  <div class="btn-register header-item">
    <a href="{{ route('bank_account.edit') }}" class="btn-update-bank">編集</a>
  </div>
</div>
<div class="content">
  <div class="label-title">
    <p>売上振込先口座を登録してください</p>
  </div>
  <form action="#">
    <div class="bank-name border-bottom row">
      <span class="left">銀行名</span>
      <div class="right">
        <a href="#" class="value-true-color">{{ $bankAccount->bank_name }}</a>
      </div>
    </div>
    <div class="clear"></div>
    <div class="branch-name border-bottom row">
      <span class="left">支店名</span>
      <div class="right">
        <a href="#" class="value-true-color">{{ $bankAccount->branch_name }}</a>
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-type border-bottom row">
      <span class="left">口座種別</span>
      <div class="right">
        <span class="value-true-color" id="select-account-type">{{ $bankAccount->type == 1? '普通':'当座' }}</span>
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-number border-bottom row">
      <span class="left">口座番号</span>
      <div class="right">
        <input type="tel" class="value-true-color" pattern="[0-9]*" id="number" placeholder="口座番号を入力してください" value="{{ $bankAccount->number }}" disabled>
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-holder border-bottom row">
      <span class="left">口座名義</span>
      <div class="right">
        <input type="text" class="value-true-color" id="holder-name" value="{{ $bankAccount->holder_name }}" placeholder="口座名義を入力してください" disabled>
      </div>
    </div>
    <div class="clear"></div>
  </form>
</div>
@endsection

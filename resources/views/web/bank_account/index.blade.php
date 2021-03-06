@section('title', '振込口座登録')
@section('screen.id', 'ci7')
@section('controller.id', 'bank_accounts')
@extends('layouts.web')
@section('web.content')
<div class="title">
  <div class="btn-back">
    <a href="{{ route('web.index') }}" class="prev"><i><img src="/assets/web/images/common/prev.svg" alt=""></i></a>
  </div>
  <div class="title-name">
    <p>振込口座登録</p>
  </div>
  <div class="btn-register header-item">
    <a href="{{ route('cast_mypage.bank_account.edit') }}" class="btn-update-bank">編集</a>
  </div>
</div>
<div class="content">
  <div class="label-title">
    <p>売上金の振込先口座を登録してください</p>
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
        <span class="value-true-color">{{ $bankAccount->type == 1? '普通':'当座' }}</span>
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-number border-bottom row">
      <span class="left">口座番号</span>
      <div class="right">
        <span class="value-true-color">{{ $bankAccount->number }}</span>
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-holder border-bottom row">
      <span class="left">口座名義</span>
      <div class="right">
        <span class="value-true-color">{{ $bankAccount->holder_name }}</span>
      </div>
    </div>
    <div class="clear"></div>
  </form>
</div>
@endsection

@section('title', '振込口座登録')
@section('screen.id', 'ci7')
@section('controller.id', 'edit_bank_accounts')
@extends('layouts.web')
@section('web.content')
<div class="title">
  <div class="btn-back">
    <a href="{{ route('cast_mypage.bank_account.index') }}" class="prev"><i><img src="/assets/web/images/common/prev.svg" alt=""></i></a>
  </div>
  <div class="title-name">振込口座登録</div>
  <div class="btn-register header-item">
    <a class="btn-edit-bank" >完了</a>
  </div>
</div>
<div class="content">
  <div class="label-title">
    <p>売上金の振込先口座を登録してください</p>
  </div>
  <form action="#">
    @php
      $url = route('cast_mypage.bank_account.index');
    @endphp
    <input type="hidden" id="back-url" value="{{ $url }}">
    <div class="bank-name border-bottom row">
      <span class="left">銀行名</span>
      <div class="right">
        <input type="hidden" name="bank-account" id="bank-account" value="{{ Auth::user()->bankAccount->id }}">
        @if (request()->bank_name)
        <input type="hidden" name="bank_name" value="{{ request()->bank_name }}">
        <input type="hidden" name="bank_code" id="bank-code" value="{{ request()->bank_code }}">
        <a href="{{ route('cast_mypage.bank_account.bank_name', ['bank_name' => request()->bank_name, 'bank_code' => request()->bank_code, 'branch_name' => request()->branch_name, 'branch_code' => request()->branch_code, 'type' => 'edit']) }}" class="value-true-color" id="bank-name">{{ request()->bank_name }}</a>
        @else
        <input type="hidden" name="bank_code" id="bank-code" value="{{ $bankAccount->bank_code }}">
        <a href="{{ route('cast_mypage.bank_account.bank_name') }}" class="value-true-color" id="bank-name">{{ $bankAccount->bank_name }}</a>
        @endif
      </div>
    </div>
    <div class="clear"></div>
    <div class="branch-name border-bottom row">
      <span class="left">支店名</span>
      <div class="right">
        @if (request()->branch_name)
        <input type="hidden" name="branch_name" value="{{ request()->branch_name }}">
        <input type="hidden" name="branch_code" id="branch-code" value="{{ request()->branch_code }}">
        <a href="{{ route('cast_mypage.bank_account.branch_bank_name', ['bank_name' => request()->bank_name, 'bank_code' => request()->bank_code, 'branch_name' => request()->branch_name, 'branch_code' => request()->branch_code, 'type' => 'edit']) }}" class="value-true-color" id="branch-name">{{ request()->branch_name }}</a>
        @else
        <input type="hidden" name="branch_code" id="branch-code" value="{{ $bankAccount->branch_code }}">
        <a href="{{ route('cast_mypage.bank_account.branch_bank_name', ['bank_name' => request()->bank_name, 'bank_code' => request()->bank_code, 'branch_name' => request()->branch_name, 'branch_code' => request()->branch_code]) }}" class="value-true-color" id="branch-name">{{ $bankAccount->branch_name }}</a>
        @endif
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-type border-bottom row">
      <span class="left">口座種別</span>
      <div class="right">
        <label for="" class="hidden-label">選択してください</label>
        <select name="type" id="select-account-type" class="value-true-color">
          <option value="1" {{ $bankAccount->type=1?'selected':'' }}>普通</option>
          <option value="2" {{ $bankAccount->type=2?'selected':'' }}>当座</option>
        </select>
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-number border-bottom row">
      <span class="left">口座番号</span>
      <div class="right">
        <input type="tel" pattern="[0-9]*" name="number" id="number" value="{{ $bankAccount->number }}" placeholder="口座番号を入力してください">
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-holder border-bottom row">
      <span class="left">口座名義</span>
      <div class="right">
        <input type="text" id="holder-name" name="holder_name" value="{{ $bankAccount->holder_name }}" placeholder="口座名義を入力してください">
      </div>
    </div>
    <div class="clear"></div>
  </form>
</div>
@endsection

@section('title', '振込口座登録')
@section('screen.id', 'ci7')
@section('controller.id', 'create_bank_accounts')
@extends('layouts.web')
@section('web.content')
<div class="title">
  <div class="title-name">振込口座登録</div>
  <div class="btn-register header-item">
    <a id="btn-create-bank-info" class="btn-submit-bank" >完了</a>
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
        @if (request()->bank_name)
        <input type="hidden" name="bank_name" value="{{ request()->bank_name }}">
        <input type="hidden" name="bank_code" id="bank-code" value="{{ request()->bank_code }}">
        <a href="{{ route('bank_account.bank_name', ['bank_name' => request()->bank_name, 'bank_code' => request()->bank_code, 'branch_name' => request()->branch_name, 'branch_code' => request()->branch_code]) }}" class="value-true-color" id="bank-name">{{ request()->bank_name }}</a>
        @else
        <a href="{{ route('bank_account.bank_name') }}">銀行名を入力してください</a>
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
        <a href="{{ route('bank_account.branch_bank_name', ['bank_name' => request()->bank_name, 'bank_code' => request()->bank_code, 'branch_name' => request()->branch_name, 'branch_code' => request()->branch_code]) }}" class="value-true-color" id="branch-name">{{ request()->branch_name }}</a>
        @else
        <a href="{{ route('bank_account.branch_bank_name', ['bank_name' => request()->bank_name, 'bank_code' => request()->bank_code, 'branch_name' => request()->branch_name, 'branch_code' => request()->branch_code]) }}">支店名を入力してください</a>
        @endif
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-type border-bottom row">
      <span class="left">口座種別</span>
      <div class="right">
        <label for="">選択してください</label>
        <select name="type" id="select-account-type">
          <option value="1">普通</option>
          <option value="2">当座</option>
        </select>
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-number border-bottom row">
      <span class="left">口座番号</span>
      <div class="right">
        <input type="tel" pattern="[0-9]*" name="number" id="number" placeholder="口座番号を入力してください">
      </div>
    </div>
    <div class="clear"></div>
    <div class="account-holder border-bottom row">
      <span class="left">口座名義</span>
      <div class="right">
        <input type="text" id="holder-name" name="holder_name" placeholder="口座名義を入力してください">
      </div>
    </div>
    <div class="clear"></div>
  </form>
</div>
@endsection

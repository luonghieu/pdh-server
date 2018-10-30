@section('title', '振込口座登録')
@section('screen.id', 'ci7')
@section('controller.id', 'select_bank_name')
@extends('layouts.web')
@section('web.content')
<div class="title">
  <div class="btn-back">
    <a href="{{ URL::previous() }}" class="prev"><i><img src="/assets/web/images/common/prev.svg" alt=""></i></a>
  </div>
  <div class="title-name">銀行名</div>
  <div class="btn-register header-item">
    <a id="btn-create"></a>
  </div>
</div>
<div class="content">
  <form action="{{ route('cast_mypage.bank_account.bank_name') }}" method="POST">
      <input type="hidden" name="bank_name"  value="{{ request()->bank_name }}">
      <input type="hidden" name="bank_code"  value="{{ request()->bank_code }}">
      <input type="hidden" name="branch_name"  value="{{ request()->branch_name }}">
      <input type="hidden" name="branch_code"  value="{{ request()->branch_code }}">
      <input type="hidden" name="type"  value="{{ request()->type }}">
    {{ csrf_field() }}
    <div class="result-bank-name">
      <div class="account-holder border-bottom row">
        <span class="left color-green">銀行名</span>
        <div class="right">
          <input type="text" id="bank-name" name="bank_name" value="{{ request()->bank_name }}" placeholder="例）みずほ銀行">
        </div>
      </div>
      <div class="clear"></div>
      @if (isset($listResult))
        @if ($listResult->first())
          @foreach ($listResult as $result)
          @php
            if(\Session::has('backUrl')) {
              $url = 'cast_mypage.bank_account.edit';
            } else {
              $url = 'cast_mypage.bank_account.index';
            }
          @endphp
          <a href="{{ route($url, ['bank_name' => $result->name, 'bank_code' => $result->code, 'branch_name' => $infoBank?$infoBank['branch_name']:'', 'branch_code' => $infoBank?$infoBank['branch_code']:'']) }}">
            <div class="border-bottom row">
              <span class="left result">{{ $result->name }}</span>
              @if (Auth::user()->bankAccount && Auth::user()->bankAccount->bank_name == $result->name)
              <img src="/assets/web/images/ci7/ic_check_green@3x.png" alt="">
              @endif
            </div>
            <div class="clear"></div>
          </a>
          @endforeach
        @else
        <div class="notify-no-result">
          <p>データが見つかりません。</p>
        </div>
        @endif
      @endif
    </div>
  </form>
</div>
@endsection

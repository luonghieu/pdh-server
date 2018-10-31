@section('title', '振込口座登録')
@section('screen.id', 'ci7')
@section('controller.id', 'select_branch_bank')
@extends('layouts.web')
@section('web.content')
<div class="title">
  <div class="btn-back">
    <a href="{{ URL::previous() }}" class="prev"><i><img src="/assets/web/images/common/prev.svg" alt=""></i></a>
  </div>
  <div class="title-name">支店名</div>
  <div class="btn-register header-item">
    <a id="btn-create"></a>
  </div>
</div>
<div class="content">
  <form action="{{ route('cast_mypage.bank_account.branch_bank_name') }}" method="POST">
    <input type="hidden" name="bank_name"  value="{{ request()->bank_name }}">
    <input type="hidden" name="bank_code"  value="{{ request()->bank_code }}">
    <input type="hidden" name="branch_name"  value="{{ request()->branch_name }}">
    <input type="hidden" name="branch_code"  value="{{ request()->branch_code }}">
    {{ csrf_field() }}
    <div class="result-bank-name">
      <div class="account-holder border-bottom row">
        <span class="left color-green">支店名</span>
        <div class="right">
          <input type="text" id="bank-name" name="branch_name" value="{{ request()->branch_name }}" placeholder="例）渋谷">
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
          <a href="{{ route($url, ['branch_name' => $result->name, 'branch_code' => $result->code, 'bank_name' => $infoBank?$infoBank['bank_name']:'', 'bank_code' => $infoBank?$infoBank['bank_code']:'']) }}">
            <div class="border-bottom row">
              <span class="left result">{{ $result->name }}</span>
              @if (isset($infoBank) && !empty($infoBank['branch_code']))
                @if ($infoBank['branch_code'] == $result->code)
                  <img src="/assets/web/images/ci7/ic_check_green@3x.png" alt="">
                @endif
              @else
                @if (Auth::user()->bankAccount && Auth::user()->bankAccount->branch_name == $result->name)
                <img src="/assets/web/images/ci7/ic_check_green@3x.png" alt="">
                @endif
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

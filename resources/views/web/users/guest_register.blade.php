@section('title', 'プロフィール編集')
@section('screen.id', 'gm1')
@section('screen.class', 'gm1-edit register-user')

@extends('layouts.web')
@section('web.extra')

<div class="modal_wrap">
  <input id="invite-code-error" type="checkbox">
  <div class="modal_overlay">
    <label for="invite-code-error" class="modal_trigger"></label>
    <div class="modal_content modal_content-btn1">
      <div class="text-box ">
        <h2>招待コードが正しくありません</h2>
        <p>招待コードが間違っているか、存在しません</p>
      </div>
      <label for="invite-code-error" class="close_button invite-code-error">OK</label>
    </div>
  </div>
</div>

<div class="modal_wrap">
  <input id="date-of-birth-error" type="checkbox">
  <div class="modal_overlay">
    <label for="date-of-birth-error" class="modal_trigger"></label>
    <div class="modal_content modal_content-btn1">
      <div class="text-box ">
        <p>20歳未満の方は、ご利用いただけません</p>
      </div>
      <label for="date-of-birth-error" class="close_button invite-code-error">OK</label>
    </div>
  </div>
</div>

@endsection
@section('web.content')
<div class="page-header" id="guest-register">
  <h1 class="text-bold">プロフィール登録</h1>
</div>

  <form action="#" method="GET" id="update-date-of-birth">
    {{ csrf_field() }}
  <div class="cast-profile">

    <section class="portlet register-date-of-birth">
      <div class="portlet-header">
        <h2 class="portlet-header__title">基本情報<span class="color-error">*</span></h2>
      </div>
      <div class="portlet-content">
        <ul class="portlet-content__list">
          <li class="portlet-content__item">
            <p class="portlet-content__text--list"></p>
            @php
              $max = \Carbon\Carbon::parse(now())->subYear(20);
            @endphp
            <div class="init-date-of-birth">
              <input type="date" id="date-of-birth" name="date_of_birth" data-date="" max="{{ $max->format('Y-m-d') }}" data-date-format="YYYY年MM月DD日" value="{{ \Carbon\Carbon::parse(Auth::user()->date_of_birth)->format('Y-m-d') }}">
              <i></i>
            </div>
          </li>
          <label data-field="date_of_birth" id="date-of-birth-error" class="error help-block" for="date-of-birth"></label>
          <div data-field="cohabitant_type" class="help-block"></div>
        </ul>
      </div>
    </section>

    <!-- profile-photos -->
    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">ご利用エリア<span class="color-error">*</span></h2>
      </div>
      <div class="portlet-content">
        <ul class="portlet-content__list">
          <label data-field="height" id="height-error" class="error help-block" for="height"></label>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list"></p>
            <label class="time-input">
              <div class="selectbox select-prefecture" >
                <select dir="rtl" id="prefecture-id" name="prefecture_id">
                  <option value="" class="hidden">選択してください</option>
                  @foreach($prefectures as $prefecture)
                    <option value="{{ $prefecture->id }}">{{ $prefecture->name }}</option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <label data-field="prefecture_id" class="error help-block" for="prefecture-id"></label>
          <div data-field="cohabitant_type" class="help-block"></div>
        </ul>
      </div>
    </section>

    <section class="portlet" id="invite-code">
      <div class="portlet-header">
        <h2>招待コード</h2>
      </div>
      <div class="portlet-content">
        <input type="text" name="" placeholder="招待コードを入力してください" id="input_invite-code">
      </div>
      <div id="invalid-invite-code">
        <label data-field="date_of_birth" id="date-of-birth-error" class="error help-block"></label>
      </div>
      <div>
        <p>
          ※友達紹介キャンペーンの招待コードをお持ちの場合は、こちらにご入力ください <br>
          ※これ以降で、招待コードを入力することはできません
        </p>
      </div>
    </section>

    <div class="btn-l init-button"><button type="submit">登録する</button></div>
  </div>
</form>
@endsection
@section('web.script')
<script>
//prefectures selected
$("#select-date_of_birth").on("change", function() {
  $(this).css("color", "black");
});

</script>
@stop

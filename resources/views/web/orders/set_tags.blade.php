@section('title', '今日はどんな気分?(5つまで)')
@section('screen.id', 'ge2-1-x')
@section('screen.class', 'ge2-1-b')
@extends('layouts.web')
@section('web.content')
@section('web.extra')
  <div class="modal_wrap">
    <input id="max-tags" type="checkbox">
    <div class="modal_overlay">
      <label for="max-tags" class="modal_trigger" id="lb-max-tags"></label>
      <div class="modal_content modal_content-btn1">
        <div class="text-box" id="max-tags-message">
          <h2>5つまで選択することができます</h2>
        </div>
        <label for="max-tags" class="close_button">OK</label>
      </div>
    </div>
  </div>
@endsection
<form action="{{ route('guest.orders.post_step2') }}" method="POST" class="create-call-form" id="" name="select_tags_form">
  {{ csrf_field() }}
  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>希望するキャスト</h2>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      @if(count($desires['data']))
       @foreach($desires['data'] as $tag)
       <label class="button button--green checkbox-tags {{ (isset($currentDesires) && in_array($tag['id'], $currentDesires) ) ? 'active' : '' }}">
       <input type="checkbox" name="desires[]" value="{{ $tag['id'] }}" class="tags-name" {{ (isset($currentDesires) && in_array($tag['id'], $currentDesires) ) ? 'checked="checked"' : '' }}>{{ $tag['name'] }}</label>
       @endforeach
      @endif
    </div>
  </div>

  <div class="reservation-item">
    <div class="caption"><!-- 見出し用div -->
      <h2>シチュエーション</h2>
    </div>
    <div class="form-grpup"><!-- フォーム内容 -->
      @if(count($situations['data']))
       @foreach($situations['data'] as $tag)
       <label class="button button--green checkbox-tags {{ (isset($currentSituations) && in_array($tag['id'], $currentSituations) ) ? 'active' : '' }}">
        <input type="checkbox" name="situations[]" value="{{ $tag['id'] }}" {{ (isset($currentSituations) && in_array($tag['id'], $currentSituations) ) ? 'checked="checked"' : '' }} class="tags-name">{{ $tag['name'] }}</label>
       @endforeach
      @endif
    </div>
  </div>
  <button type="submit" class="form_footer ct-button">次に進む　(2/4)</button>
</form>

@endsection

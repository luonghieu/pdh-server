<div class="modal_wrap">
  <input id="{{ $triggerId }}" type="checkbox">
  <div class="modal_overlay">
    <label for="trigger" class="modal_trigger"></label>
    <div class="modal_content modal_content-btn1">
      <div class="text-box">
        <h2>{{ $title or '' }}</h2>
        <p>{{ $content or '' }}</p>
      </div>
      <label for="{{ $triggerId }}" class="close_button {{ $triggerClass }}">{{ $button or 'OK'}}</label>
    </div>
  </div>
</div>

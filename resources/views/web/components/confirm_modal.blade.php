<div class="modal_wrap">
  <input id="{{ $triggerId }}" type="checkbox">
  <div class="modal_overlay">
    <label for="trigger2" class="modal_trigger"></label>
    <div class="modal_content modal_content-btn2">
      <div class="text-box">
        <h2>{{ $title or '' }}</h2>
        <p>{{ $content or '' }}</p>
      </div>
      <div class="close_button-box">
        <div class="close_button-block">
          <label for="{{ $triggerId }}" class="close_button  left {{ $triggerCancel or '' }}">{{ $buttonLeft or '' }}</label>
        </div>
        <div class="close_button-block">
          <label for="{{ $triggerId }}" class="close_button {{ $triggerClass or '' }} {{ $triggerSuccess or '' }}">{{ $buttonRight or '' }}</label>
        </div>
      </div>
    </div>
  </div>
</div>

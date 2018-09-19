<div class="modal_wrap">
  <input id="{{ $triggerId }}" type="checkbox">
  <div class="modal_overlay">
    <label for="trigger2" class="modal_trigger"></label>
    <div class="modal_content modal_content-btn2">
      <div class="text-box">
        <h2>{{ $title }}</h2>
        <p>{{ $content }}</p>
      </div>
      <div class="close_button-box">
        <div class="close_button-block">
          <label for="trigger2" class="close_button  left {{ $triggerClass }}">はい</label>
        </div>
        <div class="close_button-block">
          <label for="trigger2" class="close_button">いいえ</label>
        </div>
      </div>
    </div>
  </div>
</div>

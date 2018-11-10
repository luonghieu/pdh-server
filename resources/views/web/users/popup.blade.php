<div class="modal_wrap">
  <input id="cookie-popup" type="checkbox">
  <div class="modal_overlay modal_overlay-popup">
      <label for="cookie-popup" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn2">
          <div class="text-box">
              <h2 id="" style="color: #d0021b">重要なお知らせ</h2>
              <p>現在LINE@の不具合により、<br/>皆様のLINEに通知が届いておりません。<br>
              ご面倒をおかけしますが、下記のボタンから再度友だち登録をお願いします。<br>
              <br>
              キャストからのメッセージも届かないため<br>何卒よろしくお願い致します。
              </p>
              <div>
              <input type="checkbox" id="input-cookie" style="display: inline-block;margin-bottom: 15px;margin-bottom: 15px;margin-right: 15px;">今後表示しない
              </div>
          </div>
          <div class="close_button-box">
              <label for="cookie-popup" class="close_button">
                <a href="{{ 'https://line.me/R/ti/p/%40' . env('LINE_ID') }}" style="color: #FF66BB" id="cookie-link">友だち登録する</a></label>
          </div>
      </div>
  </div>
</div>

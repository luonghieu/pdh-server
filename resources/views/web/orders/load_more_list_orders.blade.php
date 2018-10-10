@foreach ($orders['data'] as $order)
 <div class="list_item">
   <div class="item_up">
     <ul class="item_left">
      <li class="time-text">
        {{ Carbon\Carbon::parse($order['date'])->format('m月d日') }} ({{ dayOfWeek()[Carbon\Carbon::parse($order['date'])->dayOfWeek] }})
      </li>
      <li class="time-text display-flex">
        <span class="text-ellipsis">{{ $order['address'] }}</span>
        <span>{{ Carbon\Carbon::parse($order['start_time'])->format('H:i') }}〜</span>
      </li>
      @if(count($order['tags']))
        <li class="tag-text">
          @foreach($order['tags'] as $tag)
          #{{ $tag['name'] }}
          @endforeach
        </li>
      @endif
     </ul>
     <ul class="item_right">
       <li class=""><span class="icon1 icon-size-w11">{{ $order['duration'] }}時間</span></li>
       <li class="">
        <span class="icon2 icon-size-w17">
         {{ number_format($order['temp_point']) }}P〜
        </span>
       </li>
       <li class=""><span class="icon3 icon-size-w15">{{ $order['total_cast'] }}名</span></li>
     </ul>
   </div>
   <div class="item_down">
     <hr class="border-blue">
     <ul class="face-img">
      @foreach($order['casts'] as $cast)
        <li class="reserve-image">
          @if (@getimagesize($cast['avatars'][0]['thumbnail']))
          <img src="{{ $cast['avatars'][0]['thumbnail'] }}" alt="顔の写真">
          @else
          <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="顔の写真">
          @endif
        </li>
      @endforeach
     </ul>
   </div>
   <div class="btn_wrap">
     <section class="btn-cancel">
      <label for="cancel" data-id ="{{ $order['id'] }}" class="lb-cancel" >キャンセル</label>
     </section>
     <button class="mess-btn" type="button" name="button">
          <a href="{{ route('message.messages',['room' =>$order['room_id']]) }}">メッセージを確認</a>
     </button>
   </div>
 </div> <!-- /list_item -->
  @endforeach

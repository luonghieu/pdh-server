<div class="next-page" data-url="{{ $rooms->next_page_url }}"></div>
@php
  $rooms = collect($rooms->data);
@endphp
@foreach ($rooms as $room)
  @php
    $sumUser = count($room->users);
    $sumImg = 0;
    $listName = [];
    switch ($sumUser) {
      case 2:
        $sumImg = '01';
        break;
      case 3:
        $sumImg = '02';
        break;
      case 4:
        $sumImg = '03';
        break;
      default:
        $sumImg = '04';
        break;
    }
  @endphp
  <section class="msg" id="msg_{{ $room->id }}" data-id="{{ $room->id }}">
    <a href="{{ route('message.messages', ['room' => $room->id]) }}">
      <div class="msg-box">
        <ul class="msg-box-img msg-img{{ $sumImg }}">
          @php
            $i = 0;
          @endphp
          @foreach ($room->users as $user)
            @if ($i != 4)
              @if ($user->id != Auth::user()->id)
                <li><img src="{{ $user->avatars ? $user->avatars[0]->path:'/assets/web/images/gm1/ic_default_avatar@3x.png' }}"></li>
                @php
                  $i++;
                @endphp
              @endif
            @endif
          @endforeach
        </ul>
        <div class="msg-box-ttl wrap-balloon">
          <div class="msg_time">
            @if ($room->latest_message != null)
            <p class="time-msg">{{ $room->latest_message ? Carbon\Carbon::parse($room->latest_message->created_at)->format('H:i') : ''}} </p>
            @else
            <p class="time-msg"></p>
            @endif
          </div>
            <div id="balloon_{{ $room->id }}" @if($room->unread_count > 0) class="notyfi-msg"@else class="balloon"@endif>
              <span data-unread="{{ $room->unread_count }}" id="room_{{ $room->id }}" >{{ ($room->unread_count > 99) ? '99+' : $room->unread_count }}</span>
            </div>
          <div class="msg-msg">
            @if ($room->type == App\Enums\RoomType::SYSTEM)
            <h2 class="list-name">Cheers運営局</h2>
            @else
              @foreach ($room->users as $user)
                @if ($user->id != Auth::user()->id)
                  @php
                    array_push($listName, $user->nickname);
                  @endphp
                @endif
              @endforeach
              @php
              $listName = implode(",",$listName);
              @endphp
              <h2 class="list-name">{{ $listName }}</h2>
              <h2 class="sum-users">{{ ($sumUser > 2) ? '('.$sumUser.')': ''}}</h2>
            @endif
            @if ($room->latest_message != null)
              @if ($room->latest_message->image)
              <p class="latest-message" id="latest-message_{{ $room->id }}">{{ $room->latest_message ? $room->latest_message->user->nickname:'' }}さんが写真を送信しました</p>
              @else
              <p class="latest-message" id="latest-message_{{ $room->id }}">{{ $room->latest_message ? $room->latest_message->message:'' }}</p>
              @endif
            @else
            <p class="latest-message" id="latest-message_{{ $room->id }}"></p>
            @endif
          </div>
        </div>
      </div>
    </a>
  </section>
@endforeach

@section('web.script')
<script>
$(function() {
  var $textarea = $('#textarea');
  var lineHeight = parseInt($textarea.css('lineHeight'));
  $textarea.height(20);//init
  $textarea.css("lineHeight","20px");//init

  $textarea.on('input', function(e) {
    var lines = ($(this).val() + '\n').match(/\n/g).length;
    $(this).height(lineHeight * lines);
    $textarea.css("lineHeight","1.2");//init
  });
});
</script>
@endsection

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
  <section class="msg">
    <a href="{{ route('message.messages', ['room' => $room->id]) }}">
      <div class="msg-box">
        <ul class="msg-box-img msg-img{{ $sumImg }}">
          @php
            $i = 0;
          @endphp
          @foreach ($room->users as $user)
            @if ($i != 4)
              @if ($user->id != Auth::user()->id)
                <li><img src="{{ $user->avatars ? $user->avatars[0]->path:'#' }}"></li>
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
            <p>{{ $room->latest_message ? Carbon\Carbon::parse($room->latest_message->created_at)->format('H:i') : ''}} </p>
            @else
            <p></p>
            @endif
          </div>
          @if ($room->unread_count > 0)
            <div class="balloon">
              <span>{{ $room->unread_count }}</span>
            </div>
          @endif
          <div class="msg-msg">
            @if ($room->type == App\Enums\RoomType::SYSTEM)
            <h2>Cheers運営局</h2>
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
              <h2>{{ $listName }}</h2>
              <h2 class="sum-users">{{ ($sumUser > 2) ? '('.$sumUser.')': ''}}</h2>
            @endif
            @if ($room->latest_message != null)
              @if ($room->latest_message->image)
              <p>{{ $room->latest_message ? $room->latest_message->user->nickname:'' }}さんが写真を送信しました</p>
              @else
              <p>{{ $room->latest_message ? $room->latest_message->message:'' }}</p>
              @endif
            @else
            <p></p>
            @endif
          </div>
        </div>
      </div>
    </a>
  </section>
@endforeach

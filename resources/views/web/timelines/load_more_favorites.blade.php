@foreach ($favorites['data'] as $favorite)
  <div class="timeline-like-item user-{{ $favorite['user']['id'] }}">
    <div class="timeline-like-item__profile">
      @php
        $route = '#';
        switch ($favorite['user']['type']) {
          case App\Enums\UserType::CAST:
            $route = route('cast.show', ['user' => $favorite['user']['id']]);
            break;
          case App\Enums\UserType::GUEST:
            $route = route('guest.show', ['user' => $favorite['user']['id']]);
            break;

          default:break;
        }
      @endphp
      <a href="{{ $route }}">
        <img src="{{ $favorite['user']['avatars'] ? $favorite['user']['avatars'][0]['path'] : '/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="">
      </a>
    </div>
    <div class="timeline-like-item__info">
      <p>{{ $favorite['user']['nickname'] }}</p>
      <p>{{ $favorite['user']['age'] }}歳</p>
    </div>
    @if (($favorite['user']['type'] == $user->type) || (($user->type == App\Enums\UserType::CAST) && ($user->type == $favorite['user']['type'])))
    @else
    <div class="timeline-like-item__chat">
      <a class="msg" id="create-room" data-user-id="{{ $favorite['user']['id'] }}">
        <img src="{{ asset('assets/web/images/common/msg2.svg') }}">
      </a>
    </div>
    @endif
  </div>
@endforeach

@foreach ($favorites['data'] as $favorite)
  <a href="{{ route('cast.show', $favorite['id']) }}" class="cast-items">
    <div class="thumbnail">
      @php
      $class = '';
      switch ($favorite['class_id']) {
          case 1:
              $class = 'cast-class_b';
              break;
          case 2:
              $class = 'cast-class_p';
              break;
          case 3:
              $class = 'cast-class_d';
              break;
      }
      @endphp
      @if ($favorite['avatars'] && @getimagesize($favorite['avatars'][0]['thumbnail']))
      <img class="lazy" data-src="{{ $favorite['avatars'][0]['thumbnail'] }}">
      @else
      <img class="lazy" data-src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}">
      @endif

      <span class="{{ $class }} text-bold">{{ $favorite['class'] }}</span>
      @if ($favorite['working_today'])
        <span class="today text-bold">今日OK</span>
      @endif
    </div>
    <div class="profile">
      <p class="top">
        <i class="{{ $favorite['is_online'] ? 'online' : 'offline' }}"></i>
        <span class="job text-bold">{{ $favorite['job'] }}</span>
        <span class="age text-bold">{{ $favorite['age'] }}歳</span>
      </p>
      <p class="message">{{ $favorite['intro'] ? $favorite['intro'] : '...' }}</p>
      <p class="point"><span class="text-bold">{{ number_format($favorite['cost']) }}P</span>/30分</p>
    </div>
  </a>
@endforeach

@foreach ($casts['data'] as $cast)
  <a href="{{ route('cast.show', $cast['id']) }}" class="cast-items">
    <div class="thumbnail">
      @php
      $class = '';
      switch ($cast['class_id']) {
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

      @if ($cast['avatars'] && isset($cast['avatars'][0]) && $cast['avatars'][0]['thumbnail'])
      <img class="lazy" data-src="{{ $cast['avatars'][0]['thumbnail'] }}">
      @else
      <img class="lazy" data-src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}">
      @endif

      <span class="{{ $class }} text-bold">{{ $cast['class'] }}</span>
      @if ($cast['working_today'])
        <span class="today text-bold">今日OK</span>
      @endif
    </div>
    <div class="profile">
      <p class="top">
        <i class="{{ $cast['is_online'] ? 'online' : 'offline' }}"></i>
        <span class="job text-bold">{{ $cast['job'] }}</span>
        <span class="age text-bold">{{ $cast['age'] }}歳</span>
      </p>
      <p class="message">{{ $cast['intro'] ? $cast['intro'] : '...' }}</p>
      <p class="point"><span class="text-bold">{{ number_format($cast['cost']) }}P</span>/30分</p>
    </div>
  </a>
@endforeach


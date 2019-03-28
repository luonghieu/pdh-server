    @php $renderListCast = true; $casts = $casts->toArray(); @endphp
    @if (!count($casts['data']))
        <div class="no-cast" id="cast-list">
            <figure><img src="{{ asset('assets/web/images/common/woman2.svg') }}"></figure>
            <figcaption>キャストが見つかりません</figcaption>
        </div>
    @else
        <div class="cast-list" id="cast-list">
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
                @if($cast['is_new_user'])
                  <img src="/assets/web/images/common/ic_new_user.png" class="ic-new-user" alt="">
                @endif
                @if ($cast['avatars'] && isset($cast['avatars'][0]) && $cast['avatars'][0]['thumbnail'])
                  <img class="lazy" data-src="{{ $cast['avatars'][0]['thumbnail'] }}" src="{{ $cast['avatars'][0]['thumbnail'] }}">
                @else
                  <img class="lazy" data-src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}">
                @endif

                <span class="{{ $class }} text-bold">{{ $cast['class_name'] }}</span>
                @if ($cast['is_working_today'])
                  <span class="today text-bold">今日OK</span>
                @endif
              </div>
              <div class="profile">
                <p class="top">
                  <i class="{{ $cast['is_online'] ? 'online' : 'offline' }}"></i>
                  <span class="job text-bold">{{ $cast['job_name'] }}</span>
                  <span class="age text-bold">{{ $cast['age'] }}歳</span>
                </p>
                <p class="message">{{ $cast['intro'] ? $cast['intro'] : '...' }}</p>
                <p class="point"><span class="text-bold">{{ number_format($cast['cost']) }}P</span>/30分</p>
              </div>
            </a>
          @endforeach
          @if ($casts['next_page_url'])
              <input type="hidden" id="next_page" value="{{ $casts['next_page_url'] . '&is_ajax=1'  }}">
          @endif
          <div class="sk-circle js-loading css-loading-none">
            <div class="sk-circle1 sk-child"></div>
            <div class="sk-circle2 sk-child"></div>
            <div class="sk-circle3 sk-child"></div>
            <div class="sk-circle4 sk-child"></div>
            <div class="sk-circle5 sk-child"></div>
            <div class="sk-circle6 sk-child"></div>
            <div class="sk-circle7 sk-child"></div>
            <div class="sk-circle8 sk-child"></div>
            <div class="sk-circle9 sk-child"></div>
            <div class="sk-circle10 sk-child"></div>
            <div class="sk-circle11 sk-child"></div>
            <div class="sk-circle12 sk-child"></div>
          </div>
        </div>
@endif


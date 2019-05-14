@if(isset($timelines['data']))
  @foreach($timelines['data'] as $timeline)
    <div class="timeline-item" id="timeline-{{ $timeline['id'] }}">
      <div class="user-info">
          <div class="user-info__profile">
            @if(App\Enums\UserType::CAST == $timeline['user']['type'])
            <a href="{{ route('cast.show', ['id' => $timeline['user']['id']]) }}">
            @else
            <a href="{{ route('guest.show', ['id' => $timeline['user']['id']]) }}">
            @endif
              @if (@getimagesize($timeline['user']['avatars'][0]['thumbnail']))
                <img class="lazy" data-src="{{ $timeline['user']['avatars'][0]['thumbnail'] }}" alt="">
                @else
                <img class="lazy" data-src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
              @endif
            </a>
          </div>
          <a href="{{ route('web.timelines.show', ['id' => $timeline['id']]) }}">
            <div class="user-info__text">
                <div class="user-info__top">
                    <p>{{ $timeline['user']['nickname'] }}</p>
                    <p>{{ $timeline['user']['age'] }}歳</p>
                </div>
                <div class="user-info__bottom">
                    <p>{{ $timeline['location'] }}</p>
                    <p>{{ Carbon\Carbon::parse($timeline['created_at'])->format('m/d H:i') }}</p>
                </div>
            </div>
          </a>
          @if(Auth::user()->id == $timeline['user']['id'])
          <div class="timeline-delete" data-id="{{ $timeline['id'] }}">
             <img src="{{ asset('assets/web/images/common/timeline-like-button_del.svg') }}" alt="">
          </div>
          @endif
      </div>
      <div class="timeline-content">
          <a href="{{ route('web.timelines.show', ['id' => $timeline['id']]) }}">
            <div class="timeline-article">
                <div class="timeline-article__text">{!! nl2br($timeline['content']) !!}</div>
            </div>
            @if($timeline['image'])
            <div class="timeline-images">
                <div class="timeline-images__list">
                    <div class="timeline-images__item">
                        <img class="lazy" data-src="{{ $timeline['image'] }}" width="100%">
                    </div>
                </div>
            </div>
            @endif
          </a>
          <div class="timeline-like">
              <button class="timeline-like__icon" data-id="{{ $timeline['id'] }}">
                @if($timeline['is_favourited'])
                  <img src="{{ asset('assets/web/images/common/like-icon_on.svg') }}" alt="">
                @else
                  <img src="{{ asset('assets/web/images/common/like-icon.svg') }}" alt="">
                @endif
              </button>
              <p class="timeline-like__sum"><a href="{{ route('web.timelines.show', ['id' => $timeline['id']])
              }}">{{ $timeline['total_favorites'] }}</a>
              </p>
          </div>
      </div>
    </div>
  @endforeach
@endif

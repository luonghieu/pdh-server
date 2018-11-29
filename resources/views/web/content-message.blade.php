<div class="next-page" data-url="{{ $messages['next_page_url'] }}"></div>
@php
  Carbon\Carbon::setLocale('ja');
@endphp
@if($messages == null)
<p>No Message</p>
@else
  @php
    $messagesData = collect($messages['data'])->mapToGroups(function ($item, $key) {
      return [
          Carbon\Carbon::parse($item[0]['created_at'])->format('Y-m-d') => App\Http\Resources\MessageResource::make($item),
      ];
   });
    $messagesData = $messagesData->sortKeys();
  @endphp
  @foreach ($messagesData as $key => $message)
    @if ($key == now()->today()->format('Y-m-d'))
    <div class="msg-date"><h3>今日</h3></div>
    @else
    <div class="msg-date"><h3>{{ Carbon\Carbon::parse($key)->diffForHumans()}}</h3></div>
    @endif
    @foreach ($message[0] as $elements)
    @php
      $elements = collect($elements)->sortBy('created_at');
    @endphp
      @foreach ($elements as $element)
        @php
          if($element['user_id'] == Auth::user()->id) {
            $className = 'msg-right';
          } else {
            $className = 'msg-left';
          }
        @endphp

        @if ($element['type'] == App\Enums\MessageType::SYSTEM && $element['system_type'] == App\Enums\SystemMessageType::NOTIFY)
          <div class="msg-alert">
            <h3><span>{{ Carbon\Carbon::parse($element['created_at'])->format('H:i') }}</span><br>{{ $element['message'] }}</h3>
          </div>
        @else
        <div class="{{ $className }} msg-wrap" id="msg-left">
          <figure>
            @if ($element['user']['type'] == App\Enums\UserType::CAST)
            <a href="{{ route('cast.show', $element['user_id']) }}"><img src="{{ ($element['user']['avatars'] && @getimagesize($element['user']['avatars'][0]['path'])) ? $element['user']['avatars'][0]['path'] :'/assets/web/images/gm1/ic_default_avatar@3x.png' }}"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
            @else
            <a href="javascript:void(1);"><img src="{{ ($element['user']['avatars'] && @getimagesize($element['user']['avatars'][0]['path'])) ? $element['user']['avatars'][0]['path'] :'/assets/web/images/gm1/ic_default_avatar@3x.png' }}"  alt="" title="" class="alignnone size-full wp-image-515" /></a>
            @endif
          </figure>
          <div class="{{ $className }}-text">
            @if (in_array($element['type'], [App\Enums\MessageType::MESSAGE, App\Enums\MessageType::THANKFUL]))
            <div class="text">
              <div class="text-wrapper">
                <p>
                  @php
                  $reg_exUrl = "/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?/";
                  @endphp
                  @if (preg_match($reg_exUrl, $element['message'], $url))
                    {!! nl2br(preg_replace($reg_exUrl, '<a href='. $url[0].' target="_blank">'. $url[0].'</a>' , $element['message'])) !!}
                  @else
                  {!! nl2br($element['message']) !!}
                  @endif
                </p>
              </div>
            </div>
            @endif
            @if ($element['type'] == App\Enums\MessageType::IMAGE)
              <div class="pic">
                <p>
                  <img src="{{ $element['image'] }}"  alt="" title="" class="">
                </p>
              </div>
            @endif
            @if ($element['type'] == App\Enums\MessageType::SYSTEM && $element['system_type'] == App\Enums\SystemMessageType::NORMAL)
            <div class="text">
              <div class="text-wrapper">
                @if ($element['order_id'])
                <p class="msg-system" data-id='{{ $element['order_id'] }}'>{!! nl2br($element['message']) !!}</p>
                @else
                <p>
                  @php
                    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
                  @endphp
                  @if (preg_match($reg_exUrl, $element['message'], $url))
                    {!! nl2br(preg_replace($reg_exUrl, '<a href='. $url[0].' target="_blank">'. $url[0].'</a>' , $element['message'])) !!}
                  @else
                  {!! nl2br($element['message']) !!}
                  @endif
                </p>
                @endif
              </div>
            </div>
            @endif
            <div class="time"><p>{{ Carbon\Carbon::parse($element['created_at'])->format('H:i') }}</p></div>
          </div>
        </div>
        @endif
      @endforeach
    @endforeach
 @endforeach
@endif

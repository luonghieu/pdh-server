@foreach($casts['data'] as $cast)
  <div class="cast_block" >
    <input type="checkbox" name="casts[]" value="{{ $cast['id'] }}" id="{{ $cast['id'] }}" class="select-casts">
    <div class="icon">
      <p>
        <a href="{{ route('guest.orders.cast_detail', ['id' => $cast['id']]) }}" class="cast-link">
          @if (@getimagesize($cast['avatars'][0]['thumbnail']))
          <img class="lazy" data-src="{{ $cast['avatars'][0]['thumbnail'] }}" alt="">
          @else
          <img class="lazy" data-src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
          @endif
        </a>
      </p>
    </div>
    <span class="sp-name-cast text-ellipsis text-nickname">{{ $cast['nickname'] .'('. $cast['age'] .')' }}</span>
    <label for="{{ $cast['id'] }}" class="label-select-casts">指名する</label>
  </div>
@endforeach

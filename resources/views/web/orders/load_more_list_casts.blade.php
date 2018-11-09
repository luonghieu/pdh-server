@foreach($casts['data'] as $cast)
  <div class="cast_block" >
    <input type="checkbox" name="casts[]" value="{{ $cast['id'] }}" {{ (isset($currentCasts) && in_array($cast['id'], $currentCasts) ) ? 'checked="checked"' : '' }} id="{{ $cast['id'] }}" class="select-casts">
    <div class="icon">
      <p>
        <a href="{{ route('cast.show', ['id' => $cast['id']]) }}" class="cast-link {{ (isset($currentCasts) && in_array($cast['id'], $currentCasts) ) ? 'cast-detail' : '' }}">
          @if (@getimagesize($cast['avatars'][0]['thumbnail']))
          <img src="{{ $cast['avatars'][0]['thumbnail'] }}" alt="">
          @else
          <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
          @endif
        </a>
      </p>
    </div>
    <span class="sp-name-cast text-ellipsis text-nickname">{{ $cast['nickname'] .'('. $cast['age'] .')' }}</span>
    <label for="{{ $cast['id'] }}" class="label-select-casts">{{ (isset($currentCasts) && in_array($cast['id'], $currentCasts) ) ? '指名中' : '指名する' }} </label>
  </div>
@endforeach

@foreach ($casts as $cast)
  @php
    if (isset($jobs) && $cast->job_id) {
      $jobName = str_limit($jobs[$cast->job_id], 15);
    } else {
      if ($cast->job) {
        $jobName = str_limit($cast->job, 15);
      } else {
        $jobName = '';
      }
    }

    if (isset($castClass) && $cast->class_id) {
      $className = $castClass[$cast->class_id];
    } else {
      if ($cast->class) {
        $className = $cast->class;
      } else {
        $className = '';
      }
    }
  @endphp
  <div class="cast-item">
    <a href="{{ Auth::user()->status ? route('cast.show', ['id' => $cast->id]) : 'javascript:void(0)' }}" id="{{ Auth::user()->status ? '' : 'popup-freezed-account' }}">
      @php
        if ($cast->class_id == 1) {
          $class = 'cast-class_b';
        }

        if ($cast->class_id == 2) {
          $class = 'cast-class_p';
        }

        if ($cast->class_id == 3) {
          $class = 'cast-class_d';
        }
      @endphp
      <span class="tag {{ $class }}">{{ $className }}</span>
      <img src="{{ ($cast->avatars && isset($cast->avatars[0]) && $cast->avatars[0]->thumbnail) ? $cast->avatars[0]->thumbnail :'/assets/web/images/gm1/ic_default_avatar@3x.png' }}">
      <div class="info">
        <span class="tick {{ $cast->is_online == 1? 'tick-online':'tick-offline' }}"></span>
        <span class="title-info">{{ $jobName }}  {{ $cast->age }}歳</span>
        <div class="wrap-description">
          <span class="description">{{ $cast->intro }}</span>
        </div>
      </div>
    </a>
  </div>
@endforeach
@if (Auth::user()->status)
  <a href="{{ route('cast.list_casts') }}" class="cast-item import"></a>
@else
  <a href="javascript:void(0)" class="cast-item import" id="popup-freezed-account"></a>
@endif

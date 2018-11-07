@foreach ($avatars as $avatar)
  <img class="avatar-cover" data-id="{{ $avatar->id }}" src="{{ @getimagesize($avatar->path) ? $avatar->path :'/assets/admin/img/default_avatar.png' }}" alt="avatar">
@endforeach

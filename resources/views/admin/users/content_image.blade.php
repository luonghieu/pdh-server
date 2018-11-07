@foreach ($avatars as $avatar)
  <img class="avatar-cover" data-id="{{ $avatar->id }}" src="{{ @getimagesize($avatar->path) ? $avatar->path :'/assets/admin/img/default_avatar.png' }}" alt="avatar">
@endforeach
@if ($avatars->count() < 10)
  <label class="img-default"><input type="file" name="image" id="upload-avatar" accept="image/*"></label>
  <div class="error-message"></div>
@endif

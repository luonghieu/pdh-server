@if ($errors->any())
  <div class="alert alert-danger fade in" id="flash">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    @foreach ($errors->all() as $error)
      {{ $error }}<br>
    @endforeach
  </div>
@endif

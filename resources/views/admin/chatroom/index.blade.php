@extends('layouts.admin')
@section('admin.content')
    <div id="chatroom">
        <input type="hidden" value="{{$token}}" id="token">
        <input type="hidden" value="{{$user_id}}" id="userId">
        <chat-room></chat-room>
    </div>
@endsection
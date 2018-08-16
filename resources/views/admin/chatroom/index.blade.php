@extends('layouts.chatroom')
@section('admin.content')
    <div id="chatroom">
        <input type="hidden" value="{{$token}}" id="token">
        <input type="hidden" value="{{$userId}}" id="userId">
        <chat-room></chat-room>
    </div>
@endsection

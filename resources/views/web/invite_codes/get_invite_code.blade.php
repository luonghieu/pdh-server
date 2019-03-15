@section('title', 'Invite code')
@extends('layouts.web')
@section('web.content')
  <h2>Code: <span>{{$inviteCode->code}}</span></h2>
@endsection
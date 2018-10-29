@section('title', 'キャストランキング')
@section('web.extra_css')
<link rel="stylesheet" href="{{ asset('assets/web/css/gf_4.css') }}">
@endsection
@section('screen.id', 'gf4')
@extends('layouts.web')
@section('web.content')
<div class="page-header">
  <h1>キャストランキング</h1>
</div>

<div class="cast-rank">
  <div class="first-place">
    <a href="{{ route('cast.show', ['id' => $castRankings[0]->id]) }}">
      <figure><img src="{{ $castRankings[0]->avatars && @getimagesize($castRankings[0]->avatars[0]->path) ? $castRankings[0]->avatars[0]->path: '/assets/web/images/gm1/ic_default_avatar@3x.png' }}"></figure>
      <figcaption><span>♥♥{{ $castRankings[0]->nickname }}♥♥</span><span>({{ $castRankings[0]->age }})</span></figcaption>
    </a>
  </div>
  <div class="second_third">
    <div class="second">
      <a href="{{ route('cast.show', ['id' => $castRankings[1]->id]) }}">
        <figure><img src="{{ $castRankings[1]->avatars && @getimagesize($castRankings[1]->avatars[0]->path) ? $castRankings[1]->avatars[0]->path: '/assets/web/images/gm1/ic_default_avatar@3x.png' }}"></figure>
        <figcaption><span>♥♥{{ $castRankings[1]->nickname }}♥♥</span><span>({{ $castRankings[1]->age }})</span></figcaption>
      </a>
    </div>
    <div class="third">
      <a href="{{ route('cast.show', ['id' => $castRankings[2]->id]) }}">
        <figure><img src="{{ $castRankings[2]->avatars && @getimagesize($castRankings[2]->avatars[0]->path) ? $castRankings[2]->avatars[0]->path: '/assets/web/images/gm1/ic_default_avatar@3x.png' }}"></figure>
        <figcaption><span>♥♥{{ $castRankings[2]->nickname }}♥♥</span><span>({{ $castRankings[2]->age }})</span></figcaption>
      </a>
    </div>
  </div>
  <ul class="later">
    @foreach ($castRankings as $key => $cast_ranking)
      @if ($key >= 3)
      <li>
        <a href="{{ route('cast.show', ['id' => $cast_ranking->id]) }}">
          <span class="num">{{ $key + 1 }}</span>
          <img src="{{ ($cast_ranking->avatars && @getimagesize($cast_ranking->avatars[0]->path)) ? $cast_ranking->avatars[0]->path: '/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="">
          <span class="name">{{ $cast_ranking->nickname }}</span>
          <span class="age">({{ $cast_ranking->age }})</span>
        </a>
      </li>
      @endif
    @endforeach
  </ul>
</div>
@endsection

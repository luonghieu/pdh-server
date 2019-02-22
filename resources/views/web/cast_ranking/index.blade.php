@section('title', 'キャストランキング')
@section('web.extra_css')
<link rel="stylesheet" href="{{ mix('assets/web/css/gf_4.min.css') }}">
@endsection
@section('screen.id', 'gf4')
@extends('layouts.web')
@section('web.content')
<div class="page-header">
  <h1 class="text-bold">キャストランキング</h1>
</div>
@if ($castRankings->first())
<div class="cast-rank text-bold">
  @if ($castRankings->count() >= 1)
  <div class="first-place">
    <a href="{{ route('cast.show', ['id' => $castRankings[0]->id]) }}">
      <figure><img src="{{ $castRankings[0]->avatars && isset($castRankings[0]->avatars[0]) && $castRankings[0]->avatars[0]->thumbnail ? $castRankings[0]->avatars[0]->thumbnail: '/assets/web/images/gm1/ic_default_avatar@3x.png' }}"></figure>
      <figcaption>
        <div class="nickname-cast-rank">
          <span>{{ $castRankings[0]->nickname }}</span>
        </div>
        <span class="age-first">({{ $castRankings[0]->age }})</span>
      </figcaption>
    </a>
  </div>
  @endif
  <div class="second_third">
    @if ($castRankings->count() >= 2)
    <div class="second">
      <a href="{{ route('cast.show', ['id' => $castRankings[1]->id]) }}">
        <figure><img src="{{ $castRankings[1]->avatars && isset($castRankings[1]->avatars[0]) && $castRankings[1]->avatars[0]->thumbnail ? $castRankings[1]->avatars[0]->thumbnail: '/assets/web/images/gm1/ic_default_avatar@3x.png' }}"></figure>
        <figcaption>
          <div class="nickname-cast-rank">
            <span>{{ $castRankings[1]->nickname }}</span>
          </div>
          <span>({{ $castRankings[1]->age }})</span>
        </figcaption>
      </a>
    </div>
    @endif
    @if ($castRankings->count() >= 3)
    <div class="third">
      <a href="{{ route('cast.show', ['id' => $castRankings[2]->id]) }}">
        <figure><img src="{{ $castRankings[2]->avatars && isset($castRankings[2]->avatars[0]) && $castRankings[2]->avatars[0]->thumbnail ? $castRankings[2]->avatars[0]->thumbnail: '/assets/web/images/gm1/ic_default_avatar@3x.png' }}"></figure>
        <figcaption>
          <div class="nickname-cast-rank">
            <span>{{ $castRankings[2]->nickname }}</span>
          </div>
          <span>({{ $castRankings[2]->age }})</span>
        </figcaption>
      </a>
    </div>
    @endif
  </div>
  @if ($castRankings->count() >= 4)
  <ul class="later">
    @foreach ($castRankings as $key => $cast_ranking)
      @if ($key >= 3)
      <li>
        <a href="{{ route('cast.show', ['id' => $cast_ranking->id]) }}">
          <span class="num">{{ $key + 1 }}</span>
          <img src="{{ ($cast_ranking->avatars && isset($cast_ranking->avatars[0]) && $cast_ranking->avatars[0]->thumbnail) ? $cast_ranking->avatars[0]->thumbnail: '/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="">
          <span class="name">{{ $cast_ranking->nickname }}</span>
          <span class="age">({{ $cast_ranking->age }})</span>
        </a>
      </li>
      @endif
    @endforeach
  </ul>
  @endif
</div>
@endif
@endsection

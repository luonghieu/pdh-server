@section('title', 'プロフィール_編集')
@section('screen.id', 'gm1')
@section('screen.class', 'gm1-edit')

@extends('layouts.web')
@section('web.content')
<div class="cast-profile">
  <section class="profile-photo">
    <div class="profile-photo__top">
      @if ($profile['avatars'][0] && $profile['avatars'][0]['path'])
      <img class="init-image-radius" src="{{ $profile['avatars'][0]['path'] }}" alt="">
      @else
      <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
      @endif
    </div>
    <div class="profile-photo__list">
      <ul>
        @foreach ($profile['avatars'] as $avatar)
          @if ($avatar['path'])
          <li class="profile-photo__item">
            <a href="#"><img src="" alt=""></a>
          </li>
          @endif
        @endforeach
        <label class="profile-photo__item--add-button">
          <input type="file" id="image" hidden="">
          <img src="{{ asset('assets/web/images/gm1/add-button_bg.png') }}" alt="">
        </label>
      </ul>
    </div>
  </section>
  <!-- profile-photos -->
  <form id="update-profile" action="#" method="POST">
    {{ csrf_field() }}
    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">ひとこと</h2>
      </div>
      <div data-field="intro" class="help-block"></div>
      <div class="portlet-content">
        <textarea rows="2" onchange="limitMaxLength(this, 30, '30文字以内で入力してください')" id="intro">{{ $profile['intro'] }}</textarea>
      </div>
    </section>
    <!-- profile-word -->

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">自己紹介</h2>
      </div>
      <div  data-field="description" class="help-block"></div>
      <div class="portlet-content">
        <textarea rows="5" onchange="limitMaxLength(this, 1000, '1000文字以内で入力してください')" id="description">{{ $profile['description'] }}</textarea>
      </div>
    </section>
    <!-- profile-introduction -->

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">基本情報</h2>
      </div>
      <div class="portlet-content">
        <ul class="portlet-content__list">
          <div data-field="nickname" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">ニックネーム</p>
            <input type="text" onchange="limitMaxLength(this, 20, '20文字以内で入力してください')" id="nickname" value="{{ $profile['nickname'] }}">
          </li>
          <div data-field="gender" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">性別</p>
            <label class="time-input">
              <div class="selectbox" data-field="gender">
                <select dir="rtl" id="gender">
                  @foreach ($glossaries['genders'] as $gender)
                    @php
                      ($gender == $profile['gender']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $gender['id'] }}" {{ $selected }}>{{ $gender['name'] }}</option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <div data-field="date_of_birth" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">生年月日</p>
            <input type="date" id="date-of-birth" value="{{ $profile['date_of_birth'] }}">
          </li>
          <div data-field="height" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">身長</p>
            <label class="time-input">
              <div class="selectbox" data-field="height">
                <select dir="rtl" id="height">
                  @for ($height = 130; $height <= 200; $height++)
                    @php
                      ($height == $profile['height']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $height }}" {{ $selected }}>{{ $height }}</option>
                  @endfor
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <div data-field="body_type_id" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">体型</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="body-type-id">
                  @foreach ($glossaries['body_types'] as $bodyType)
                    @php
                      ($bodyType['id'] == $profile['body_type_id']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $bodyType['id'] }}" {{ $selected }}>
                      {{ $bodyType['name'] }}
                    </option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <div data-field="hometown_id" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">居住地</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="hometown-id">
                  @foreach ($glossaries['hometowns'] as $hometown)
                    @php
                      ($hometown['id'] == $profile['hometown_id']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $hometown['id'] }}" {{ $selected }}>
                      {{ $hometown['name'] }}
                    </option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <div data-field="job_id" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お仕事</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="job-id">
                  @foreach ($glossaries['jobs'] as $job)
                    @php
                      ($job['id'] == $profile['job_id']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $job['id'] }}" {{ $selected }}>
                      {{ $job['name'] }}
                    </option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <div data-field="drink_volume_type" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お酒</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="drink-volume-type">
                  @foreach ($glossaries['drink_volumes'] as $drinkVolume)
                    @php
                      ($drinkVolume['id'] == $profile['drink_volume_type']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $drinkVolume['id'] }}" {{ $selected }}>
                      {{ $drinkVolume['name'] }}
                    </option>
                  @endforeach
                </select>
                <i></i>
                <div class="help-block"></div>
              </div>
            </label>
          </li>
          <div data-field="smoking-type" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">タバコ</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="smoking-type">
                  @foreach ($glossaries['smokings'] as $smoking)
                    @php
                      ($smoking['id'] == $profile['smoking_type']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $smoking['id'] }}" {{ $selected }}>
                      {{ $smoking['name'] }}
                    </option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <div data-field="siblings_type" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">兄弟</p>
            <label class="time-input">
              <div class="selectbox">
                <select  dir="rtl" id="siblings-type">
                  @foreach ($glossaries['siblings'] as $sibling)
                    @php
                      ($sibling['id'] == $profile['siblings_type']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $sibling['id'] }}" {{ $selected }}>
                      {{ $sibling['name'] }}
                    </option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <div data-field="cohabitant_type" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">同居人</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="cohabitant-type">
                  cohabitant_type
                  @foreach ($glossaries['cohabitants'] as $cohabitant)
                    @php
                      ($cohabitant['id'] == $profile['cohabitant_type']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $cohabitant['id'] }}" {{ $selected }}>
                      {{ $cohabitant['name'] }}
                    </option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
        </ul>
      </div>
    </section>
    <!-- profile-word -->

    <div class="btn-l init-button"><button type="submit">完了</button></div>
    </div>
  </form>
@endsection
@section('web.script')
<script>
  //time_input--------------------------------------------
  var tiemButton = $(".button--green.time");
  tiemButton.on("change",function(){
    var thisButton = $(this);
    $(this).siblings().removeClass("active");
    $(this).toggleClass("active");
    if($(this).attr("id") != "time-input"){
      $(".time-input").css("display","none");
    }
  })
  $("#time-input").on("change",function(){
    if($("input[type='radio']:checked")){
      $(".time-input").css("display","flex");
    }
  })

  function limitMaxLength(target, len, err)
  {
    if( target.value.length > len )
    {
      target.value = target.value.substr(0, len);
      if( "undefined" != typeof(err) )
      {
        alert(err);
      }
    }
  }
</script>
@stop

@section('title', 'プロフィール_編集')
@section('screen.id', 'gm1')
@section('screen.class', 'gm1-edit')

@extends('layouts.web')
@section('web.extra')
<div class="modal_wrap">
  <input id="trigger4" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger4" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn4">
        <div class="select-box">
          <label for="trigger4" class="close_button" id="set-default-avatar">メインにする</label>
          @if (count($profile['avatars']) > 1)
          <label for="trigger4" class="close_button" id="delete-avatar">削除する</label>
          @endif
          <label for="trigger4" class="close_button" id="update-avatar">変更する</label>
          <input for="trigger4" class="close_button" type="file" accept="image/*" id="upload-btn" style="position: absolute; left: 99999px" />
        </div>
        <div class="cancel">
          <label for="trigger4" class="close_button fw">キャンセル</label>
        </div>
      </div>
    </div>
</div>

<div class="modal_wrap">
  <input id="trigger3" type="checkbox">
    <div class="modal_overlay">
      <label for="trigger3" class="modal_trigger"></label>
      <div class="modal_content modal_content-btn3">
        <div class="content-in" id="profile-message">
          <h2></h2>
        </div>
      </div>
    </div>
</div>
@endsection
@section('web.content')
<input type="hidden" id="name" value="{{ $profile['nickname'] }}" />
<input type="hidden" id="day" value="{{ $profile['date_of_birth'] }}" />
<input type="hidden" id="img" value="{{ count($profile['avatars']) }}" />

<section class="button-box" hidden="">
  <label for="trigger3" class="open_button button-settlement" id="profile-popup"></label>
</section>
<form id="update-profile" action="#" method="GET" enctype="multipart/form-data">
  {{ csrf_field() }}
  <div class="cast-profile">
    <section class="profile-photo">
      <div class="profile-photo__top">
        @if ($profile['avatars'] && @getimagesize($profile['avatars'][0]['thumbnail']))
          <img class="init-image-radius" src="{{ $profile['avatars'][0]['thumbnail'] }}" alt="">
        @else
        <img src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt="">
        @endif
      </div>
      <div class="profile-photo__list">
        <ul>
          @foreach ($profile['avatars'] as $avatar)
            @if (@getimagesize($avatar['thumbnail']))
            <div class="css-img">
              <label for="trigger4" class="open_button button-settlement js-img" id="{{ $avatar['id'] }}">
                <img type="file" id="valid" src="{{ $avatar['thumbnail'] }}" alt="">
              </label>
            </div>
            @endif
          @endforeach
          @if (count($profile['avatars']) < 10)
          <div class="css-img profile-photo__item--add-button">
            <label>
              <input type="file" accept="image/*" id="upload" style="position: absolute; left: 99999px" hidden=""/>
              <img src="{{ asset('assets/web/images/gm1/add-button_bg.png') }}" alt="">
            </label>
          </div>
          @endif
        </ul>
        <div class="image-error help-block"></div>
      </div>
    </section>
    <!-- profile-photos -->
    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">ひとこと</h2>
      </div>
      <div class="portlet-content">
        <textarea rows="2" id="intro" name="intro" placeholder="ひとこと設定されていません">{{ $profile['intro'] }}</textarea>
        <label data-field="intro" id="intro-error" class="error help-block" for="intro"></label>
      </div>
      <div data-field="intro" class="help-block"></div>
    </section>
    <!-- profile-word -->

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">自己紹介</h2>
      </div>
      <div class="portlet-content">
        <textarea rows="5" id="description" name="description" placeholder="自己紹介設定されていません">{{ $profile['description'] }}</textarea>
        <label data-field="description" id="description-error" class="error help-block" for="description"></label>
      </div>
    </section>
    <!-- profile-introduction -->

    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title">基本情報</h2>
      </div>
      <div class="portlet-content">
        <ul class="portlet-content__list">
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">ニックネーム</p>
            <input type="text" id="nickname" name="nickname" value="{{ $profile['nickname'] }}" >
          </li>
          <label data-field="nickname" id="nickname-error" class="error help-block" for="nickname"></label>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">性別</p>
            <label class="time-input">
              <div class="selectbox" data-field="gender">
                <select dir="rtl" id="gender">
                  <option value="" class="hidden">未設定</option>
                  @foreach ($glossaries['genders'] as $gender)
                    @php
                      (($gender['id'] == $profile['gender']) && !empty($profile['gender'])) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $gender['id'] }}" {{ $selected }}>{{ $gender['name'] }}</option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <div data-field="gender" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">生年月日</p>
            @php
              $max = \Carbon\Carbon::parse(now())->subYear(20);
            @endphp
            <input type="date" id="date-of-birth" name="date_of_birth" data-date="" max="{{ $max->format('Y-m-d') }}" data-date-format="YYYY年MM月DD日" value="{{ \Carbon\Carbon::parse($profile['date_of_birth'])->format('Y-m-d') }}">
            <i class="init-date-of-birth"></i>
          </li>
          <label data-field="date_of_birth" id="date-of-birth-error" class="error help-block" for="date-of-birth"></label>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">年齢</p>
            <span id="age">{{ $profile['age'] }}歳</span>
          </li>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">身長</p>
            <label class="time-input">
              <div class="selectbox" data-field="height">
                <select dir="rtl" id="height" name="height">
                  <option value="" class="hidden">未設定</option>
                  <option value="0" {{ ($profile['height'] == 0) ? ($selected = "selected='selected'") : ($selected = '') }}>
                    非公開
                  </option>
                  @for ($height = 130; $height <= 200; $height++)
                    @php
                      ($height === $profile['height']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $height }}" {{ $selected }}>{{ $height }}cm</option>
                  @endfor
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <label data-field="height" id="height-error" class="error help-block" for="height"></label>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">年収</p>
            <label class="time-input">
              <div class="selectbox" data-field="salary_id">
                <select dir="rtl" id="salary-id" name="salary_id">
                  <option value="" class="hidden">未設定</option>
                  @foreach ($glossaries['salaries'] as $salary)
                    @php
                      ($salary['id'] == $profile['salary_id']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $salary['id'] }}" {{ $selected }}>
                      {{ $salary['name'] }}
                    </option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <label data-field="height" id="height-error" class="error help-block" for="height"></label>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">体型</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="body-type-id" name="body_type_id">
                  <option value="" class="hidden">未設定</option>
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
          <label data-field="body_type_id" id="body-type-id-error" class="error help-block" for="body-type-id"></label>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">居住地</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="prefecture-id">
                  <option value="" class="hidden">未設定</option>
                  @foreach ($glossaries['prefectures'] as $prefecture)
                    @php
                      ($prefecture['id'] == $profile['prefecture_id']) ? ($selected = "selected='selected'") : ($selected = '')
                    @endphp
                    <option value="{{ $prefecture['id'] }}" {{ $selected }}>
                      {{ $prefecture['name'] }}
                    </option>
                  @endforeach
                </select>
                <i></i>
              </div>
            </label>
          </li>
          <div data-field="prefecture_id" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">出身地</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="hometown-id">
                  <option value="" class="hidden">未設定</option>
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
          <div data-field="hometown_id" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お仕事</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="job-id">
                  <option value="" class="hidden">未設定</option>
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
          <div data-field="job_id" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">お酒</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="drink-volume-type">
                  <option value="" class="hidden">未設定</option>
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
              </div>
            </label>
          </li>
          <div data-field="drink_volume_type" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">タバコ</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="smoking-type">
                  <option value="" class="hidden">未設定</option>
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
          <div data-field="smoking-type" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">兄弟</p>
            <label class="time-input">
              <div class="selectbox">
                <select  dir="rtl" id="siblings-type">
                  <option value="" class="hidden">未設定</option>
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
          <div data-field="siblings_type" class="help-block"></div>
          <li class="portlet-content__item">
            <p class="portlet-content__text--list">同居人</p>
            <label class="time-input">
              <div class="selectbox">
                <select dir="rtl" id="cohabitant-type">
                  <option value="" class="hidden">未設定</option>
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
          <div data-field="cohabitant_type" class="help-block"></div>
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
tiemButton.on("change", function() {
  var thisButton = $(this);

  $(this).siblings().removeClass("active");
  $(this).toggleClass("active");

  if ($(this).attr("id") != "time-input") {
    $(".time-input").css("display", "none");
  }
})
$("#time-input").on("change", function() {
  if ($("input[type='radio']:checked")) {
    $(".time-input").css("display", "flex");
  }
});
</script>
@stop

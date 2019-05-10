@section('title', 'プロフィール')
@section('screen.id', 'gm1')

@extends('layouts.web')
@section('web.extra')
<div class="modal_wrap">
  <input id="trigger-profile" type="checkbox">
  <div class="modal_overlay">
    <label for="trigger-profile" class="modal_trigger" id="profile-popup"></label>
    <div class="modal_content modal_content-btn3">
      <div class="content-in" id="profile-message">
        <h2></h2>
      </div>
    </div>
  </div>
</div>
@endsection
@section('web.content')
<div class="cast-profile">
  <section class="profile-photo">
    <div class="profile-photo__top">
      @if ($profile['avatars'] && @getimagesize($profile['avatars'][0]['path']))
      <img class="init-image-radius lazy" data-src="{{ $profile['avatars'][0]['path'] }}" alt="">
      @else
      <img class="init-image-radius lazy" data-src="{{ asset('assets/web/images/ge1/user_icon.svg') }}" alt="">
      @endif
    </div>
    <div class="profile-photo__list">
      <ul>
        @foreach ($profile['avatars'] as $avatar)
          @if (@getimagesize($avatar['path']))
          <li class="css-img"><img class="lazy" data-src="{{ $avatar['path'] }}" alt=""></li>
          @else
          <li class="css-img"><img class="lazy" data-src="{{ asset('assets/web/images/gm1/ic_default_avatar@3x.png') }}" alt=""></li>
          @endif
        @endforeach
      </ul>
    </div>
  </section>
  <!-- profile-photos -->

  <section class="portlet">
    <div class="portlet-header">
      <h2 class="portlet-header__title">ひとこと</h2>
    </div>
    <div class="portlet-content">
      @if (!$profile['intro'])
      <p class="portlet-header__title">ひとこと設定されていません</p>
      @else
      <p class="portlet-content__text">{{ $profile['intro'] }}</p>
      @endif
    </div>
  </section>
  <!-- profile-word -->

  <section class="portlet">
    <div class="portlet-header">
      <h2 class="portlet-header__title">自己紹介</h2>
    </div>
    <div class="portlet-content">
      @if (!$profile['description'])
      <p class="portlet-header__title">自己紹介設定されていません</p>
      @else
      <p class="portlet-content__text">{!! strip_tags(nl2br($profile['description']), '<br>') !!}</p>
      @endif
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
          <p class="portlet-content__value">{{ $profile['nickname'] or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">電話番号<span class="text-phone">※相手には表示されません</span></p>
          <p class="portlet-content__value">{{ (!$profile['phone']) ? '未設定' : $profile['phone']}}</p>
        </li>
        @php
        switch ($profile['gender']) {
            case '0':
                $gender = '非公開';
                break;
            case '1':
                $gender = '男性';
                break;
            case '2':
                $gender = '女性';
                break;

            default:
                $gender = '未設定';
                break;
        }
        @endphp
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">性別</p>
          <p class="portlet-content__value">{{ $gender }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">生年月日</p>
          <p class="portlet-content__value">
            {{ (!$profile['date_of_birth']) ? '未設定' : \Carbon\Carbon::parse($profile['date_of_birth'])->format('Y年m月d日') }}
          </p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">年齢</p>
          <p class="portlet-content__value">{{ $profile['age'] or '未設定' }}{{ (!$profile['age']) ? '' : '歳' }}</p>
        </li>
        @php
        switch ($profile['height']) {
            case '0':
                $height = '非公開';
                break;

            default:
                $height = (!$profile['height']) ? '' : $profile['height'] . 'cm';
                break;
        }
        @endphp
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">身長</p>
          <p class="portlet-content__value">{{ $height or '未設定' }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">年収</p>
          <p class="portlet-content__value">
            {{ (!$profile['salary']) ? '未設定' : $profile['salary'] }}
          </p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">体型</p>
          <p class="portlet-content__value">
            {{ (!$profile['body_type']) ? '未設定' : $profile['body_type'] }}
          </p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">ご利用エリア</p>
          <p class="portlet-content__value">
            {{ (!$profile['prefecture']) ? '未設定' : $profile['prefecture'] }}
          </p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">出身地</p>
          <p class="portlet-content__value">{{ (!$profile['hometown']) ? '未設定' : $profile['hometown'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">お仕事</p>
          <p class="portlet-content__value">{{ (!$profile['job']) ? '未設定' : $profile['job'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">お酒</p>
          <p class="portlet-content__value">{{ (!$profile['drink_volume']) ? '未設定' : $profile['drink_volume'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">タバコ</p>
          <p class="portlet-content__value">{{ (!$profile['smoking']) ? '未設定' : $profile['smoking'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">兄弟</p>
          <p class="portlet-content__value">{{ (!$profile['siblings']) ? '未設定' : $profile['siblings'] }}</p>
        </li>
        <li class="portlet-content__item">
          <p class="portlet-content__text--list">同居人</p>
          <p class="portlet-content__value">{{ (!$profile['cohabitant']) ? '未設定' : $profile['cohabitant'] }}</p>
        </li>
      </ul>
    </div>
  </section>
  <!-- profile-word -->
  <div class="timeline">
    <section class="portlet">
      <div class="portlet-header">
        <h2 class="portlet-header__title title-shifts">タイムライン</h2>
      </div>
      <div class="portlet-content--timeline">
        <div class="timeline-list">
          <div class="timeline-item">
            <div class="user-info">
              <div class="user-info__profile">
                <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg" alt="">
              </div>
              <div class="user-info__text">
                <div class="user-info__top">
                  <p>Ayaka</p>
                  <p>22</p>
                </div>
                <div class="user-info__bottom">
                  <p>新宿</p>
                  <p>16時間前</p>
                </div>
              </div>
            </div>
            <div class="timeline-content">
              <div class="timeline-article">
                <div class="timeline-article__text">今日今から一緒に飲めるかた！
                  <br>可愛いまどかちゃんと一緒にいます❤︎
                  <br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした 事がある。</div>
              </div>
              <div class="timeline-images">
                <div class="timeline-images__list">
                  <div class="timeline-images__item">
                    <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                  </div>
                </div>
              </div>
              <div class="timeline-like">
                <button class="timeline-like__icon">
                  <img src="./assets/web/images/common/like-icon.svg" alt="">
                </button>
                <p class="timeline-like__sum">113</p>
              </div>
            </div>
          </div>
          <div class="timeline-item">
            <div class="user-info">
              <div class="user-info__profile">
                <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg" alt="">
              </div>
              <div class="user-info__text">
                <div class="user-info__top">
                  <p>Ayaka</p>
                  <p>22</p>
                </div>
                <div class="user-info__bottom">
                  <p>新宿</p>
                  <p>3日</p>
                </div>
              </div>
            </div>
            <div class="timeline-content">
              <div class="timeline-article">
                <div class="timeline-article__text">一緒に楽しく飲みましょう！と思える人がいいな-</div>
              </div>
              <div class="timeline-like">
                <button class="timeline-like__icon">
                  <img src="./assets/web/images/common/like-icon.svg" alt="">
                </button>
                <p class="timeline-like__sum">54</p>
              </div>
            </div>
          </div>
          <div class="timeline-item">
            <div class="user-info">
              <div class="user-info__profile">
                <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg" alt="">
              </div>
              <div class="user-info__text">
                <div class="user-info__top">
                  <p>カトリーヌまどか@今日は気分がいい…</p>
                  <p>22歳</p>
                </div>
                <div class="user-info__bottom">
                  <p>新宿</p>
                  <p>4月2日</p>
                </div>
              </div>
            </div>
            <div class="timeline-content">
              <div class="timeline-article">
                <div class="timeline-article__text">今日今から一緒に飲めるかた！
                  <br>可愛いまどかちゃんと一緒にいます❤︎
                  <br>親譲りの無鉄砲で小供の時から損ばかりしている。小学校に居る時分学校の二階から飛び降りて一週間ほど腰を抜かした 事がある。</div>
              </div>
              <div class="timeline-images">
                <div class="timeline-images__list">
                  <div class="timeline-images__item">
                    <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg">
                  </div>
                </div>
              </div>
              <div class="timeline-like">
                <button class="timeline-like__icon">
                  <img src="./assets/web/images/common/like-icon.svg" alt="">
                </button>
                <p class="timeline-like__sum">407</p>
              </div>
            </div>
          </div>
          <div class="timeline-item">
            <div class="user-info">
              <div class="user-info__profile">
                <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg" alt="">
              </div>
              <div class="user-info__text">
                <div class="user-info__top">
                  <p>Ayaka</p>
                  <p>22</p>
                </div>
                <div class="user-info__bottom">
                  <p>新宿</p>
                  <p>3日</p>
                </div>
              </div>
            </div>
            <div class="timeline-content">
              <div class="timeline-article">
                <div class="timeline-article__text">一緒に楽しく飲みましょう！と思える人がいいな-</div>
              </div>
              <div class="timeline-like">
                <button class="timeline-like__icon">
                  <img src="./assets/web/images/common/like-icon.svg" alt="">
                </button>
                <p class="timeline-like__sum">54</p>
              </div>
            </div>
          </div>
          <div class="timeline-item">
            <div class="user-info">
              <div class="user-info__profile">
                <img src="https://i.pinimg.com/originals/4f/32/90/4f329022530b11cbbec602b5288c21f8.jpg" alt="">
              </div>
              <div class="user-info__text">
                <div class="user-info__top">
                  <p>Ayaka</p>
                  <p>22</p>
                </div>
                <div class="user-info__bottom">
                  <p>新宿</p>
                  <p>3日</p>
                </div>
              </div>
            </div>
            <div class="timeline-content">
              <div class="timeline-article">
                <div class="timeline-article__text">一緒に楽しく飲みましょう！と思える人がいいな-</div>
              </div>
              <div class="timeline-like">
                <button class="timeline-like__icon timeline-like__icon--clicked">
                  <img src="./assets/web/images/common/like-icon.svg" alt="">
                </button>
                <p class="timeline-like__sum">54</p>
              </div>
            </div>
          </div>
        </div>
        <div class="timeline-more">
          <p>さらに見る</p>
        </div>
      </div>
    </section>
  </div>
  <div class="btn-l edit-user-profile"><a href="{{ route('profile.edit') }}">修正</a></div>
</div>
@endsection

@section('web.script')
<script>
    $(function () {
      var popup_profile = window.sessionStorage.getItem('popup_profile');

      if (popup_profile) {
        $('#profile-popup').trigger('click');
        $('#profile-message h2').html(popup_profile);

        window.sessionStorage.removeItem('popup_profile');

        $('#profile-popup').on('click', function () {
          var checked = $('#trigger-profile').prop('checked');

          if (checked == false) {
            return false;
          }

          return true;
        });
      }
    });
  </script>
@endsection

@section('title', 'Cheers Rating')
@section('controller.id', 'index-rating-controller')
@section('screen.id', 'go1')
@extends('layouts.web')
@section('web.extra')

@endsection
@section('web.content')
    <div class="cast-profile">
        <section class="profile-photo">
            <div class="profile-photo_top"><img src="assets/images/go1/cast1.png" alt=""></div>
            <h2>Satomi♥♥(21)</h2>
            <p>2018年01月23日(土)</p>
            <p>13:00〜15:00</p>
        </section>
    </div>
    <!-- profile-photos -->
    <section class="evaluation-box">
        <ul class="">
            <li><p class="label1">満足度</p>
                <span class="star-rating">
            <input type="radio" name="rating" value="1"><i></i>
            <input type="radio" name="rating" value="2"><i></i>
            <input type="radio" name="rating" value="3"><i></i>
            <input type="radio" name="rating" value="4"><i></i>
            <input type="radio" name="rating" value="5"><i></i>
          </span>
            </li>
            <li><p class="label2">ルックス・<br>身だしなみ</p>
                <span class="star-rating">
            <input type="radio" name="rating2" value="1"><i></i>
            <input type="radio" name="rating2" value="2"><i></i>
            <input type="radio" name="rating2" value="3"><i></i>
            <input type="radio" name="rating2" value="4"><i></i>
            <input type="radio" name="rating2" value="5"><i></i>
          </span>
            </li>
            <li><p class="label3">愛想・気遣い</p>
                <span class="star-rating">
            <input type="radio" name="rating3" value="1"><i></i>
            <input type="radio" name="rating3" value="2"><i></i>
            <input type="radio" name="rating3" value="3"><i></i>
            <input type="radio" name="rating3" value="4"><i></i>
            <input type="radio" name="rating3" value="5"><i></i>
          </span>
            </li>
        </ul>
        <div>
            <textarea class="form" name="example" cols="50" rows="10" wrap="soft" placeholder="よろしければ評価内容をご入力ください"></textarea>
        </div>
    </section>


    <section class="settlement-confirm">
        <button type="button" class="button button-settlement">評価する</button>
    </section>
@endsection
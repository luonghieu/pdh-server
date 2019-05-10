@section('title', 'Timeline create')
@section('controller.id', 'time-line-create-controller')

@extends('layouts.web')
@section('web.extra')
    <div class="modal_wrap">
        <input id="add-location" type="checkbox">
        <div class="modal_overlay">
            <label for="trigger2" class="modal_trigger"></label>
            <div class="modal_content modal_content-btn2">
                <div class='position-box'>
                    <div class='position-box__close' onclick="document.getElementById('add-location').click()"></div>
                    <div class='position-box__head'>チェックイン</div>
                    <div class='position-box__body'>
                        <input id='positionInput' type='text' placeholder='どこにいますか?'>
                    </div>
                    <div class='position-box__foot'>
                        <button id='positionOk'>確認</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('web.content')
    <div class="timeline">
        <section class="timeline-message">
            <div class="timeline-message__header">
                <div class="user-info">
                    <div class="user-info__profile">
                        <img src="/assets/web/images/timeline/timeline-profile-img_003.jpg" alt="">
                    </div>
                    <div class="user-info__text">
                        <div class="user-info__top">
                            <p>Ayaka</p>
                            <p>22</p>
                        </div>
                        <div class="user-info__bottom">
                            <p></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="timeline-message__content">
                <div class="timeline-edit">
                    <div class="timeline-edit__area" contenteditable="true"></div>
                </div>
            </div>
        </section>
        <div class="timeline-edit__input">
            <label class="timeline-edit-pic">
                <img src="{{ asset('assets/web/images/common/picture.svg') }}">
                <input type="file" style="display: none" name="image" accept="image/*">
            </label>
            <label class="timeline-edit-camera">
                <img src="{{ asset('assets/web/images/common/camera.svg') }}">
                <input type="file" style="display: none" name="image-camera" accept="image/*" capture="camera">
            </label>
            <label class="timeline-edit-position">
                <img src="{{ asset('assets/web/images/common/position.svg') }}" onclick="document.getElementById('add-location').click()">
                <input type="button" style="display: none">
            </label>
            <label class="timeline-edit-sum">
                <p class="timeline-edit-sum__text">1</p>
            </label>
        </div>
    </div>
@endsection
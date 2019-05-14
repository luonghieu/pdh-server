@section('title', 'Timeline create')
@section('controller.id', 'time-line-create-controller')

@extends('layouts.web')
@section('web.extra')
    <div class="modal_wrap">
        <input id="add-location" type="checkbox">
        <div class="modal_overlay">
            <label for="add-location" class="modal_trigger"></label>
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
    <div class="modal_wrap">
        <input id="del-post-timeline" type="checkbox">
        <div class="modal_overlay">
            <label for="del-post-timeline" class="modal_trigger"></label>
            <div class="modal_content modal_content-btn2">
                <div class="text-box">
                    <h2>削除しますか？</h2>
                </div>
                <div class="close_button-box">
                    <div class="close_button-block">
                        <label for="del-post-timeline" class="close_button left">キャンセル</label>
                    </div>
                    <div class="close_button-block">
                        <a href="{{ route('web.timelines.index') }}"><label class="close_button right">削除</label></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('web.content')
    <div class="timeline">
        <div class="page-title">
            <div><button type="reset" class="btn_cancel">キャンセル</button></div>
            <div><button type="submit" class="btn_submit" id="timeline-btn-submit">つぶやく</button></div>
        </div>
        <section class="timeline-message">
            <div class="timeline-message__header">
                <div class="user-info">
                    <div class="user-info__profile">
                        <img src="{{ (Auth::user()->avatars()->first()) ? Auth::user()->avatars()->first()->path : '/assets/web/images/gm1/ic_default_avatar@3x.png' }}" alt="">
                    </div>
                    <div class="user-info__text">
                        <div class="user-info__top">
                            <p>{{ Auth::user()->nickname }}</p>
                            <p>{{ Auth::user()->age }}歳</p>
                        </div>
                        <div class="user-info__bottom">
                            <p></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="timeline-message__content">
                <div class="timeline-edit" >
                    <div class="timeline-edit__area" contenteditable="false">
                        <div class="timeline-edit__text" placeholder="いま何してる？" contenteditable="true"></div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="create-timeline-user-id" value="{{ Auth::user()->id }}">
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

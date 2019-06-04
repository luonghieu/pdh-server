@section('title', 'タイムライン')
@section('controller.id', 'time-line-index-controller')

@extends('layouts.web')
@section('web.content')
    <div class="frozen">
        <div class="frozen-content">
            <div class="frozen-content__logo">
                <img src="{{ asset('assets/web/images/common/icon-warning_001.svg') }}">
            </div>
            <div class="frozen-content__title">このアカウントは
                <br>凍結されています</div>
            <div class="frozen-content__text">
                <p>Cheersのルールに違反する行為が発覚したため、アカウントが停止されました。</p>
                <p>ご不明点がある場合はお問い合わせフォームよりお問い合わせください。</p>
            </div>
            <div class="frozen-content__link">
                <a href="{{ route('points.history') }}">ポイント購入履歴</a>
                <a href="/service/contact">お問い合わせはこちら</a>
            </div>
        </div>
    </div>
@endsection


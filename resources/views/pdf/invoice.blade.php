<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: Ja;
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/ja.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: Ja;
            font-weight: bold;
            src: url('{{ storage_path('fonts/ja.ttf') }}') format('truetype');
        }

        body {
            font-family: Ja !important;
        }
    </style>
</head>
<body>
<div class="container" style="padding: 1em;">
    <div class="header">
        <div class="lef" style="float: left; padding: 10px; border-bottom: 1px solid black">
            No. {{$no}}
        </div>
        <div class="right" style="float: right; padding: 10px; border-bottom: 1px solid black">
            {{ \Carbon\Carbon::parse($created_at)->format('Y年m月d日') }}
        </div>
        <div class="clear" style="clear: both"></div>
    </div>
    <h1 style="text-align: center; font-size: 48px;">領収書</h1>
    <div class="content">
        <div class="name" style="display:block; margin-left: 40px; border-bottom: 1px solid black; width: 300px;
            padding-bottom: 10px;">
            @if ($name)
            <span>{{ $name }} 様</span>
            @else
            <span>　　　　　様</span>
            @endif
        </div>
        <div class="amount" style="display: table; width: 100%; margin-top: 30px;">
            <div style="display: table-cell; vertical-align: middle;">
                <span style="display: block; padding: 15px; width: 250px; height: 30px; text-align: center;
                background-color: #d4e1f5; font-weight: bold; font-size: 24px; margin-left: auto; margin-right: auto;
">￥ {{ number_format($amount) }} -</span>
            </div>
        </div>
        <div class="info" style="display: table; width: 100%; margin-top: 30px;">
            <div style="display: table-cell; vertical-align: middle;">
                <div class="text" style="width: 320px; margin-right: auto; margin-left: auto;">
                    <span style="display: block; font-size: 24px">
                    @if($content)
                    但し{{ $content }}飲食代として
                    @else
                    但し　　　　　飲食代として
                    @endif
                    </span>
                    <span style="display: block; font-size: 24px">上記正に領収いたしました</span>
                </div>
            </div>
        </div>
    </div>
    <div class="footer" style="display: table; width: 100%; margin-top: 30px;">
        <div style="display: table-cell; vertical-align: middle;">
            <div class="text" style="width: 320px; margin-right: auto; margin-left: auto; white-space: nowrap;">
                <span style="display: block; font-size: 24px;">〒160−0023</span>
                <span style="display: block; font-size: 24px;">東京都新宿区西新宿1−22−2 サンエービル3階</span>
                <span style="display: block; font-size: 24px;">株式会社ネオラボ</span>
                <span style="display: block; font-size: 24px">TEL：03-5908-8422</span>
            </div>
        </div>
    </div>
</div>
</body>
</html>

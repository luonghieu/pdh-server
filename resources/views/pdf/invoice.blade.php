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
<body style="background: white !important;">
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
            padding-bottom: 10px; word-break: break-word;">
            @if ($name)
            <span>{{ $name }} 様</span>
            @else
            <span>　　　　　様</span>
            @endif
        </div>
        <div class="amount" style="display: table; width: 100%; margin-top: 30px;">
            <div style="display: table-cell; vertical-align: middle;">
                <span style="display: block; padding: 15px; width: 250px; height: 30px; text-align: center;
                background-color: #d4e1f5; font-weight: bold; font-size: 24px; margin-left: auto; margin-right: auto;">
              ￥ {{ number_format($amount) }} -</span>
            </div>
        </div>
        <div class="info" style="display: table; width: 150px; margin-top: 30px; margin-left: 144px;">
            <div style="display: table-cell; vertical-align: middle;word-wrap: break-word;">
                <div class="text">
                    <span style="display: block; width: 500px; font-size:24px; word-wrap: break-word;">
                    @if($content)
                    但し{{ $content }}として
                    @else
                    但し　　　　　として
                    @endif
                    </span>
                    <span style="display: block; font-size: 24px">上記正に領収いたしました</span>
                </div>
            </div>
        </div>
    </div>
    <div class="footer" style="display: table; width: 100%; margin-top: 30px; margin-left: 17px;">
        <div style="display: table-cell; vertical-align: middle;">
            <div class="text" style="width: 320px; margin-right: auto; margin-left: auto; white-space: nowrap;">
                <span style="display: block; font-size: 24px;">〒164-0014</span>
                <span style="display: block; font-size: 24px;">東京都中野区南台2丁目3番6号</span>
                <span style="display: block; font-size: 24px;">Cheers運営局</span>
                <span style="display: block; font-size: 24px">Mail：support@cheers.style</span>
            </div>
        </div>
    </div>
</div>
</body>
</html>

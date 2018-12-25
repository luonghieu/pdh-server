<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<Response>
    <Say language="ja-jp" voice="alice">
        こちらはチアーズ、うんえいきょくです。
        <break strength="x-strong"/>
        おきゃくさまの認証コードは。
        <break strength="x-strong"/>
    </Say>
    @foreach($codes as $code)
        <Say language="ja-jp" voice="alice">
            <prosody rate="20%" pitch="x-high" volume="x-loud">{{ $code }}</prosody>
            <break strength="strong" time="500ms"/>
        </Say>
    @endforeach
    <Say language="ja-jp" voice="alice">
        です。
        <break strength="x-strong"/>
    </Say>
</Response>
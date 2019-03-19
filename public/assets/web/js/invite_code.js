$(document).ready(function(){
    var inviteCode = $('#invite-code').text();

    $('.btn-invite-via-line').on('click', function () {
        let message = '【招待コード：'+inviteCode+'】\n' +
            '登録から1週間以内にご利用いただくと2時間以上のご利用で1時間無料キャンペーン中！\n' +
            'また、会員登録時に招待コードを入力すると、2回目以降にCheersで使える10,000Pをプレゼント！\n' +
            'ダブルでお得！✨\n' +
            '\n' +
            '会員登録時に、忘れずに招待コードを入力してください。\n' +
            '\n' +
            'ご登録はこちら\n' +
            '👉'+window.location.origin;
        let encodeMessage = encodeURI(message);
        window.location.href = 'line://msg/text/?'+encodeMessage;
    })
});
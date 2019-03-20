@section('title', 'クレジットカード登録')
@section('screen.id', 'gl1')
@extends('layouts.tc')
@section('web.content')
<div class="border-bottom header-webview">
  <div class="btn-back header-item">
    <a href="#"><img src="{{ asset('/assets/web/images/card/back.png') }}" alt=""></a>
  </div>
  <div class="title-main header-item">
    <span>クレジットカード登録</span>
  </div>
</div>
<div class="image-main">
  <img src="{{ asset('/assets/web/images/card/card_brands.png') }}" alt="">

  <p>＋その他各社クレジットカード</p>
  <div class="urgent-announcement">
    <% urgent_announcement %>
  </div>
</div>
<div class="notify" id="notify">
  <span></span>
</div>
<div class="content">
  <form name="myFORM" method="POST" action="connect.pl" class="tc-form">
    <div class="hide-content">
      <% option %>
    </div>
    <INPUT TYPE="HIDDEN" NAME="i" VALUE="<% FORM{i} %>">
    <INPUT TYPE="HIDDEN" NAME="rebill_param_id" VALUE="<% FORM{rebill_param_id} %>">
    <INPUT TYPE="HIDDEN" NAME="clientip" VALUE="<% FORM{clientip} %>">
    <INPUT TYPE="HIDDEN" NAME="sendid" VALUE="<% FORM{sendid} %>">
    <INPUT TYPE="HIDDEN" NAME="sendpass" VALUE="<% FORM{sendpass} %>">
    <INPUT TYPE="HIDDEN" NAME="redirect_url" VALUE="<% FORM{redirect_url} %>">
    <INPUT TYPE="HIDDEN" NAME="redirect_back_url" VALUE="<% FORM{redirect_back_url} %>">
    <INPUT TYPE="HIDDEN" NAME="attribute" VALUE="<% FORM{attribute} %>">
    <INPUT TYPE="HIDDEN" NAME="option" VALUE="<% FORM{option} %>">
    <INPUT TYPE="HIDDEN" NAME="option2" VALUE="<% FORM{option2} %>">
    <INPUT TYPE="HIDDEN" NAME="send_pass_bool" VALUE="<% FORM{send_pass_bool} %>">
    <INPUT TYPE="HIDDEN" NAME="non_duplication_id" VALUE="<% FORM{non_duplication_id} %>">
    <INPUT TYPE="HIDDEN" NAME="token" VALUE="<% FORM{token} %>">
    <INPUT TYPE="HIDDEN" NAME="telno" VALUE="<% FORM{usrtel} %>">
    <INPUT TYPE="HIDDEN" NAME="email" VALUE="<% FORM{usrmail} %>">
    <INPUT TYPE="HIDDEN" NAME="email2" VALUE="<% FORM{usrmail} %>">
    <div class="sub-title">
      <p>カード情報</p>
    </div>
  <div class="card-number border-bottom">
    <div class="card-outside">
      <div class="left"><span>カード番号</span></div>
      <div class="right number">
        <input type="tel" pattern="[0-9]*" maxlength="16" name="cardnumber" id="number-card" value="" placeholder="0000 0000 0000 0000" required>
      </div>
    </div>
  </div>
  <div class="clear"></div>
  <div class="expiration-date border-bottom">
    <div class="card-outside">
      <div class="left"><span>有効期限</span></div>
      <div class="right date-select">
        <select name="expmm" id="month-select">
          <OPTION VALUE="01" <% FORM{expmm_01} %>>01月
          <OPTION VALUE="02" <% FORM{expmm_02} %>>02月
          <OPTION VALUE="03" <% FORM{expmm_03} %>>03月
          <OPTION VALUE="04" <% FORM{expmm_04} %>>04月
          <OPTION VALUE="05" <% FORM{expmm_05} %>>05月
          <OPTION VALUE="06" <% FORM{expmm_06} %>>06月
          <OPTION VALUE="07" <% FORM{expmm_07} %>>07月
          <OPTION VALUE="08" <% FORM{expmm_08} %>>08月
          <OPTION VALUE="09" <% FORM{expmm_09} %>>09月
          <OPTION VALUE="10" <% FORM{expmm_10} %>>10月
          <OPTION VALUE="11" <% FORM{expmm_11} %>>11月
          <OPTION VALUE="12" <% FORM{expmm_12} %>>12月
        </select>

        <select name="expyy" id="year-select">
          <% year %>
        </select>
      </div>
    </div>
  </div>
  <div class="sub-title secondary">
    <p>セキュリティコード</p>
  </div>
  <div class="security-code border-bottom">
    <div class="security-code-outside">
      <div class="left">
        <img src="{{ asset('/assets/webview/images/ic_card_cvv.png') }}" alt="" >
      </div>
      <div id="cvv-field" class="right">
        <INPUT TYPE="TEL" NAME="code_number" class="textbox2" MAXLENGTH="4" VALUE="" autocomplete="off" style="ime-mode: disabled;"
          placeholder="３桁または４桁の数字" required>
      </div>
    </div>
  </div>
  <div class="tc-notice">
    <p>※クレジットカードの裏に記載の3桁数字</p>
    <p>※アメックスの場合は表の右上に記載の4桁数字</p>
  </div>
  <div class="sub-title secondary">
    <p>お名前</p>
  </div>
  <div class="security-code border-bottom card-name">
    <div class="security-code-outside">
      <div class="right">
        <input type="text" placeholder="TARO YAMADA" name="username" id="username" maxlength="50" required>
      </div>
    </div>
  </div>
  <div class="tc-notice">
    <p>※クレジットカードに記載されている名前を半角英数字で入力してください。</p>
    <p>※ご自身以外の名義のカードはご利用いだだけません。</p>

    <div class="sub-title secondary">
      <p>確認事項</p>
    </div>
    <div class="maintenance-info">
      <div class="maintenance-info1">
        <% maintenance_info1 %>
      </div>
      <div class="maintenance-info2">
        <% maintenance_info2 %>
      </div>
    </div>
    <p>・エムアイカードまたはビューカードをご利用の方は<a href="https://secure.telecomcredit.co.jp/notes/credituser_tc2.html" target="_blank">重要なお知らせ</a>を確認ください。</p>
    <p>・個人情報の取扱については必ず<a href="https://secure.telecomcredit.co.jp/notes/credituser_tc2.html" target="_blank">こちらをお読みください。</a></p>
    <p>・個人情報の取扱をご同意の上、「登録する」を押してください。</p>
  </div>

  <button type="submit" class="form_footer ct-button">登録する</button>
  </form>
</div>
@endsection

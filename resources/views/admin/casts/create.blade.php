@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-8">
      <div class="row">
        <!--/col-->
        <div class="col-12 ">
          <div class="panel panel-default">
            <div class="panel-heading" data-original-title>
              <h2><i class="fa fa-user"></i><span class="break"></span>キャスト新規登録画面</h2>
            </div>
            <div class="panel-body">
              <div class="wrapper" >
                <form method="POST" action="{{ route('admin.casts.store') }}" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="list-avatar">
                    <ul>
                      <li>
                        <img src="/assets/admin/img/default_avatar.png" id="front_side-img" />
                      </li>
                      <li>
                        おもて面
                      </li>
                      <li>
                        <input type="file" name="front_side" id="front_side" />
                      </li>
                      <li>
                        @if ($errors->has('front_side'))
                          <div class="error pull-left">
                            <span>{{ $errors->first('front_side') }}</span>
                          </div>
                        @endif
                      </li>
                    </ul>
                  </div>
                  <div class="list-avatar">
                    <ul>
                      <li>
                        <img src="/assets/admin/img/default_avatar.png" id="back_side-img" />
                      </li>
                      <li>
                        うら面
                      </li>
                      <li>
                        <input type="file" name="back_side" id="back_side" />
                      </li>
                      <li>
                        @if ($errors->has('back_side'))
                          <div class="error pull-left">
                            <span>{{ $errors->first('back_side') }}</span>
                          </div>
                        @endif
                      </li>
                    </ul>
                  </div>
                  <div class="info-table coupon">
                    <table class="table table-bordered">
                      <!--  table-striped -->
                      <tr>
                        <th><span class="color-error">*</span>ログインID (メールアドレス)</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-6">
                              <input type="email" name="email" placeholder="メールアドレス" value="{{ old('email') }}">
                              @if ($errors->has('email'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('email') }}</span>
                                </div>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th><span class="color-error">*</span>パスワード(6桁以上)</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-6">
                              <input type="password" name="password" placeholder="パスワード" value="">
                              @if ($errors->has('password'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('password') }}</span>
                                </div>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th><span class="color-error">*</span>氏名</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3">
                              <input type="text" name="lastname" placeholder="姓"  value="{{ old('lastname') }}">
                              @if ($errors->has('lastname'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('lastname') }}</span>
                                </div>
                              @endif
                            </div>
                            <div class="col-sm-3">
                              <input type="text" name="firstname" value="{{ old('firstname') }}" placeholder="名">
                              @if ($errors->has('firstname'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('firstname') }}</span>
                                </div>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th><span class="color-error">*</span>ふりがな</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3">
                              <input type="text" name="lastname_kana" placeholder="せい"  value="{{ old('lastname_kana') }}">
                              @if ($errors->has('lastname_kana'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('lastname_kana') }}</span>
                                </div>
                              @endif
                            </div>
                            <div class="col-sm-3">
                              <input type="text" name="firstname_kana" value="{{ old('firstname_kana') }}" placeholder="めい">
                              @if ($errors->has('firstname_kana'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('firstname_kana') }}</span>
                                </div>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th><span class="color-error">*</span>ニックネーム</th>
                        <td>
                          <div class="form-group " >
                            <div class="col-sm-6">
                              <input type="text" name="nickname" placeholder="ニックネーム"  value="{{ old('nickname') }}">
                              @if ($errors->has('nickname'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('nickname') }}</span>
                                </div>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th><span class="color-error">*</span>生年月日</th>
                        <td>
                          <div class="form-group">
                            @php
                              $maxYear = \Carbon\Carbon::parse(now())->subYear(52)->format('Y');
                              $minYear = \Carbon\Carbon::parse(now())->subYear(20)->format('Y');
                            @endphp
                            <div class="col-sm-2">
                              <select name="year" id="val-year" class="form-control select-time">
                                <option value="" class="hidden">未設定</option>
                                @foreach (range($minYear, $maxYear) as $year)
                                  <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>{{ $year }} </option>
                                @endforeach
                              </select>
                              <span class="time">年</span>
                            </div>
                            <div class="col-sm-2 ">
                               <select name="month" id="val-month" class="form-control select-time">
                                <option value="" class="hidden">未設定</option>
                                @foreach (range(01,12) as $month)
                                  <option value="{{ $month }}" {{ old('month') == $month ? 'selected' : '' }}>{{ $month }}</option>
                                @endforeach
                              </select>
                              <span class="time">月</span>
                            </div>
                            <div class="col-sm-2">
                              <select name="date" id="val-date" class="form-control select-time">
                                <option value="" class="hidden">未設定</option>
                                @foreach (range(01,31) as $date)
                                <option value="{{ $date }}" {{ old('date') == $date ? 'selected' : '' }}>{{ $date }}</option>
                                @endforeach
                              </select>
                              <span class="time">日</span>
                            </div>
                            <div class="col-sm-3">
                              現在の年齢 : <span id="age"></span> 歳
                            </div>
                            @if ($errors->has('date_of_birth'))
                              <div class="error col-sm-12">
                                <span>{{ $errors->first('date_of_birth') }}</span>
                              </div>
                            @endif
                            <input type="hidden" name="date_of_birth" id="date-of-birth" value="">
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th><span class="color-error">*</span>性別</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3">
                              <select name="gender" class="form-control select-time notify-type" >
                                <option value="2" class="for_user">女性</option>
                              </select>
                            </div>
                          </div>
                          @if ($errors->has('gender'))
                            <div class="error pull-left">
                              <span>{{ $errors->first('gender') }}</span>
                            </div>
                          @endif
                        </td>
                      </tr>
                      @if ($castClass->count())
                      <tr>
                        <th><span class="color-error">*</span>キャストクラス</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3">
                              <select name="class_id" class="form-control select-time notify-type" >
                                @foreach($castClass as $value)
                                <option value="{{ $value->id }}" class="for_user" {{ old('class_id') == $value->id ? 'selected' : '' }}>
                                  {{ $value->name }}
                                </option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                          @if ($errors->has('class_id'))
                            <div class="error pull-left">
                              <span>{{ $errors->first('class_id') }}</span>
                            </div>
                          @endif
                        </td>
                      </tr>
                      @endif
                      <tr>
                        <th>キャスト一覧表示優先ランク</th>
                        <td>
                          @php
                            $arrRank = App\Enums\UserRank::toSelectArray();
                            krsort($arrRank);
                          @endphp
                          <div class="form-group">
                            <div class="col-sm-3">
                              <select class="cast-rank" name="cast_rank">
                                @foreach($arrRank as $key => $rank)
                                  <option value="{{ $key }}" {{ old('cast_rank') == $key ? 'selected' : '' }}>{{ $rank }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        </td>
                      </tr>
                      @if ($prefectures->count())
                      <tr>
                        <th class="color-error">稼働希望エリア</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3">
                              <select name="prefecture" class="form-control select-time notify-type">
                                @foreach($prefectures as $prefecture)
                                <option value="{{ $prefecture->id }}" class="for_user" {{ old('prefecture') == $prefecture->id ? 'selected' : '' }}>
                                  {{ $prefecture->name }}
                                </option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                          @if ($errors->has('prefecture'))
                            <div class="error pull-left">
                              <span>{{ $errors->first('prefecture') }}</span>
                            </div>
                          @endif
                        </td>
                      </tr>
                      @endif
                      <tr>
                        <th><span class="color-error">*</span>電話番号</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-6">
                              <input type="text" name="phone" placeholder="半角数字を入力してください"  value="{{ old('phone') }}">
                              @if ($errors->has('phone'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('phone') }}</span>
                                </div>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th><span class="color-error">*</span>LINE ID</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-6">
                              <input type="text" name="line_id" placeholder="LINE IDを入力してください"  value="{{ old('line_id') }}">
                              @if ($errors->has('line_id'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('line_id') }}</span>
                                </div>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th>振込口座</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3">
                              <label>銀行名</label>
                              <input type="text" name="bank_name" value="{{ old('bank_name') }}" >
                              @if ($errors->has('bank_name'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('bank_name') }}</span>
                                </div>
                              @endif
                            </div>
                            <div class="col-sm-3">
                              <label>支店名</label>
                              <input type="text" name="branch_name" value="{{ old('branch_name') }}" >
                              @if ($errors->has('branch_name'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('branch_name') }}</span>
                                </div>
                              @endif
                            </div>
                            <div class="col-sm-3">
                              <label>口座番号</label>
                              <input type="text" name="account_number" value="{{ old('account_number') }}" >
                              @if ($errors->has('account_number'))
                                <div class="error pull-left">
                                  <span>{{ $errors->first('account_number') }}</span>
                                </div>
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th>備考</th>
                        <td>
                          <div class="form-group col-sm-9">
                            <textarea class="form-control" rows="5" name="note" placeholder="入力してください">{{ old('note') }}</textarea>
                          </div>
                          @if ($errors->has('note'))
                            <div class="error pull-left">
                              <span>{{ $errors->first('note') }}</span>
                            </div>
                          @endif
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="cast-confirm">
                    <button type="submit" class="btn btn-info">新規キャスト登録</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!--/col-->
      </div>
      <!--/row-->
    </div>
    <!--/col-->
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
@section('admin.js')
<script>
  $(function () {
    function getAge(valYear, valMonth, valDate) {
      if (valYear && valMonth && valDate) {
        var date = valYear + '-' + valMonth + '-' + valDate;
        var today = (new Date()) / 1000;
        var date = (new Date(date)) / 1000;

        var range = (today - date) / (24 * 60 * 60 * 365);
        var age = Math.floor(range);

        $('#age').html(age);

        $('#date-of-birth').val(valYear + '-' + valMonth + '-' + valDate);
      }
    }

    var valYear = $('#val-year').val();
    var valMonth = $('#val-month').val();
    var valDate = $('#val-date').val();
    getAge(valYear, valMonth, valDate);

    $('body').on('change', '#val-year', function () {
      var valYear = $(this).val();
      var valMonth = $('#val-month').val();
      var valDate = $('#val-date').val();
      getAge(valYear, valMonth, valDate);
    });

    $('body').on('change', '#val-month', function () {
      var valYear = $('#val-year').val();
      var valMonth = $(this).val();
      var valDate = $('#val-date').val();
      getAge(valYear, valMonth, valDate);
    });

    $('body').on('change', '#val-date', function () {
      var valYear = $('#val-year').val();
      var valMonth = $('#val-month').val();
      var valDate = $(this).val();
      getAge(valYear, valMonth, valDate);
    })
  });
</script>
@endsection

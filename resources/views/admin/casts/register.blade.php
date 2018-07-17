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
                <form method="POST" action="{{ route('admin.casts.confirm', ['id' => $user->id ]) }}" id="formEditCoupon" enctype='multipart/form-data' >
                  {{ csrf_field() }}
                  <div class="list-avatar">
                    <ul>
                      <li>
                        <img src="" id="front_side-img" />
                      </li>
                      <li>
                        おもて面
                      </li>
                      <li>
                        <input type="file" name="front_side" id="front_side">
                      </li>
                      <li>
                      @if ($errors->has('front_side'))
                          <div class="form-group">
                            <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                              <button data-dismiss="alert" class="close close-sm" type="button">
                                <i class="icon-remove"></i>
                              </button>
                              <strong>
                                {{ $errors->first('front_side') }}
                              </strong>
                            </div>
                          </div>
                        @endif
                      </li>
                    </ul>
                  </div>
                  <div class="list-avatar">
                    <ul>
                      <li>
                        <img src="" id="back_side-img"/>
                      </li>
                      <li>
                        うら面
                      </li>
                      <li>
                        <input type="file" name="back_side" id="back_side">
                      </li>
                      <li>
                      @if ($errors->has('back_side'))
                          <div class="form-group">
                            <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                              <button data-dismiss="alert" class="close close-sm" type="button">
                                <i class="icon-remove"></i>
                              </button>
                              <strong>
                                {{ $errors->first('back_side') }}
                              </strong>
                            </div>
                          </div>
                        @endif
                      </li>
                    </ul>
                  </div>
                  <div class="info-table coupon">
                    <table class="table table-bordered">
                      <!--  table-striped -->
                      <tr>
                        <th>userID</th>
                        <td>{{ $user->id }}</td>
                      </tr>
                      <tr>
                        <th>*氏名</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-1">
                              <input type="text" name="last_name" id="" placeholder="姓"  value="" required >
                            </div>
                            <div class="col-sm-3 col-sm-offset-1">
                              <input type="text" name="first_name" id=""  value="" placeholder="名" required >
                            </div>
                          </div>
                          @if ($errors->has('last_name'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('last_name') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                          @if ($errors->has('first_name'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('first_name') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>*ふりがな</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-1">
                              <input type="text" name="last_name_kana" id="" placeholder="せい"  value="" required >
                            </div>
                            <div class="col-sm-3 col-sm-offset-1">
                              <input type="text" name="first_name_kana" id=""  value="" placeholder="めい" required >
                            </div>
                          </div>
                          @if ($errors->has('last_name_kana'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('last_name_kana') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                          @if ($errors->has('first_name_kana'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('first_name_kana') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>*ニックネーム</th>
                        <td>
                          <div class="form-group " >
                            <div class="col-sm-3 col-sm-offset-1">
                              <input type="text" name="nick_name" id="" placeholder="せい"  value="" required >
                            </div>
                          </div>
                          @if ($errors->has('nick_name'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('nick_name') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>*生年月日</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-1">
                              <select id="" name="start_year" class="form-control select-time" >
                                @foreach (range(1950,2025) as $year)
                                  <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-sm-3 ">
                               <select id="" name="start_month" class="form-control select-time">
                                @foreach (range(01,12) as $month)
                                  <option value="{{ $month }}" >{{ $month }}月</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-sm-2">
                              <select id="" name="start_date" class="form-control select-time">
                                @foreach (range(01,31) as $date)
                                  <option value="{{ $date }}">{{ $date }}日</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-sm-2"></div>
                          </div>
                          @if(Session::has('msgstartdate'))
                          <div class="form-group error-end-coupon" >
                            <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                              <button data-dismiss="alert" class="close close-sm" type="button">
                                <i class="icon-remove"></i>
                              </button>
                              <strong>
                                {{ Session::get('msgstartdate') }}
                              </strong>
                            </div>
                          </div>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>*性別</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-1">
                              <select id="" name="gender" class="form-control select-time notify-type" >
                                <option value="1"  class="for_user" >女性</option>
                                <option value="2"  class="for_staff" >男性</option>
                              </select>
                            </div>
                          </div>
                          @if(Session::has('gender'))
                          <div class="form-group error-end-coupon" >
                            <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                              <button data-dismiss="alert" class="close close-sm" type="button">
                                <i class="icon-remove"></i>
                              </button>
                              <strong>
                                {{ Session::get('gender') }}
                              </strong>
                            </div>
                          </div>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>*キャストクラス</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-1">
                              <select id="" name="cast_class" class="form-control select-time notify-type" >
                                @foreach($castClass as $val)
                                <option value=" {{ $val->id }}"  class="for_user" >{{ $val->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                          @if(Session::has('cast_class'))
                          <div class="form-group error-end-coupon" >
                            <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                              <button data-dismiss="alert" class="close close-sm" type="button">
                                <i class="icon-remove"></i>
                              </button>
                              <strong>
                                {{ Session::get('cast_class') }}
                              </strong>
                            </div>
                          </div>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>*電話番号</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-1">
                              <input type="text" name="phone" id="" placeholder="半角数字を入力してください"  value="" required >
                            </div>
                          </div>
                          @if ($errors->has('phone'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('phone') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>*LINE ID</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-1">
                              <input type="text" name="line" id="" placeholder="LINE IDを入力してください"  value="" required >
                            </div>
                          </div>
                          @if ($errors->has('line'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('line') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>振込口座</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-3 col-sm-offset-1">
                              <label for="bank_name">銀行名</label>
                              <input type="text" name="bank_name" id="bank_name" placeholder="姓"  value="" required >
                            </div>
                            <div class="col-sm-3 col-sm-offset-1">
                              <label for="branch_name">支店名</label>
                              <input type="text" name="branch_name" id="branch_name" placeholder="姓"  value="" required >
                            </div>
                            <div class="col-sm-3 col-sm-offset-1">
                              <label for="number">口座番号</label>
                              <input type="text" name="number" id="number" placeholder="姓"  value="" required >
                            </div>
                          </div>
                          @if ($errors->has('bank_name'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('bank_name') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                          @if ($errors->has('branch_name'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('branch_name') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                          @if ($errors->has('number'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('number') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <th>備考</th>
                        <td>
                          <div class="form-group">
                            <textarea class="form-control" rows="5" id="note" name='note' placeholder="入力してください"></textarea>
                          </div>
                          @if ($errors->has('note'))
                            <div class="form-group">
                              <div class="alert alert-danger fade in col-sm-5 col-sm-offset-1">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                  <i class="icon-remove"></i>
                                </button>
                                <strong>
                                  {{ $errors->first('note') }}
                                </strong>
                              </div>
                            </div>
                          @endif
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="cast-confirm">
                    <button type="submit" class="btn btn-accept">確認画面へ</button>
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

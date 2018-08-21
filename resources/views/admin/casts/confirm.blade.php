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
                <form method="GET" action="{{ route('admin.casts.register', ['id' => $user->id ]) }}" id="formEditCoupon" >
                  <div class="list-avatar">
                    <ul>
                      <li>
                        <img src="{{ getImages($data['front_id_image']) }}" id="front_side-img" class="image-confirm" />
                      </li>
                      <li>
                        おもて面
                      </li>
                      <li>
                      </li>
                    </ul>
                  </div>
                  <div class="list-avatar">
                    <ul>
                      <li>
                        <img src="{{ getImages($data['back_id_image']) }}" id="front_side-img" class="image-confirm"/>
                      </li>
                      <li>
                        うら面
                      </li>
                      <li>
                      </li>
                    </ul>
                  </div>
                  <div class="info-table coupon">
                    <table class="table table-bordered">
                      <!--  table-striped -->
                      <tr>
                        <th>ユーザーID</th>
                        <td>{{ $user->id }}</td>
                      </tr>
                      <tr>
                        <th>*氏名</th>
                        <td>
                          <div class="form-group confirm-cast">
                            <div class="col-sm-1 col-sm-offset-5">
                              {{ $data['lastname'] }}
                            </div>
                            <div class="col-sm-1">
                              {{ $data['firstname'] }}
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th>*ふりがな</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-1 col-sm-offset-5">
                              {{ $data['lastname_kana'] }}
                            </div>
                            <div class="col-sm-1">
                              {{ $data['firstname_kana'] }}
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th>*ニックネーム</th>
                        <td>
                          <div class="form-group " >
                            <div class="col-sm-1 col-sm-offset-5">
                              {{ $data['nickname'] }}
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th>*生年月日</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-2 col-sm-offset-3">
                              {{ $data['year'] }} 年
                            </div>
                            <div class="col-sm-2">
                               {{ $data['month'] }} 月
                            </div>
                            <div class="col-sm-2">
                              {{ $data['date'] }} 日
                            </div>
                            <div class="col-sm-2">
                              {{ $data['age'] }} 歳
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th>*性別</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-1 col-sm-offset-5">
                              {{ $data['gender'] == 1 ? '女性' :'男性' }}
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th>*キャストクラス</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-2 col-sm-offset-5">
                              @if ($data['class_id'] == 1)
                                 ブロンズ
                              @elseif ($data['class_id'] == 2)
                                プラチナ'
                              @else
                                ダイヤモンド
                              @endif
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th style="color: red;">エリア</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-2 col-sm-offset-5">
                              {{ getPrefectureName($data['prefecture_id']) }}
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th>*電話番号</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-1 col-sm-offset-5">
                              {{ $data['phone'] }}
                            </div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <th>*LINE ID</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-1 col-sm-offset-5">
                              {{ $data['line_id'] }}
                            </div>
                          </div>
                        </td>
                      </tr>
                      @if (isset($data['bank_name']))
                      <tr>
                        <th>振込口座</th>
                        <td>
                          <div class="form-group">
                            <div class="col-sm-1 col-sm-offset-2">
                              <label >銀行名</label>
                            </div>
                            <div class="col-sm-2">
                              {{ $data['bank_name'] }}
                            </div>
                            <div class="col-sm-1 ">
                              <label >支店名</label>
                            </div>
                            <div class="col-sm-2">
                              {{ $data['branch_name'] }}
                            </div>
                            <div class="col-sm-1 ">
                              <label >口座番号</label>
                            </div>
                            <div class="col-sm-2">
                              {{ $data['number'] }}
                            </div>
                          </div>
                        </td>
                      </tr>
                      @endif
                      <tr>
                        <th>備考</th>
                        <td>
                          <div class="form-group">
                            {{ $data['note'] }}
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="cast-confirm">
                    <button type="submit" class="btn btn-accept">戻る</button>
                    <button type="button" class="btn btn-accept" data-toggle="modal" data-target="#saveCast">登録する</button>
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
<div class="modal fade" id="saveCast" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <span>登録が完了しました。</span><br/>
      </div>
      <div class="modal-footer">
        <form action="{{ route('admin.casts.save', ['id' => $user->id ]) }}" method="POST" class="form-cast">
          {{ csrf_field() }}
          <input type="hidden" name="lastname" value="{{ $data['lastname'] }}">
          <input type="hidden" name="firstname" value="{{ $data['lastname'] }}">
          <input type="hidden" name="lastname_kana" value="{{ $data['lastname_kana'] }}">
          <input type="hidden" name="firstname_kana" value="{{ $data['firstname_kana'] }}">
          <input type="hidden" name="nickname" value="{{ $data['nickname'] }}">
          <input type="hidden" name="phone" value="{{ $data['phone'] }}">
          <input type="hidden" name="line_id" value="{{ $data['line_id'] }}">
          @if (isset($data['bank_name']))
          <input type="hidden" name="bank_name" value="{{ $data['bank_name'] }}">
          <input type="hidden" name="branch_name" value="{{ $data['branch_name'] }}">
          <input type="hidden" name="number" value="{{ $data['number'] }}">
          @endif
          <input type="hidden" name="note" value="{{ $data['note'] }}">
          <input type="hidden" name="gender" value="{{ $data['gender'] }}">
          <input type="hidden" name="class_id" value="{{ $data['class_id'] }}">
          <input type="hidden" name="year" value="{{ $data['year'] }}">
          <input type="hidden" name="month" value="{{ $data['month'] }}">
          <input type="hidden" name="date" value="{{ $data['date'] }}">
          <input type="hidden" name="front_id_image" value="{{ $data['front_id_image'] }}">
          <input type="hidden" name="back_id_image" value="{{ $data['back_id_image'] }}">
          <input type="hidden" name="prefecture" value="{{ $data['prefecture_id'] }}">
          <button type="submit" class="btn btn-accept">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

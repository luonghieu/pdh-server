@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  @include('admin.partials.alert-error', compact('errors'))
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-resigns', compact('hidden'))
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="#" method="GET">
              <input type="hidden" name="hidden" value="{{ request()->hidden }}" />
              <input type="text" class="form-control init-input-search" placeholder="ユーザーID,名前" name="search" value="{{ request()->search }}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker init-input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date }}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->to_date }}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn btn-search"></button>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="#" id="limit-page" method="GET">
              <div class="form-group">
                <label class="col-md-1 limit-page">表示件数：</label>
                <div class="col-md-1">
                  <select id="select-limit" name="limit" class="form-control">
                    @foreach ([10, 20, 50, 100] as $limit)
                      <option value="{{ $limit }}" {{ request()->limit == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                    @endforeach
                  </select>
                  <input type="hidden" name="from_date" value="{{ request()->from_date }}" />
                  <input type="hidden" name="to_date" value="{{ request()->to_date }}" />
                  <input type="hidden" name="search" value="{{ request()->search }}" />
                  <input type="hidden" name="hidden" value="{{ request()->hidden }}" />
                </div>
              </div>
          </form>
        </div>
        <div class="panel-body">
          @php
            $request = [
              'page' => request()->page,
              'limit' => request()->limit,
              'search' => request()->search,
              'from_date' => request()->from_date,
              'to_date' => request()->to_date,
              'hidden' => request()->hidden,
           ];
          @endphp
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th></th>
                <th>ユーザーID</th>
                <th>ユーザー名</th>
                <th>申請日</th>
                <th>ゲスト詳細</th>
                <th>退会申請詳細</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
                <tr>
                  <td class="select-checkbox">
                      <input type="checkbox" class="verify-checkboxs" value="{{ $user->id }}">
                  </td>
                  <td>{{ $user->id }}</td>
                  <td>{{ $user->nickname }}</td>
                  <td>{{ $user->resign_date }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="btn btn-detail">詳細</a></td>
                  <td><a href="{{ route('admin.resigns.show', ['user' => $user->id]) }}" class="btn btn-detail">詳細</a></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <!-- popup image -->
        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <div class="init-border-img">
                  <img src="" class="imagepreview" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--  -->
        <!-- popup public timeline -->
        <div class="modal fade" id="public-timeline" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>公開ステータスに変更しますか？</p>
              </div>
              <form method="POST" class="form-action">
                {{ csrf_field() }}
                <div class="modal-footer">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept on-work">変更する</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!--  -->
        <!-- popup private timeline -->
        <div class="modal fade" id="private-timeline" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>非公開ステータスに変更しますか？</p>
              </div>
              <form method="POST" class="form-action">
                {{ csrf_field() }}
                <div class="modal-footer">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept on-work">変更する</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!--  -->
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
              全 2323件中 1~2件を表示しています
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{--{{ $timelines->appends(request()->all())->links() }}--}}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
@section('admin.js')
  <script>

  </script>
@endsection

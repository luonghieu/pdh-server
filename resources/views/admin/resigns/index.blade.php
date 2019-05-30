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
            @php
            if (request()->resign_status == App\Enums\ResignStatus::PENDING) {
              $route = route('admin.resigns.index', ['resign_status' => App\Enums\ResignStatus::PENDING]);
            } else {
              $route = route('admin.resigns.index', ['resign_status' => App\Enums\ResignStatus::APPROVED]);
            }
            @endphp
            <form class="navbar-form navbar-left form-search" action="{{ $route }}" method="GET">
              <input type="hidden" name="hidden" value="{{ request()->hidden }}" />
              <input type="text" class="form-control init-input-search" placeholder="ユーザーID,名前" name="search" value="{{ request()->search }}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker init-input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date }}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->to_date }}" placeholder="yyyy/mm/dd"/>
              <input type="hidden" name="resign_status" value="{{ request()->resign_status }}">
              <input type="hidden" name="limit" value="{{ request()->limit }}">
              <input type="hidden" name="page" value="{{ request()->page }}">
              <button type="submit" class="fa fa-search btn btn-search"></button>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{ $route }}" id="limit-page" method="GET">
              <div class="form-group">
                <label class="col-md-1 limit-page">表示件数：</label>
                <div class="col-md-1">
                  <select id="select-limit" name="limit" class="form-control">
                    @foreach ([10, 20, 50, 100] as $limit)
                      <option value="{{ $limit }}" {{ request()->limit == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            <input type="hidden" name="from_date" value="{{ request()->from_date }}" />
            <input type="hidden" name="to_date" value="{{ request()->to_date }}" />
            <input type="hidden" name="search" value="{{ request()->search }}" />
            <input type="hidden" name="hidden" value="{{ request()->hidden }}" />
            <input type="hidden" name="resign_status" value="{{ request()->resign_status }}" />
          </form>
        </div>
        <form class="navbar-form navbar-left form-search" action="{{ $route }}" id="limit-page" method="GET">
          @if(request()->resign_status == \App\Enums\ResignStatus::PENDING)
            <div class="init-btn-confirm-resign">
              <button onclick="return false;" class="btn btn-info" data-toggle="modal" data-target="#confirm-resign">退会済みにする</button>
            </div>
          @else
            <div class="init-btn-export-resign">
              <input type="hidden" name="is_export_resign" value="1">
              <button type="submit" class="btn btn-info" name="submit" value="export_resign">エクスポートする</button>
            </div>
          @endif
        </form>
        <div class="panel-body">
          @php
            $request = [
              'page' => request()->page,
              'limit' => request()->limit,
              'search' => request()->search,
              'from_date' => request()->from_date,
              'to_date' => request()->to_date,
              'hidden' => request()->hidden,
              'resign_status' => request()->resign_status,
           ];
          @endphp
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                @if(request()->resign_status == \App\Enums\ResignStatus::PENDING)
                <th></th>
                @endif
                  <th>ユーザーID</th>
                  <th>ユーザー名</th>
                  @if(request()->resign_status == \App\Enums\ResignStatus::PENDING)
                    <th>申請日</th>
                    <th>ゲスト詳細</th>
                    <th>退会申請詳細</th>
                  @else
                    <th>退会日時</th>
                    <th></th>
                  @endif
              </tr>
            </thead>
            <tbody>
            @if (empty($users->count()))
              <tr>
                <td colspan="10">{{ trans('messages.results_not_found') }}</td>
              </tr>
            @else
              @foreach($users as $user)
                <tr>
                @if(request()->resign_status == \App\Enums\ResignStatus::PENDING)
                  <td class="select-checkbox">
                    <input type="checkbox" class="verify-checkboxs" value="{{ $user->id }}">
                  </td>
                @endif
                <td>{{ $user->id }}</td>
                <td>{{ $user->nickname }}</td>
                @if(request()->resign_status == \App\Enums\ResignStatus::PENDING)
                  <td>{{ $user->resign_date ? Carbon\Carbon::parse($user->resign_date)->format('Y年m月d日') : '' }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="btn btn-detail">詳細</a></td>
                  <td><a href="{{ route('admin.resigns.show', ['user' => $user->id]) }}" class="btn btn-detail">詳細</a></td>
                @else
                  <td>{{ $user->resign_date ? Carbon\Carbon::parse($user->resign_date)->format('Y年m月d日　h:m') : '' }}</td>
                  <td><a href="{{ route('admin.resigns.show', ['user' => $user->id]) }}" class="btn btn-detail">詳細</a></td>
                @endif
                </tr>
              @endforeach
            @endif
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
        <!-- popup confirm resign -->
        <div class="modal fade" id="confirm-resign" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>チェックを付けたレコードを"退会済み"に変更しますか？</p>
              </div>
              <form action="{{ route('admin.resigns.delete') }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <input type="hidden" id="user_ids" name="user_ids" value="{{ old('user_ids') }}" />
                <div class="modal-footer">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept confirm-resign">はい</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!--  -->
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($users->total())
              全 {{ $users->total() }}件中 {{ $users->firstItem() }}~{{ $users->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $users->appends(request()->all())->links() }}
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
  <script type="text/javascript">
    $('.confirm-resign').on('click', function() {
      var user_ids = [];
      $('.verify-checkboxs:checked').each(function() {
        user_ids.push(this.value);
      });

      $('#user_ids').val(user_ids.join(','));

      return true;
    });
  </script>
@stop

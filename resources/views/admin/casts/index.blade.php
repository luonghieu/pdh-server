@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.casts.index') }}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{ request()->search }}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date }}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->to_date }}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn btn-search"></button>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <div class="col-sm-6">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.casts.index') }}" id="limit-page" method="GET">
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
                  </div>
                </div>
            </form>
          </div>
          <div class="col-sm-6">
            <div class="pull-right">
              <a href="{{ route('admin.casts.create') }}" class="btn btn-info">新規キャストアカウント作成</a>
            </div>
            <div class="mr-1 pull-right">
              <a href="{{ route('admin.casts.export_bank_accounts') }}" class="btn btn-info">振込口座リストを抽出する</a>
            </div>
          </div>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          @php
            $request = [
              'page' => request()->page,
              'limit' => request()->limit,
              'search' => request()->search,
              'from_date' => request()->from_date,
              'to_date' => request()->to_date,
           ];
          @endphp
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>ユーザーID</th>
                <th>ニックネーム</th>
                <th>年齢</th>
                <th class="sorting{{ (request()->rank) ? '_' . request()->rank: '' }}">
                  <a href="{{ route('admin.casts.index',
                    array_merge($request, ['rank' => (request()->rank == 'asc') ? 'desc' : 'asc',])
                    ) }}">優先ランク
                  </a>
                </th>
                <th>会員区分</th>
                <th>アカウント連携状況</th>
                <th>ステータス</th>
                <th class="sorting{{ (request()->last_active_at) ? '_' . request()->last_active_at: '' }}">
                  <a href="{{ route('admin.casts.index',
                    array_merge($request, ['last_active_at' => (request()->last_active_at == 'asc') ? 'desc' : 'asc',])
                    ) }}">オンライン
                   </a>
                </th>
                <th>本日出勤</th>
                <th>キャスト登録日時</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($casts->count()))
                <tr>
                  <td colspan="10">{{ trans('messages.cast_not_found') }}</td>
                </tr>
              @else
                @foreach ($casts as $key => $cast)
                <tr>
                  <td>{{ $casts->firstItem() + $key }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $cast->id]) }}">{{ $cast->id }}</a></td>
                  <td>
                    @if ($cast->provider == App\Enums\ProviderType::EMAIL)
                    <span class="color-error">★</span>
                    @endif
                    {{ $cast->nickname }}
                  </td>
                  <td>{{ $cast->age }}</td>
                  <td>{{ App\Enums\UserRank::getKey($cast->rank) }}</td>
                  <td>{{ App\Enums\UserType::getDescription($cast->type) }}</td>
                  <td>
                    @if(App\Enums\DeviceType::IOS == $cast->device_type && 'facebook' == $cast->provider)
                      未完了
                    @endif
                  </td>
                  <td>{{ App\Enums\Status::getDescription($cast->status) }}</td>
                  @if ($cast->is_online == true)
                  <td>オンライン中</td>
                  @else
                  <td>{{ $cast->last_active }}</td>
                  @endif
                  <td>
                    {{ App\Enums\WorkingType::getDescription($cast->working_today) }}
                    @php
                      if ($cast->working_today == App\Enums\WorkingType::LEAVING_WORK) {
                        $workStatus = 'on-work';
                      } else {
                        $workStatus = 'leaving-work';
                      }
                    @endphp
                    <button data-toggle="modal" data-target="#{{ $workStatus }}" data-url="{{ route('admin.casts.change_status_work', $cast->id) }}" id="change-status-work" class="btn btn-default">{{ ($cast->working_today == 1) ? '退勤'  : '出勤' }}</button>
                  </td>
                  <td>{{ Carbon\Carbon::parse($cast->created_at)->format('Y/m/d H:i') }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $cast->id]) }}" class="btn btn-detail">詳細</a></td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <!-- popup on work -->
        <div class="modal fade" id="on-work" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>このキャストを"出勤中"にしますか？</p>
              </div>
              <form method="POST" class="form-action">
                {{ csrf_field() }}
                <div class="modal-footer">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">いいえ</button>
                  <button type="submit" class="btn btn-accept on-work">はい</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!--  -->
        <!-- popup leaving work -->
        <div class="modal fade" id="leaving-work" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>このキャストを"退勤中"にしますか？</p>
              </div>
              <form method="POST" class="form-action">
                {{ csrf_field() }}
                <div class="modal-footer">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">いいえ</button>
                  <button type="submit" class="btn btn-accept leaving-work">はい</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!--  -->
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($casts->total())
              全 {{ $casts->total() }}件中 {{ $casts->firstItem() }}~{{ $casts->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $casts->appends(request()->all())->links() }}
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
    $(function () {
      $('body').on('click', '#change-status-work', function () {
        var url = $(this).attr('data-url');
        $('.form-action').attr('action', url);
      });
    });
  </script>
@endsection

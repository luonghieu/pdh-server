@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-timeline', compact('hidden'))
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
                <th>No.</th>
                <th class="sorting{{ (request()->user_id) ? '_' . request()->user_id: '' }}">
                  <a href="{{ route('admin.timelines.index',
                    array_merge($request, ['user_id' => (request()->user_id == 'asc') ? 'desc' : 'asc',])
                    ) }}">ユーザーID
                  </a>
                </th>
                <th>ニックネーム</th>
                <th class="sorting{{ (request()->type) ? '_' . request()->type: '' }}">
                  <a href="{{ route('admin.timelines.index',
                    array_merge($request, ['type' => (request()->type == 'asc') ? 'desc' : 'asc',])
                    ) }}">会員区分
                  </a>
                </th>
                <th class="sorting{{ (request()->created_at) ? '_' . request()->created_at: '' }}">
                  <a href="{{ route('admin.timelines.index',
                    array_merge($request, ['created_at' => (request()->created_at == 'asc') ? 'desc' : 'asc',])
                    ) }}">投稿日時
                  </a>
                </th>
                <th>投稿内容</th>
                <th>添付ファイル</th>
                <th>{{ (request()->hidden == App\Enums\TimelineStatus::PUBLIC) ? '非公開' : '公開' }}</th>
                <th>ユーザー詳細</th>
              </tr>
            </thead>
            <tbody>
              @if (empty($timelines->count()))
                <tr>
                  <td colspan="9">{{ trans('messages.timeline_not_found') }}</td>
                </tr>
              @else
                @php
                  $index = 1;
                @endphp
                @foreach ($timelines as $key => $timeline)
                <tr>
                  <td>{{ (request()->limit ?: 10) * ((request()->page ?: 1) - 1) + $index++ }}</td>
                  <td>{{ $timeline->user_id }}</td>
                  <td>{{ $timeline->user->nickname }}</td>
                  <td>{{ App\Enums\UserType::getDescription($timeline->user->type) }}</td>
                  <td>{{ Carbon\Carbon::parse($timeline->created_at)->format('Y/m/d H:i') }}</td>
                  <td>{{ $timeline->content }}</td>
                  <td>
                    @if ($timeline->image)
                    <a href="javascript::void(0)" class="js-pop-img">
                      <button class="btn btn-default" data-src="{{ $timeline->image }}">表示する</button>
                    </a>
                    @endif
                  </td>
                  <td>
                    @php
                      if ($timeline->hidden == App\Enums\TimelineStatus::PRIVATE) {
                        $isHidden = 'public-timeline';
                      } else {
                        $isHidden = 'private-timeline';
                      }
                    @endphp
                    <button data-toggle="modal" data-target="#{{ $isHidden }}" data-url="{{ route('admin.timelines.change_status_hidden', $timeline->id) }}" id="change-status-hidden" class="btn btn-default">{{ ($timeline->hidden == 1) ? '公開' : '非公開' }}</button>
                  </td>
                  <td><a href="{{ route('admin.users.show', ['user' => $timeline->user_id]) }}" class="btn btn-detail">詳細</a></td>
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
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($timelines->total())
              全 {{ $timelines->total() }}件中 {{ $timelines->firstItem() }}~{{ $timelines->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $timelines->appends(request()->all())->links() }}
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
      $('body').on('click', '#change-status-hidden', function () {
        var url = $(this).attr('data-url');
        $('.form-action').attr('action', url);
      });
    });
  </script>
  <script>
    $(function() {
        $('.js-pop-img').on('click', function() {
          $('.imagepreview').attr('src', $(this).find('button').attr('data-src'));
          $('#imagemodal').modal('show');
        });
    });
  </script>
@endsection

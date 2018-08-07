@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.reports.index')}}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{request()->search}}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn-search"></button>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{route('admin.reports.index')}}" id="limit-page" method="GET">
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
        <div class="btn-change-report">
          <button data-toggle="modal" data-target="#done_report" >
            <p>チェックを付けた通報の</p>
            <p>対応ステータスを完了にす</p>
          </button>
        </div>
        <form action="{{ route('admin.reports.make_report_done') }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="modal fade" id="done_report" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-body">
                  <p>チェックを付けた通報の対応ステータスを完了にしますか？</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept">はい</button>
                </div>
              </div>
            </div>
          </div>
          <div class="panel-body">
            @include('admin.partials.notification')
            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th>No.</th>
                  <th></th>
                  <th>ルームID </th>
                  <th>送信者</th>
                  <th>通報内容</th>
                  <th>送信日時</th>
                  <th>リンク</th>
                  <th>対応ステータス</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($reports as $key => $report)
                <tr>
                  <td>{{ $reports->firstItem() +$key }}</td>
                  @if ($report->status == App\Enums\ReportStatus::OPEN)
                  <td class="select-checkbox">
                    <input class="verify-checkboxs"
                      type="checkbox"
                      value="{{ $report->id }}"
                      name="report_ids[]">
                  </td>
                  @else
                  <td></td>
                  @endif
                  <td><a href="{{ route('admin.rooms.messages_by_room', ['room' => $report->room_id]) }}">{{ $report->room_id }}</a></td>
                  <td><a href="{{ route('admin.users.show', ['user' => $report->user->id]) }}">{{ $report->user->fullname}}</a></td>
                  <td>{{ $report->content }}</td>
                  <td>{{ Carbon\Carbon::parse($report->created_at)->format('Y/m/d H:i') }}</td>
                  <td>{{ 1 }}</td>
                  <td>{{ App\Enums\ReportStatus::getDescription($report->status) }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </form>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($reports->total())
              全 {{ $reports->total() }}件中 {{ $reports->firstItem() }}~{{ $reports->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $reports->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection

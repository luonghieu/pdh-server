@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.rooms.messages_by_room', ['room' => $room->id])}}" method="GET">
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
          <form class="navbar-form navbar-left form-search" action="{{route('admin.rooms.messages_by_room', ['room' => $room->id])}}" id="limit-page" method="GET">
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
          <div class="inactive-room">
              @php
                if($room->is_active == true) {
                  $nameId = "#inactiveModal";
                  $title = "このチャットルームを無効にする";
                } else {
                  $nameId = "#activeModal";
                  $title = "このチャットルーム有効にする";
                }
              @endphp
            <button class="btn btn-info" data-toggle="modal" data-target="{{ $nameId }}">{{ $title }}</button>
          </div>
        </div>
        <div class="panel-body change-active-room">
          <div class="display-title">
            <p><b>ルームID:</b> {{ $room->id }}</p>
          </div>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>区分</th>
                <th>送信者</th>
                <th>メッセージ</th>
                <th>送信日時</th>
              </tr>
            </thead>
            <tbody>
              @if (empty($messages->count()))
                <tr>
                  <td colspan="8">{{ trans('messages.message_not_found') }}</td>
                </tr>
              @else
                @foreach ($messages as $key => $message)
                <tr>
                  <td>{{ $messages->firstItem() +$key }}</td>
                  <td>{{ $message->user ? App\Enums\UserType::getDescription($message->user->type) : "" }}</td>
                  <td><a href="{{ $message->user ? route('admin.users.show', ['user' => $message->user->id]) : '#' }}">
                    @if ($message->user->type == App\Enums\UserType::CAST && $message->user->provider == App\Enums\ProviderType::EMAIL)
                    <span class="color-error">★</span>
                    @endif
                    {{ $message->user ? $message->user->fullname : ""}}
                  </a></td>
                  @if (empty($message->message))
                  <td class="long-text"><img src="{{ $message->image }}" alt="" class="image-message"></td>
                  @else
                  <td class="long-text">{{ $message->message }}</td>
                  @endif
                  <td>{{ Carbon\Carbon::parse($message->created_at)->format('Y/m/d H:i') }}</td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <div class="modal fade" id="inactiveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>本当にこのチャットルームを無効にしますか？</p>
                <p>実行すると、アプリ側のチャットルームが削除されます。</p>
              </div>
              <div class="modal-footer">
                <form action="{{ route('admin.rooms.change_active', ['rooms' => $room->id]) }}" method="POST">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  {{ csrf_field() }}
                  {{ method_field('PUT') }}
                  <button type="submit" class="btn btn-accept">はい</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="activeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>本当にこのチャットルームを有効にしますか？</p>
                <p>実行すると、アプリ側にチャットルームが表示されるようになります。</p>
              </div>
              <div class="modal-footer">
                <form action="{{ route('admin.rooms.change_active', ['rooms' => $room->id]) }}" method="POST">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  {{ csrf_field() }}
                  {{ method_field('PUT') }}
                  <button type="submit" class="btn btn-accept">はい</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($messages->total())
              全 {{ $messages->total() }}件中 {{ $messages->firstItem() }}~{{ $messages->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $messages->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection

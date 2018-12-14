@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.rooms.index') }}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID, 名前" name="search" value="{{request()->search}}">
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
          <form class="navbar-form navbar-left form-search" action="{{ route('admin.rooms.index') }}" id="limit-page" method="GET">
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
        <div class="btn-change-report report">
          <button data-toggle="modal" data-target="#done_report" >
            <p>
              <a href="{{ route('admin.reports.index') }}">通報一覧へ</a>
            </p>
          </button>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>ルームID </th>
                <th>オーナーID</th>
                <th>チャットルーム区分</th>
                <th>メンバー情報</th>
                <th>チャットルーム生成日時</th>
                <th>ステータス</th>
                <th>リンク</th>
              </tr>
            </thead>
            <tbody>
              @if (empty($rooms->count()))
                <tr>
                  <td colspan="8">{{ trans('messages.room_not_found') }}</td>
                </tr>
              @else
                @foreach ($rooms as $key => $room)
                <tr>
                  <td>{{ $rooms->firstItem() +$key }}</td>
                  <td><a href="{{ route('admin.rooms.messages_by_room', ['room' => $room->id]) }}">{{ $room->id }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $room->owner_id]) }}">
                    @if ($room->is_group)
                    {{ $room->owner_id }}
                    @else
                      @if (
                        $room->owner->id == $room->owner_id
                        && $room->owner->provider == App\Enums\ProviderType::EMAIL
                        && $room->owner->type == App\Enums\UserType::CAST
                        )
                        <span class="color-error">★</span>
                      @endif
                      {{ $room->owner_id }}
                    @endif
                  </a></td>
                  <td>{{ $room->is_group ? 'グループ' : '個別' }}</td>
                  <td>
                    @if ($room->is_group)
                    <a href="{{ route('admin.rooms.members', ['room' => $room->id]) }}">
                      {{ $room->users->count().'人' }}
                    </a>
                    @else
                    <a href="{{ route('admin.users.show',
                    ['user' => ($room->users[0]->id == $room->owner_id) ? $room->users[1]->id : $room->users[0]->id]) }}">
                      @if (
                        ($room->users[0]->provider == App\Enums\ProviderType::EMAIL
                        && $room->users[0]->id != $room->owner_id
                        && $room->users[0]->type == App\Enums\UserType::CAST
                        )
                        ||
                        ($room->users[1]->provider == App\Enums\ProviderType::EMAIL
                        && $room->users[1]->id != $room->owner_id
                        && $room->users[1]->type == App\Enums\UserType::CAST)
                        )
                        <span class="color-error">★</span>
                      @endif
                      {{ ($room->users[0]->id == $room->owner_id) ? $room->users[1]->id : $room->users[0]->id }}
                    </a>
                    @endif
                  </td>
                  <td>{{ Carbon\Carbon::parse($room->created_at)->format('Y/m/d H:i') }}</td>
                  <td>
                    @if ($room->is_direct && $room->users->count() >= 2)
                      @if ($room->checkBlocked(($room->users[0]->id == $room->owner_id) ? $room->users[1]->id : $room->users[0]->id))
                      'ブロック中'
                      @endif
                    @else
                    {{ App\Enums\Status::getDescription($room->is_active) }}
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('admin.rooms.messages_by_room', ['room' => $room->id]) }}">
                      {{ route('admin.rooms.messages_by_room', ['room' => $room->id]) }}
                    </a>
                  </td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($rooms->total())
              全 {{ $rooms->total() }}件中 {{ $rooms->firstItem() }}~{{ $rooms->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $rooms->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection

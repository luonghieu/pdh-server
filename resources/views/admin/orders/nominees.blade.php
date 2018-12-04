@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="display-title">
            <p><b>予約ID:</b> {{ $order->id }} の指名キャスト一覧</p>
          </div>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <div class="row">
            <div class="col-lg-8">
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>ユーザーID</th>
                <th>ニックネーム</th>
                <th>ステータス</th>
              </tr>
            </thead>
            <tbody>
              @if (empty($casts->count()))
                <tr>
                  <td colspan="4">{{ trans('messages.results_not_found') }}</td>
                </tr>
              @else
                @foreach ($casts as $key => $cast)
                <tr>
                  <td>{{ $casts->firstItem() +$key }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $cast->id]) }}">{{ $cast->id }}</a></td>
                  <td>
                    @if ($cast->provider == App\Enums\ProviderType::EMAIL)
                    <span class="color-error">★</span>
                    @endif
                    {{ $cast->nickname }}
                  </td>
                  <td>
                    @if (App\Enums\CastOrderStatus::OPEN == $cast->pivot->status)
                    提案中
                    @endif
                    @if (in_array($cast->pivot->status, [App\Enums\CastOrderStatus::ACCEPTED, App\Enums\CastOrderStatus::PROCESSING, App\Enums\CastOrderStatus::DONE]))
                    承諾済み
                    @endif
                    @if (in_array($cast->pivot->status, [App\Enums\CastOrderStatus::DENIED, App\Enums\CastOrderStatus::CANCELED, App\Enums\CastOrderStatus::TIMEOUT]))
                    キャンセル
                    @endif
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
            </div>
          </div>
        </div>
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

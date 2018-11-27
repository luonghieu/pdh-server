@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="pull-right">
            <a href="{{ route('admin.offers.create') }}" class="btn btn-info">新規オファーを作成する</a>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>予約ID</th>
                <th>キャスト情報</th>
                <th>ギャラ飲み予定開始日時</th>
                <th>ギャラ飲みの時間</th>
                <th>エリア</th>
                <th>合計予定ポイント</th>
                <th>ステータス</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($offers->count()))
                <tr>
                  <td colspan="9">{{ trans('messages.results_not_found') }}</td>
                </tr>
              @else
                @foreach ($offers as $key => $offer)
                <tr>
                  <td>{{ $offers->firstItem() + $key }}</td>
                  <td><a href="#">{{ $offer->id }}</a></td>
                  <td>{{ count($offer->cast_ids) }}名</td>
                  <td>
                    {{ Carbon\Carbon::parse($offer->date)->format('Y/m/d') }}
                    {{ Carbon\Carbon::parse($offer->start_time_from)->format('h:i') }} ~
                    {{ Carbon\Carbon::parse($offer->start_time_to)->format('h:i') }}
                  </td>
                  <td>{{ $offer->duration }}時間</td>
                  <td>{{ getPrefectureName($offer->prefecture_id) }}</td>
                  <td>{{ number_format($offer->temp_point) }}P</td>
                  <td>{{ App\Enums\OfferStatus::getDescription($offer->status) }}</td>
                  <td><a href="#" class="btn btn-info">詳細</a></td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($offers->total())
              全 {{ $offers->total() }}件中 {{ $offers->firstItem() }}~{{ $offers->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $offers->appends(request()->all())->links() }}
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
    $(function () {
      if (localStorage.getItem('offer')){
        localStorage.removeItem('offer');
      }
    })
  </script>
@stop

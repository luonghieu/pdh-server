@extends('layouts.admin')
@section('admin.content')
  <div class="col-md-10 col-sm-11 main">
    @include('admin.partials.alert-error', compact('errors'))
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-body handling">
            <div class="search">
              <form class="navbar-form navbar-left form-search" action="{{ route('admin.coupons.index') }}" method="GET">
                <input type="text" class="form-control input-search" placeholder="クーポン名" name="search" value="{{request()->search}}">
                <label for="">From date: </label>
                <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
                <label for="">To date: </label>
                <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
                <button type="submit" class="btn fa fa-search btn-search"></button>
              </form>
            </div>
            <div class="clearfix"></div>
            <div class="pull-right">
              <a href="{{ route('admin.coupons.create') }}" class="btn btn-info">新規クーポン作成</a>
            </div>
          </div>
          <div class="panel-body">
            @include('admin.partials.notification')
            <table class="table table-striped table-bordered bootstrap-datatable table-hover">
              <thead>
              <tr>
                <th>No.</th>
                <th>クーポン名</th>
                <th>対象ゲスト</th>
                <th>利用回数</th>
                <th>クーポン作成日時</th>
                <th></th>
                <th>表示</th>
              </tr>
              </thead>
              <tbody>
              @if (empty($coupons->count()))
                <tr>
                  <td colspan="10">{{ trans('messages.results_not_found') }}</td>
                </tr>
              @else
                @php $index = 1; @endphp
                @foreach ($coupons as $key => $coupon)
                  <tr class="js-init-coupon" id="css-init-hover" data-coupon-id="{{ $coupon->id }}" data-sort-index="{{ $coupon->sort_index }}">
                    <td class="index">{{ $index + $key }}</td>
                    <td class="long-text">{{ $coupon->name }}</td>
                    <td>
                      @if($coupon->is_filter_after_created_date)
                      登録時から{{ $coupon->filter_after_created_date }}日間以内
                      @endif
                    </td>
                    <td><a href="{{route('admin.coupons.history', ['coupon' => $coupon->id])}}">{{ count($coupon->users) }}</a></td>
                    <td>{{ $coupon->created_at }}</td>
                    <td>
                      <a href="{{route('admin.coupons.show', ['coupon' => $coupon->id])}}" class="btn btn-info">詳細</a>
                      <button type="button" class="btn btn-info" data-toggle="modal" data-target="#delete_coupon_modal_{{$coupon->id}}" >削除</button>
                      <div class="modal fade" id="delete_coupon_modal_{{$coupon->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-body">
                              <p>{{$coupon->name}}を削除しますか？？</p>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                              <a href="{{route('admin.coupons.delete', ['coupon' => $coupon->id])}}" data-method="delete" class="btn btn-info">はい</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <label class="switch switch-primary">
                        <input type="checkbox" class="switch-input" data-id="{{ $coupon->id }}" data-update-is-active="{{ route('admin.coupons.update_is_active', ['coupon' => $coupon->id]) }}" name="is_active" value="{{ $coupon->is_active }}" {{ $coupon->is_active ? 'checked' : ''}}>
                        <span class="switch-label" data-on="On" data-off="Off"></span>
                        <span class="switch-handle"></span>
                      </label>
                    </td>
                  </tr>
                @endforeach
              @endif
              </tbody>
            </table>
            <input type="hidden" id="url-update-sort-index" value="{{ route('admin.coupons.update_sort_index') }}">
          </div>
        </div>
      </div>
      <!--/col -->
    </div>
    <!--/row -->
  </div>
@endsection
@section('admin.js')
<!-- Update sort_index in table coupons -->
<!-- drag and drop -->
<script>
  const urlUpdateSortIndex = $('#url-update-sort-index').val();

  var fixHelperModified = function(e, tr) {
    var $originals = tr.children();
    var $helper = tr.clone();

    $helper.children().each(function(index) {
      $(this).width($originals.eq(index).width())
    });

    return $helper;
  },
  updateIndex = function(e, ui) {
    $('td.index', ui.item.parent()).each(function (i) {
      $(this).html(i+1);
    });

    var couponIds = [];
    $('tr.js-init-coupon', ui.item.parent()).each(function (i) {
      $(this).attr('data-sort-index', i+1);

      couponIds.push($(this).attr('data-coupon-id'));
    });

    $.ajax({
      url: urlUpdateSortIndex,
      method: 'PUT',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        couponIds: couponIds,
      },
      success: function(result, xhr){
        if (result.success) {}
      },
      error: function(xhr) {}
    });
  };

  $('tbody').sortable({
    helper: fixHelperModified,
    stop: updateIndex
  });
</script><!-- /drag and drop -->

<!-- Change on/off -->
<script>
  $('body').on('click', '.switch-input', function() {
    var couponId = $(this).attr('data-id');
    var urlUpdateIsActive = $(this).attr('data-update-is-active');

    $.ajax({
      url: urlUpdateIsActive,
      method: 'PUT',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        couponId: couponId,
      },
      success: function(result, xhr){
        if (result.success) {}
      },
      error: function(xhr) {}
    });
  });
</script><!-- /Change on/off -->
@endsection

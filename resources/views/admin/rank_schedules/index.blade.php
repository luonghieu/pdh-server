@extends('layouts.admin')
@section('admin.content')
<div class="modal fade" id="popup-rank-schedule" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <p>キャストランクルールを確定しますか？</p>
        <p>"はい"をタップすると、キャストはキャストルールを確認することができます。</p>
      </div>
      <div class="modal-footer">
        <form action="{{ route('admin.rank_schedules.update') }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input type="hidden" id="from-date" name="from_date" value="">
          <input type="hidden" id="to-date" name="to_date" value="">
          <input type="hidden" id="num-of-attend-platium" name="num_of_attend_platium" value="">
          <input type="hidden" id="num-of-avg-rate-platium" name="num_of_avg_rate_platium" value="">
          <input type="hidden" id="num-of-attend-up-platium" name="num_of_attend_up_platium" value="">
          <input type="hidden" id="num-of-avg-rate-up-platium" name="num_of_avg_rate_up_platium" value="">

          <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-accept">はい</button>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-rank-schedule')
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <div class="info-table col-lg-10">
            <table class="table table-bordered table-responsive">
              <!--  table-striped -->
              <tr>
                <th>次回クラス変更期間</th>
                <td>
                  <b class="pull-left">From date</b>
                  <input type="text" class="form-control date-picker from-date" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ $rankSchedule->from_date }}" placeholder="yyyy/mm/dd" />
                </td>
                <td>
                  <b class="pull-left">To date</b>
                  <input type="text" class="form-control date-picker to-date" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ $rankSchedule->to_date }}" placeholder="yyyy/mm/dd" />
                </td>
              </tr>
              <tr>
                <th>プラチナまで</th>
                <td>
                  <b class="pull-left">参加回数</b>
                  <select class="w-option num-of-attend-up-platium" name="num_of_attend_up_platium">
                    @for ($i=0; $i <= 50; $i++)
                    <option value="{{ $i }}" {{ $rankSchedule && $rankSchedule->num_of_attend_up_platium == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                  </select>
                </td>
                <td>
                  <b class="pull-left">平均評価</b>
                  <select class="w-option num-of-avg-rate-up-platium" name="num_of_avg_rate_up_platium">
                    @for ($i=0; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ $rankSchedule && $rankSchedule->num_of_avg_rate_up_platium == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                  </select>
                </td>
              </tr>
              <tr>
                <th>プラチナキープまで</th>
                <td>
                  <b class="pull-left">参加回数</b>
                  <select class="w-option num-of-attend-platium" name="num_of_attend_platium">
                    @for ($i=0; $i <= 50; $i++)
                    <option value="{{ $i }}" {{ $rankSchedule && $rankSchedule->num_of_attend_platium == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                  </select>
                </td>
                <td>
                  <b class="pull-left">平均評価</b>
                  <select class="w-option num-of-avg-rate-platium" name="num_of_avg_rate_platium">
                    @for ($i=0; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ $rankSchedule && $rankSchedule->num_of_avg_rate_platium == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                  </select>
                </td>
              </tr>
            </table>
          </div>
          <div class="col-lg-11">
            <div class="pull-right">
              <button type="button" class="btn btn-info set-rank-schedule" data-toggle="modal" data-target="#popup-rank-schedule">確定する</button>
            </div>
          </div>
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
    $('body').on('click', '.set-rank-schedule', function() {
      var fromDate = $('.from-date').val();
      $('#from-date').val(fromDate);

      var toDate = $('.to-date').val();
      $('#to-date').val(toDate);
      console.log(toDate);
      
      var numOfAttendPlatium = $('.num-of-attend-platium').val();
      $('#num-of-attend-platium').val(numOfAttendPlatium);
      
      var numOfAvgRatePlatium = $('.num-of-avg-rate-platium').val();
      $('#num-of-avg-rate-platium').val(numOfAvgRatePlatium);
      
      var numOfAttendUpPlatium = $('.num-of-attend-up-platium').val();
      $('#num-of-attend-up-platium').val(numOfAttendUpPlatium);
      
      var numOfAvgRateUpPlatium = $('.num-of-avg-rate-up-platium').val();
      $('#num-of-avg-rate-up-platium').val(numOfAvgRateUpPlatium);
    });
  </script>
@stop
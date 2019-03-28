@extends('layouts.admin')
@section('admin.content')
  <div class="col-md-10 col-sm-11 main ">
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          @include('admin.partials.menu-tab-cast', compact('user'))
          <div class="panel-body">
            @include('admin.partials.notification')
            <div class="info-table">
              <p>最終更新日：{{\Carbon\Carbon::parse($updateShiftLatest->pivot->updated_at)->format('m月d日')}}</p>
            </div>
            <div class="info-table col-lg-6">
              <table class="table table-striped table-bordered bootstrap-datatable">
                <thead>
                <tr>
                  <th>日付</th>
                  <th>スケジュール</th>
                </tr>
                </thead>
                <tbody>
                @if (empty($shifts->count()))
                  <tr>
                    <td colspan="8">{{ trans('messages.user_not_found') }}</td>
                  </tr>
                @else
                  @foreach ($shifts as $key => $shift)
                    <tr>
                      <td>{{\Carbon\Carbon::parse($shift->date)->format('m/d'). '(' . dayOfWeek()[\Carbon\Carbon::parse($shift->date)->dayOfWeek] . ')' }}</td>
                      <td>
                        @if($shift->pivot->day_shift)
                          OK
                        @endif

                        @if($shift->pivot->night_shift)
                          {{$shift->pivot->day_shift ? '・': ''}}深夜OK
                        @endif

                        @if(!$shift->pivot->day_shift && !$shift->pivot->night_shift)
                          NG
                        @endif
                      </td>
                    </tr>
                  @endforeach
                @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!--/col-->
    </div>
    <!--/row-->
  </div>
@endsection

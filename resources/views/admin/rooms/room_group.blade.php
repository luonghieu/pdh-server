@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="display-title">
            <p><b>ルームID:</b> {{ request()->room->id }} のキャスト一覧</p>
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
                  </tr>
                </thead>
                <tbody>
                  @if (empty($members->count()))
                    <tr>
                      <td colspan="3">{{ trans('messages.cast_not_found') }}</td>
                    </tr>
                  @else
                    @php
                      $i=1;
                    @endphp
                    @foreach ($members as $key => $member)
                    @if ($member->id != $ownerId)
                    <tr>
                      <td>{{ $i }}</td>
                      <td><a href="{{ route('admin.users.show', ['user' => $member->id]) }}">{{ $member->id }}</a></td>
                      <td>{{ $member->nickname }}</td>
                    </tr>
                    @php
                    $i++;
                    @endphp
                    @endif
                    @endforeach
                  @endif
                </tbody>
              </table>
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

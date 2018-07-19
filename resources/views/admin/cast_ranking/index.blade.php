@extends('layouts.admin')
@section('admin.content')
    <div class="col-md-10 col-sm-11 main">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body handling">
                        <div class="search">
                            <form class="navbar-form navbar-left form-search"
                                  action="{{ route('admin.cast_rankings.index') }}"
                                  method="GET">
                                <input type="text" class="form-control input-search" placeholder="ユーザーID,名前"
                                       name="search" value="{{ request()->search }}">
                                <label for="">From date: </label>
                                <input type="text" class="form-control date-picker input-search" name="from_date"
                                       id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date }}"
                                       placeholder="yyyy/mm/dd"/>
                                <label for="">To date: </label>
                                <input type="text" class="form-control date-picker" name="to_date" id="date01"
                                       data-date-format="yyyy/mm/dd" value="{{ request()->to_date }}"
                                       placeholder="yyyy/mm/dd"/>
                                <button type="submit" class="fa fa-search btn btn-search"></button>
                            </form>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="panel-body">
                        @include('admin.partials.notification')
                        <table class="table table-striped table-bordered bootstrap-datatable">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>順位</th>
                                <th>メインプロフィール画像</th>
                                <th>ユーザーID</th>
                                <th>ニックネーム</th>
                                <th>稼いだ金額</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (empty($casts->count()))
                                <tr>
                                    <td colspan="10">{{ trans('messages.cast_not_found') }}</td>
                                </tr>
                            @else
                                @foreach ($casts as $key => $cast)
                                    <tr>
                                        <td>{{ $casts->firstItem() + $key }}</td>
                                        <td>{{ $casts->firstItem() + $key }}</td>
                                        <td>
                                            <img width="100" src="@foreach ($cast->avatars as $avatar){{$avatar->path}}@endforeach" alt="avatar">
                                        </td>
                                        <td>{{ $cast->id}}</td>
                                        <td>
                                            <a href="{{ route('admin.users.show', ['user' => $cast->id]) }}">{{ $cast->nickname }}</a>
                                        </td>
                                        <td>{{ $cast->point }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12">
                        <div class="dataTables_info" id="DataTables_Table_0_info">
                            @if ($casts->total())
                                全 {{ $casts->total() }}件中 {{ $casts->firstItem() }}~{{ $casts->lastItem() }}件を表示しています
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--/col-->
        </div>
        <!--/row-->
    </div>
@endsection

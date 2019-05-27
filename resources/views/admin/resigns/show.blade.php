@extends('layouts.admin')
@section('admin.content')
    <div class="col-md-10 col-sm-11 main ">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="info-table col-lg-6">
                            <table class="table table-striped table-bordered bootstrap-datatable">
                                <tbody>
                                    <tr>
                                        <td>ユーザーID</td>
                                        <td>{{ $user->id }}</td>
                                    </tr>
                                    <tr>
                                        <td>ユーザー名</td>
                                        <td>{{ $user->nickname }}</td>
                                    </tr>
                                    <tr>
                                        <td>申請日</td>
                                        <td>{{ \Carbon\Carbon::parse($user->resign_date)->format('Y年m月d日') }}</td>
                                    </tr>
                                    <tr>
                                        <td>退会日時</td>
                                        <td>{{ \Carbon\Carbon::parse($user->deleted_at)->format('Y年m月d日　H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td>退会理由</td>
                                        <td>{!! implode(explode('|', $user->first_resign_description), '</br>')  !!}</td>
                                    </tr>
                                    <tr>
                                        <td>その他の退会理由</td>
                                        <td>{{ $user->second_resign_description }}</td>
                                    </tr>
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

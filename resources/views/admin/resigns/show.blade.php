@extends('layouts.admin')
@section('admin.content')
    <div class="col-md-10 col-sm-11 main">
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
                                        <td>{{ ($user->resign_date) ? \Carbon\Carbon::parse($user->resign_date)->format('Y年m月d日') : '' }}</td>
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
                                        <td>{!! ($user->second_resign_description != null) ? nl2br($user->second_resign_description) : '' !!}</td>
                                    </tr>
                                </tbody>
                            </table>
                            @if ($user->resign_status == \App\Enums\ResignStatus::PENDING)
                            <div class="pull-right">
                                <button type="button" data-toggle="modal" data-target="#revert-request-resign" class="btn btn-info">退会申請を取り下げる</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--/col-->
        </div>
        <!--/row-->
    </div>

    <div class="modal fade" id="revert-request-resign" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p>退会申請を取り下げますか？</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.resigns.revert_request', ['resign' => $user->id]) }}" method="post">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-accept">取り下げる</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

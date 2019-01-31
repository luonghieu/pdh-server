@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-cast', compact('user'))
        <div class="panel-body">
          @include('admin.partials.notification')
          <div class="info-table col-lg-6">
            <table class="table table-bordered bootstrap-datatable">
              @if (!$bankAccount)
                <tr>
                  <td colspan="2">{{ trans('messages.bank_account_not_found') }}</td>
                </tr>
              @else
                <tr>
                  <th>銀行名</th>
                  <td>{{ $bankAccount->bank_name }}</td>
                </tr>
                <tr>
                  <th>支店名</th>
                  <td>{{ $bankAccount->branch_name }}</td>
                </tr>
                <tr>
                  <th>口座種別</th>
                  <td>{{ App\Enums\BankAccountType::getDescription($bankAccount->type) }}</td>
                </tr>
                <tr>
                  <th>口座番号</th>
                  <td>{{ $bankAccount->number }}</td>
                </tr>
                <tr>
                  <th>口座名義</th>
                  <td>{{ $bankAccount->holder_name }}</td>
                </tr>
              @endif
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

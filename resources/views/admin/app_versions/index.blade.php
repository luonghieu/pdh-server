@extends('layouts.admin')
@section('admin.content')
  <div class="col-md-10 col-sm-11 main ">
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-body">
            @include('admin.partials.notification')
            <div class="col-lg-8">
              <table class="table table-striped table-bordered bootstrap-datatable">
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Type</th>
                  <th>Version</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                @if (empty($appVersions->count()))
                  <tr>
                    <td colspan="4">{{ trans('messages.results_not_found') }}</td>
                  </tr>
                @else
                  @foreach ($appVersions as $key => $appVersion)
                    <tr>
                      <td>{{ $key + 1 }}</td>
                      <td>{{ App\Enums\AppVersionType::getDescription($appVersion->type) }}</td>
                      <td>{{ $appVersion->version }}</td>
                      <td>
                        @php
                          $type = App\Enums\AppVersionType::getDescription($appVersion->type);
                          $url = route('admin.app_versions.update', ['app_version' => $appVersion->id]);
                        @endphp
                        <button data-toggle="modal" data-type="{{ $type }}" data-target="#app-version-{{ $appVersion->id }}" data-app-version="{{ $appVersion }}" data-url="{{ $url }}" id="popup-app-version" class="btn btn-info">
                          Edit
                        </button>
                      </td>
                    </tr>
                  @endforeach
                @endif
                </tbody>
              </table>
            </div>
            <div class="modal fade modal-app-version" id="" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <p>Edit app version</p>
                  </div>
                  <input type="hidden" id="data-app-version" value="">
                  <form action="#" method="POST" id="update-app-version">
                    {{ csrf_field() }}
                    <div class="modal-body">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Type</th>
                            <th>Version</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td id="type"></td>
                            <td>
                              <div class="form-group">
                                <input type="text" name="version" class="col-sm-12 text-center" id="version" placeholder="Please enter version" value="">
                              </div>
                              <label id="version-error" class="error help-block"></label>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-canceled" data-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-accept">Update</button>
                    </div>
                  </form>
                </div>
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
<script src="/assets/admin/js/appversion/app_version.js"></script>
@stop

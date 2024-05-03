@extends('layouts.app')

@section('title', 'Add Vehicle')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add Vehicle</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Add Vehicle</li>
                        </ol>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- /.card-header -->
                            <form method="POST" action="{{ route('vehicle.submit') }}" enctype="multipart/form-data"
                                id="vehicleForm">

                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Vehicle Number</label>
                                                <input type="text" class="form-control" name="vehicle_no"
                                                    placeholder="AB-1234 / ABC-1234 / 12-1234">
                                                <div class="invalid-feedback d-none" id="error-vehicle_no"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Vehicle Name</label>
                                                <input type="text" class="form-control" name="name"
                                                    placeholder="Delivery Truck">
                                                <div class="invalid-feedback d-none" id="error-name"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Vehicle Type</label>
                                                <select class="form-control" name="type">
                                                    <option value="Lorry">Lorry</option>
                                                    <option value="Van">Van</option>
                                                </select>
                                                <div class="invalid-feedback d-none" id="error-type"></div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="added_by_admin_id" value="1">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit Vehicle</button>
                                </div>
                            </form>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('scripts')
    <script src="{{ asset('js/vehicle-actions.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#vehicleForm').submit(function(event) {
                event.preventDefault(); // Prevent default form submission
                $('#submitBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
                );

                // Clear all previous validation errors
                $('.invalid-feedback').addClass('d-none').text('');
                $('.form-control').removeClass('is-invalid');

                var formData = new FormData(this);

                // Perform form submission via AJAX
                $.ajax({
                    url: '{{ route('vehicle.submit') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#vehicleForm')[0].reset(); // Clear form fields on success
                        } else {
                            if (response.errors) {
                                $.each(response.errors, function(key, value) {
                                    $('#error-' + key).removeClass('d-none').text(value[0]);
                                    $('input[name="' + key + '"], select[name="' + key +
                                        '"]').addClass('is-invalid');
                                });
                            } else {
                                toastr.error(response.message || 'Failed to add vehicle');
                            }
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                    },
                    complete: function() {
                        $('#submitBtn').prop('disabled', false).html('Submit Vehicle');
                    }
                });
            });
        });
    </script>
@endsection

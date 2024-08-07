@extends('layouts.app')

@section('title', 'Batch')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Batch</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">Manage Batch</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('batch.add') }}" class="btn btn-primary">
                                    Add Batch <i class="bi bi-plus-circle-dotted"></i>
                                </a>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <h6><span class="bg-dangerr">
                                        All prices in .LKR format.
                                    </span>
                                </h6>
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Product Code</th>
                                                <th>SKU</th>
                                                <th>Qty</th>
                                                <th>MRP [LKR]</th>
                                                <th>Cash Price [LKR]</th>
                                                <th>Cheque Price [LKR]</th>
                                                <th>Credit Price [LKR]</th>
                                                <th>Expire Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $key => $item)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $item['Product']['name'] ?? 'N/A' }}</td>
                                                    <td>{{ $item['Product']['product_code'] ?? 'N/A' }}</td>
                                                    <td>{{ $item['sku'] ?? 'N/A' }}</td>
                                                    <td>{{ $item['quantity'] ?? 'N/A' }}</td>
                                                    <td>{{ $item['buy_price'] ?? 'N/A' }}</td>
                                                    <td>{{ $item['cash_price'] ?? 'N/A' }}</td>
                                                    <td>{{ $item['check_price'] ?? 'N/A' }}</td>
                                                    <td>{{ $item['credit_price'] ?? 'N/A' }}</td>
                                                    <td>{{ $item['expire_date'] ?? 'N/A' }}</td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="{{ route('batch.edit', $item['id']) }}"
                                                                class="btn btn-secondary btn-sm mr-2">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-batch"
                                                                data-id="{{ $item['id'] }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Product Code</th>
                                                <th>SKU</th>
                                                <th>Qty</th>
                                                <th>MRP [LKR]</th>
                                                <th>Cash Price [LKR]</th>
                                                <th>Cheque Price [LKR]</th>
                                                <th>Credit Price [LKR]</th>
                                                <th>Expire Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
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
@section('scripts')
    <script src="{{ asset('js/batch-action.js') }}"></script>
@endsection

@endsection

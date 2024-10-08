<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\VehicleInventoryController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

Route::post('/store-token', function (Request $request) {
    session(['auth_token' => $request->token]);  // Save the token in the session
    return response()->json(['message' => 'Token stored successfully']);
});

Route::get('/test-token', function () {
    $token = session('auth_token');
    if ($token) {
        dd(session()->all());
        return response()->json(['auth_token' => $token]);
    } else {
        return response()->json(['message' => 'No token found in session.']);
    }
});

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/error', function () {
    return view('pages.error');
});

Route::get('/report-error', function () {
    return view('pages.report-error');
});

Route::get('/login', function () {
    return view('pages.auth.login');
})->name('login');

Route::get('/forgot-password', function () {
    return view('pages.auth.forgotpassword');
});

Route::post('/login', function (Request $request) {
    $validatedData = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $response = Http::post(env('API_URL') . '/admin/login', [
        'email' => $validatedData['email'],
        'password' => $validatedData['password'],
    ]);

    if ($response->successful()) {
        $apiResponse = $response->json();
        session(['email' => $validatedData['email']]);  // Save the email in the session
        return response()->json([
            'message' => $apiResponse['message'] ?? 'Logged in successfully',
            'success' => true,
        ]);
    } else {
        return response()->json([
            'message' => 'Invalid credentials',
            'success' => false,
        ], 401);
    }
});

Route::get('/otp-verification', function () {
    // Check if there's an email in the session
    if (!session()->has('email')) {
        return redirect('/login')->withErrors('No session email available.');
    }

    $email = session('email'); // Get the email from the session
    return view('pages.auth.otp', ['email' => $email]); // Pass it to the view
});

Route::post('/api/proxy/verify-otp', function (Request $request) {
    // Forward the request to the external API
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post(env('API_URL') . '/admin/verify-otp', $request->all());

    // Set CORS headers for the response to the frontend
    return response($response->body(), $response->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')  // Adjust as necessary for security
        ->header('Access-Control-Allow-Methods', 'POST')
        ->header('Access-Control-Allow-Headers', 'Content-Type');
});

// Protected routes
Route::middleware(['web'])->group(function () {

    Route::get('/', [DashboardController::class, 'showDashboard']);

    // Product Routes
    Route::get('/product', [ProductController::class, 'showData'])->name('product.manage');
    Route::delete('/product/{id}', [ProductController::class, 'deleteProduct'])->name('product.delete');
    Route::get('/add-product', [ProductController::class, 'addProductForm'])->name('product.add');
    Route::post('/add-product', [ProductController::class, 'submitProduct'])->name('product.submit');
    Route::get('/product/edit/{id}', [ProductController::class, 'editProductForm'])->name('product.edit');
    Route::put('/product/update/{id}', [ProductController::class, 'updateProduct'])->name('product.update');

    //batch route
    Route::get('/batch', [BatchController::class, 'showData'])->name('batch.manage');
    Route::delete('/batch/{id}', [BatchController::class, 'deleteBatch'])->name('batch.delete');
    Route::get('/add-batch', [BatchController::class, 'addBatchForm'])->name('batch.add');
    Route::post('/add-batch', [BatchController::class, 'submitBatch'])->name('batch.submit');
    Route::get('/batch/edit/{id}', [BatchController::class, 'editBatchForm'])->name('batch.edit');
    Route::put('/batch/update/{id}', [BatchController::class, 'updateBatch'])->name('batch.update');

    // Routes for managing vehicles
    Route::get('/vehicle', [VehicleController::class, 'showData'])->name('vehicle.manage');
    Route::delete('/vehicle/{id}', [VehicleController::class, 'deleteVehicle'])->name('vehicle.delete');
    Route::get('/add-vehicle', [VehicleController::class, 'addVehicleForm'])->name('vehicle.add');
    Route::post('/add-vehicle', [VehicleController::class, 'submitVehicle'])->name('vehicle.submit');
    Route::get('/vehicle/edit/{id}', [VehicleController::class, 'editVehicleForm'])->name('vehicle.edit');
    Route::put('/vehicle/update/{id}', [VehicleController::class, 'updateVehicle'])->name('vehicle.update');


    //Routes for managing routes
    Route::view('/map', 'pages.maps.index')->name('map');
    Route::post('/route/store', [MapController::class, 'submitRoute'])->name('route.store');
    Route::get('/route', [MapController::class, 'showData'])->name('route.manage');
    Route::get('/route/edit/{id}', [MapController::class, 'editRouteForm'])->name('route.edit');
    Route::put('/route/update/{id}', [MapController::class, 'updateRoute'])->name('route.update');
    Route::delete('/route/{id}', [MapController::class, 'deleteRoute'])->name('route.delete');
    Route::get('/client/locations', [MapController::class, 'showClientLocations'])->name('client.locations');

    //vehicle inventory
    Route::get('/vehicle-inventory', [VehicleInventoryController::class, 'showVehicleInventory'])->name('vehicle.inventory');
    Route::delete('/vehicle-inventory/{id}', [VehicleInventoryController::class, 'delete'])->name('vehicle-inventory.delete');
    Route::get('/add-vehicle-inventory', [VehicleInventoryController::class, 'addVehicleInventoryForm'])->name('vehicle-inventory.add');
    Route::post('/add-vehicle-inventory', [VehicleInventoryController::class, 'submitVehicleInventory'])->name('vehicle-inventory.submit');
    Route::get('/vehicle-inventory/{id}', [VehicleInventoryController::class, 'editVehicleInventoryForm'])->name('vehicle-inventory.edit');
    Route::put('/vehicle-inventory/{id}', [VehicleInventoryController::class, 'updateVehicleInventory'])->name('vehicle-inventory.update');

    //Routes for managing Assignments
    Route::get('/assignment', [AssignmentController::class, 'showAssignments'])->name('assignment.manage');
    Route::get('/add-assignment', [AssignmentController::class, 'addAssignmentForm'])->name('assignment.add');
    Route::post('/add-assignment', [AssignmentController::class, 'submitAssignment'])->name('assignment.submit');
    Route::get('/assignment/edit/{id}', [AssignmentController::class, 'editAssignmentForm'])->name('assignment.edit');
    Route::put('/assignment/edit/{id}', [AssignmentController::class, 'updateAssignment'])->name('assignment.update');
    Route::delete('/assignment/{id}', [AssignmentController::class, 'deleteAssignment'])->name('assignment.delete');
    Route::get('/tracking', [AssignmentController::class, 'showTodayAssignments'])->name('emp.tracking');
    Route::get('/employee/{employeeId}/location', [AssignmentController::class, 'getEmployeeLocation']);
    Route::get('/clients/route/{routeId}', [AssignmentController::class, 'getClientsByRoute']);
    Route::get('/clients/route/{routeId}', [AssignmentController::class, 'getClientsByRoute'])
        ->name('getClientsByRoute');

    //Route for sales report
    Route::get('/sales-report', [ReportController::class, 'showSales'])->name('sales.show');
    Route::get('/api/sales/report', [ReportController::class, 'getSalesReport']);

    // Route for commission report
    Route::get('/commission-report', [ReportController::class, 'showCommission'])->name('commission.show');
    Route::get('/api/commission/report', [ReportController::class, 'getCommissionReport']);


    //tracking..
    Route::get('/tracking', [AssignmentController::class, 'showTodayAssignments'])->name('emp.tracking');
    Route::get('/employee/{employeeId}/location', [AssignmentController::class, 'getEmployeeLocation']);

    //Route for outstanding report
    Route::get('/outstanding-report', [ReportController::class, 'outstandingSales'])->name('outstanding.show');
    Route::get('/api/outstanding/report', [ReportController::class, 'getOutstandingReport']);

    // Route for day end report
    Route::get('/day-end-report', [ReportController::class, 'show'])->name('day-end.show');
    Route::get('/api/day-end/report', [ReportController::class, 'getDayEndReport']);

    //mrp report
    Route::get('/mrp-report', [ReportController::class, 'showMrpReport'])->name('mrp-report.show');
    Route::get('/api/mrp-data', [ReportController::class, 'getMrpReportData'])->name('mrp-report.data');

    // Client Routes
    Route::get('/client', [ClientController::class, 'showData'])->name('client.manage');
    Route::delete('/client/{id}', [ClientController::class, 'deleteClient'])->name('client.delete');
    Route::get('/add-client', [ClientController::class, 'addClientForm'])->name('client.add');
    Route::post('/add-client', [ClientController::class, 'submitClient'])->name('client.submit');
    Route::get('/client/edit/{id}', [ClientController::class, 'editClientForm'])->name('client.edit');
    Route::put('/client/update/{id}', [ClientController::class, 'updateClient'])->name('client.update');
    Route::post('/client/toggle-status/{id}', [ClientController::class, 'toggleClientStatus'])->name('client.toggle-status');

    // Employee Routes
    Route::get('/employee', [EmployeeController::class, 'showData'])->name('employee.manage');
    Route::get('/add-employee', [EmployeeController::class, 'addEmployeeForm'])->name('employee.add');
    Route::post('/employee/submit', [EmployeeController::class, 'submitEmployee'])->name('employee.submit');
    Route::delete('/employee/{id}', [EmployeeController::class, 'deleteEmployee'])->name('employee.delete');
    Route::put('/employee/update-commission/{id}', [EmployeeController::class, 'updateCommissionRate']);

    // Route for trash records view
    Route::get('/trash', [TrashController::class, 'showTrash'])->name('trash.show');
    // Route for fetching deleted records by model
    Route::get('/api/trash/deletedRecords/{model}', [TrashController::class, 'getDeletedRecords']);
    Route::put('/api/trash/restore/{model}/{id}', [TrashController::class, 'restoreRecord']);


    //Invoice
    Route::get('/invoices', [InvoiceController::class, 'showInvoices'])->name('invoices.show');
    Route::delete('/invoice/{id}', [InvoiceController::class, 'deleteInvoice'])->name('invoice.delete');

    //payment
    Route::get('/payment', [PaymentController::class, 'showPayments'])->name('payment.manage');
    Route::put('/payment/toggle-state/{id}', [PaymentController::class, 'togglePaymentState'])->name('payment.toggle-state');
    Route::delete('/payment/{id}', [PaymentController::class, 'deletePayment'])->name('payment.delete');
    Route::get('/all-payments', [PaymentController::class, 'showAllPayments'])->name('payment.all');

    // User Settings routes
    Route::post('/admin/admin/password-change', [AdminController::class, 'changePassword'])->name('password.update');
    Route::post('/admin/delete-account', [AdminController::class, 'deleteAccount'])->name('account.delete');
});


//logout
Route::get('/logout', function () {
    session()->forget('auth_token');
    return redirect('/login')->with('message', 'Successfully logged out');
})->name('logout');

Route::post('/api/proxy/forgot-password', function (Request $request) {
    // Debugging: Log the request
    \Log::info('Forgot password request received', $request->all());

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post(env('API_URL') . '/admin/forgot-password', $request->all());

    \Log::info('Forgot password response', $response->json());

    return response($response->body(), $response->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'POST')
        ->header('Access-Control-Allow-Headers', 'Content-Type');
});

Route::post('/api/proxy/verify-otp', function (Request $request) {
    // Debugging: Log the request
    \Log::info('Verify OTP request received', $request->all());

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post(env('API_URL') . '/admin/verify-otp', $request->all());

    \Log::info('Verify OTP response', $response->json());

    return response($response->body(), $response->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'POST')
        ->header('Access-Control-Allow-Headers', 'Content-Type');
});

Route::post('/api/proxy/change-password', function (Request $request) {
    // Debugging: Log the request
    \Log::info('Change password request received', $request->all());

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post(env('API_URL') . '/admin/password-change', $request->all());

    \Log::info('Change password response', $response->json());

    return response($response->body(), $response->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'POST')
        ->header('Access-Control-Allow-Headers', 'Content-Type');
});


Route::put('/api/proxy/vehicle-inventory/toggle-looked/{id}', function (Request $request, $id) {
    \Log::info('Toggle looked request received for ID: ' . $id, $request->all());

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->put(env('API_URL') . '/vehicle-inventory/toggle-looked/' . $id, $request->all());

    \Log::info('Response from external API', ['response' => $response->json()]);

    return response()->json($response->json(), $response->status())
        ->header('Content-Type', 'application/json')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'PUT')
        ->header('Access-Control-Allow-Headers', 'Content-Type');
});

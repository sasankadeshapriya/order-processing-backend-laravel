@extends('layouts.app')

@section('title', 'Manage Routes')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manage Routes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Manage Routes</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('map') }}" class="btn btn-primary">
                                Add Route <i class="bi bi-plus-circle-dotted"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Route Name</th>
                                        <th>Waypoints</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($routes as $key => $route)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $route['name'] }}</td>
                                        <td data-waypoints='{{ $route["waypoints"] }}'>
                                            <button type="button" class="btn btn-info btn-sm view-map-btn">View Map</button>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('route.edit', $route['id']) }}" class="btn btn-secondary btn-sm mr-2">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm delete-route" data-id="{{ $route['id'] }}">
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
                                        <th>Route Name</th>
                                        <th>Waypoints</th>
                                        <th>Actions</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Route Map</h5>
            </div>
            <div class="modal-body">
                <div id="popupMap" style="height: 400px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

    <script src="{{ asset('js/route-action.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}" async defer></script>
    <script>
        // Event handler for clicking the third column of a table row
        $('#example1 tbody').on('click', 'td:nth-child(3)', function() {
            var waypoints = $(this).data('waypoints'); // Extract waypoints data

            $('#mapModal').modal('show'); // Show modal with map
            $('#mapModal').on('shown.bs.modal', function() {
                initMapModal(waypoints); // Initialize map in modal after it is shown
            });
        });

        function initMapModal(waypoints) {
            var map = new google.maps.Map(document.getElementById('popupMap'), {
                center: { lat: waypoints[0].latitude, lng: waypoints[0].longitude }, // Set map center
                zoom: 12 // Set zoom level
            });

            var directionsService = new google.maps.DirectionsService();
            var directionsRenderer = new google.maps.DirectionsRenderer({
                draggable: false,
                map: map,
                panel: document.getElementById('directionsPanel') // Set panel for directions
            });

            calculateAndDisplayRoute(directionsService, directionsRenderer, waypoints); // Calculate and display the route
        }

        function calculateAndDisplayRoute(directionsService, directionsRenderer, waypoints) {
            var waypointMarkers = waypoints.map(point => ({
                location: new google.maps.LatLng(point.latitude, point.longitude), // Create waypoints
                stopover: true
            }));

            var origin = waypointMarkers.shift().location; // Set the first waypoint as origin
            var destination = waypointMarkers.pop().location; // Set the last waypoint as destination

            directionsService.route({
                origin: origin,
                destination: destination,
                waypoints: waypointMarkers, // Set intermediate waypoints
                travelMode: 'DRIVING' // Set travel mode
            }, function(response, status) {
                if (status === 'OK') {
                    directionsRenderer.setDirections(response); // Render the directions
                } else {
                    window.alert('Directions request failed due to ' + status); // Alert on failure
                }
            });
        }
        </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=&v=weekly" async defer></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- DataTables  & Plugins -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../../plugins/jszip/jszip.min.js"></script>
    <script src="../../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
@endsection

$(document).ready(function() {
    $('.delete-payment').click(function() {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#C8B400',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                if (!navigator.onLine) {
                    toastr.error('No internet connection. Unable to delete payment.');
                    return; // Stop further execution
                }

                $.ajax({
                    url: '/payment/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('payment deleted successfully');
                            $('#example1').DataTable().row($('button[data-id="' + id + '"]').closest('tr')).remove().draw();
                        } else {
                            toastr.error(response.message || 'Failed to delete payment.');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error: ' + (xhr.responseJSON.message || xhr.statusText));
                    }
                });
            }
        });
    });
});

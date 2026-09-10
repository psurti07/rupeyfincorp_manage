@extends('layouts.manage')
@section('title', 'Webinar Customers')

@push('css-links')
@include('stacks.css.manage.datatables')
@endpush
@push('style-css')
<style>
    #webinar-table_length {
        margin-left: 50px;
    }

    /* Style for outline buttons */
    .join-community-btn {
        min-width: 100px;
        /* Optional: consistent width */
        transition: all 0.3s ease;
    }

    /* Not Joined state - Blue outline */
    .join-community-btn.btn-outline-primary {
        color: #007bff;
        background-color: transparent;
        border-color: #007bff;
    }

    .join-community-btn.btn-outline-primary:hover {
        color: #fff;
        background-color: #007bff !important;
        border-color: #007bff !important;
    }

    /* Joined state - Green outline */
    .join-community-btn.btn-outline-success {
        color: #28a745;
        background-color: transparent;
        border-color: #28a745;
    }

    .join-community-btn.btn-outline-success:hover {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
    }

    /* Active/Focus state to remove outline ring */
    .join-community-btn:focus {
        box-shadow: none;
        outline: none;
    }
</style>
@endpush

@section('breadcrumb-title')
<h3>Webinar Customers</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{!! config('dashboard.name') !!}</li>
<li class="breadcrumb-item active">Webinar Customers</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row g-3">
        <div class="col-md-2 position-relative">
            <label class="form-label">From Date</label>
            <input class="form-control" type="date" name="fromdate" id="fromdate" value="{{ date('Y-m-d',strtotime('-2 days')) }}">
        </div>
        <div class="col-md-2 position-relative">
            <label class="form-label">To Date</label>
            <input class="form-control" type="date" name="todate" id="todate" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-2 position-relative">
            <button type="button" class="mt-4 btn btn-outline-warning" id="dateBtn">Show</button>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="userDetailsModals"></div>
@endsection

@push('script-src')
@include('stacks.js.manage.datatables')
@endpush
@push('script-tag')
{{ $dataTable->scripts(attributes:['type' => 'module']) }}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).on("init.dt", '#webinar-table', function() { // Ensures full page load
        let today = new Date();
        let twoDaysBefore = new Date();
        twoDaysBefore.setDate(today.getDate() - 2);

        let formatDate = (date) => date.toISOString().split('T')[0]; // Format YYYY-MM-DD

        let fromDate = sessionStorage.getItem('from_date') || new URLSearchParams(window.location.search).get('from_date') || formatDate(twoDaysBefore);
        let toDate = sessionStorage.getItem('to_date') || new URLSearchParams(window.location.search).get('to_date') || formatDate(today);

        // Set date input fields
        $('#fromdate').val(fromDate);
        $('#todate').val(toDate);

        let table = $("#webinar-table").DataTable(); // Get existing DataTable instance

        table.on('preXhr.dt', function(e, settings, data) {
            data.start_date = $("#fromdate").val();
            data.end_date = $("#todate").val();
        });

        // 🚀 Reload only if session storage had data
        if (sessionStorage.getItem('from_date') || sessionStorage.getItem('to_date')) {
            setTimeout(() => {
                table.ajax.reload(null, false);
            }, 500); // Delay reload slightly after full page load
        }

        // Remove session storage after setting values
        sessionStorage.removeItem('from_date');
        sessionStorage.removeItem('to_date');

        // Click event for manually refreshing the table
        $('#dateBtn').on('click', function() {
            table.ajax.reload(null, false);
            return false;
        });
    });

    function deleteCustomer(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This record will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = "{{ route('manage.webinar.customer.delete', ':id') }}";
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Deleted!',
                            'Record has been deleted.',
                            'success'
                        );

                        // reload datatable
                        $('.dataTable').DataTable().ajax.reload();
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Something went wrong.',
                            'error'
                        );
                    }
                });

            }
        });
    }

    $(document).on('click', '.join-community-btn', function() {
        let btn = $(this);
        let userId = btn.data('userid');
        let url = btn.data('url');

        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.type === 'SUCCESS') {
                    toastr.success(res.message);
                    setTimeout(function() {
                        if (res.status) {
                            // Switch to green outline
                            btn.removeClass('btn-outline-primary').addClass('btn-outline-success');
                            btn.text('Joined');
                        } else {
                            // Switch to blue outline
                            btn.removeClass('btn-outline-success').addClass('btn-outline-primary');
                            btn.text('Not Joined');
                        }
                        $('.dataTable').DataTable().ajax.reload();
                    }, 2000);
                } else {
                    toastr.error(res.message || 'Action failed');
                }
            },
            error: function() {
                toastr.error('Failed to update join community status');
            }
        });
    });

    function viewDetails(userId) {
        let url = "{{ route('manage.webinar.customer.show', ':id') }}";
        url = url.replace(':id', userId);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(html) {
                $('.userDetailsModals').html(html);
                $('#viewWebinarUsers').modal('show');
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error('Failed to load lead details');
            }
        });
    }

    $(document).on('click', '#accDeactivateBtn', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to deactivate this Webinar User.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((willDelete) => {
            if (!willDelete) return;
            let userId = $(this).data('userid');

            let url = "{{ route('manage.webinar.leads.accDeactivate', ':id') }}";
            url = url.replace(':id', userId);

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {
                    toastr.success(response.message);
                    $("#viewWorkshopLeads").modal('hide');
                    $('#webinar-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    toastr.error("Something went wrong!");
                    console.log(xhr.responseText);
                }
            });
        });
    });

    $(document).on('click', '#accDeleteBtn', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this Webinar User.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((willDelete) => {
            if (!willDelete) return;
            let userId = $(this).data('userid');

            let url = "{{ route('manage.webinar.leads.accDelete', ':id') }}";
            url = url.replace(':id', userId);

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {
                    toastr.success(response.message);
                    $("#viewWorkshopLeads").modal('hide');
                    $('#webinar-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    toastr.error("Something went wrong!");
                    console.log(xhr.responseText);
                }
            });
        });
    });

</script>
@endpush
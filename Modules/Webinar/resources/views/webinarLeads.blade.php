@extends('layouts.manage')
@section('title', 'Webinar Leads')

@push('css-links')
@include('stacks.css.manage.datatables')
@endpush
@push('style-css')
<style>
    #WebinarLeads-table_length {
        margin-left: 50px;
    }
</style>
@endpush

@section('breadcrumb-title')
<h3>Webinar Leads</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{!! config('dashboard.name') !!}</li>
<li class="breadcrumb-item active">Webinar Leads</li>
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
    $(document).on("init.dt", '#WebinarLeads-table', function() { // Ensures full page load
        let today = new Date();
        let twoDaysBefore = new Date();
        twoDaysBefore.setDate(today.getDate() - 2);

        let formatDate = (date) => date.toISOString().split('T')[0]; // Format YYYY-MM-DD

        let fromDate = sessionStorage.getItem('from_date') || new URLSearchParams(window.location.search).get('from_date') || formatDate(twoDaysBefore);
        let toDate = sessionStorage.getItem('to_date') || new URLSearchParams(window.location.search).get('to_date') || formatDate(today);

        // Set date input fields
        $('#fromdate').val(fromDate);
        $('#todate').val(toDate);

        let table = $("#WebinarLeads-table").DataTable(); // Get existing DataTable instance

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

    function deleteLead(id) {
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
                let url = "{{ route('manage.webinar.lead.delete', ':id') }}";
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

    function viewDetails(userId) {
        let url = "{{ route('manage.webinar.leads.show', ':id') }}";
        url = url.replace(':id', userId);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(html) {
                $('.userDetailsModals').html(html);
                $('#viewWorkshopLeads').modal('show');
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error('Failed to load lead details');
            }
        });
    }
</script>
@endpush
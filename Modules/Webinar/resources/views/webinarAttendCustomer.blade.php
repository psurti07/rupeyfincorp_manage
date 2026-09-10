@extends('layouts.manage')
@section('title', 'Webinar Attend Customers')

@push('css-links')
@include('stacks.css.manage.datatables')
@endpush
@push('style-css')
<style>
    #webinarattend-table_length {
        margin-left: 50px;
    }

    .attend-btn.btn-success {
        background-color: #28a745;
        color: white;
    }

    .attend-btn.btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    .attend-btn.btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
    }
</style>
@endpush

@section('breadcrumb-title')
<h3>Webinar Attend Customers</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{!! config('dashboard.name') !!}</li>
<li class="breadcrumb-item active">Webinar Attend Customers</li>
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
    $(document).on("init.dt", '#webinarattend-table', function() { // Ensures full page load
        let today = new Date();
        let twoDaysBefore = new Date();
        twoDaysBefore.setDate(today.getDate() - 2);

        let formatDate = (date) => date.toISOString().split('T')[0]; // Format YYYY-MM-DD

        let fromDate = sessionStorage.getItem('from_date') || new URLSearchParams(window.location.search).get('from_date') || formatDate(twoDaysBefore);
        let toDate = sessionStorage.getItem('to_date') || new URLSearchParams(window.location.search).get('to_date') || formatDate(today);

        // Set date input fields
        $('#fromdate').val(fromDate);
        $('#todate').val(toDate);

        let table = $("#webinarattend-table").DataTable(); // Get existing DataTable instance

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

    $(document).ready(function() {
        // Attend button handler
        $(document).on('click', '.attend-btn', function() {
            let btn = $(this);
            let id = btn.data('id');

            $.ajax({
                url: "{{ route('manage.webinar.customer.attend.status', ':id') }}".replace(':id', id),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.type === 'SUCCESS') {
                        if (res.status) {
                            btn.removeClass('btn-outline-secondary').addClass('btn-success');
                            btn.text('Attended');
                        } else {
                            btn.removeClass('btn-success').addClass('btn-outline-secondary');
                            btn.text('Not Attended');
                        }
                        toastr.success(res.message);
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to update attend status');
                }
            });
        });

        $(document).on('click', '.isdnd-btn', function() {

            let btn = $(this);
            let id = btn.data('id');

            $.ajax({
                url: "{{ route('manage.webinar.customer.isdnd.status', ':id') }}".replace(':id', id),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {

                    if (res.type === 'SUCCESS') {

                        if (res.status) {

                            btn.removeClass('btn-outline-danger')
                                .addClass('btn-outline-success')
                                .html('<i class="fa fa-check-circle"></i> DND');

                        } else {

                            btn.removeClass('btn-outline-success')
                                .addClass('btn-outline-danger')
                                .html('<i class="fa fa-times-circle"></i> Not DND');
                        }

                        toastr.success(res.message);

                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to update DND status');
                }
            });
        });
    });
</script>
@endpush
@extends('layouts.manage')
@section('title', 'Program List')

@push('css-links')
@include('stacks.css.manage.datatables')
@endpush
@push('style-css')
<style>
    #webinarEventDetails-table_length {
        margin-left: 50px;
    }
</style>
@endpush

@section('breadcrumb-title')
<h3>Program List</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">{!! config('dashboard.name') !!}</li>
<li class="breadcrumb-item active">Program List</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row g-3">
        <div class="col-12 text-end">
            <button onclick="openEventModal()" class="btn btn-primary" id="addEventBtn"><i class="fa fa-plus"></i>&nbsp;Add Event</button>
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
<script>
    $(document).on("init.dt", '#webinarEventDetails-table', function() { // Ensures full page load
        let today = new Date();
        let twoDaysBefore = new Date();
        twoDaysBefore.setDate(today.getDate() - 2);

        let table = $("#webinarEventDetails-table").DataTable(); // Get existing DataTable instance

        // Click event for manually refreshing the table
        $('#dateBtn').on('click', function() {
            table.ajax.reload(null, false);
            return false;
        });
    });

    function openEventModal() {
        $.ajax({
            url: "{!! route('manage.webinar.event.create') !!}",
            type: 'GET',
            contentType: "application/json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#addEventBtn').html('<span class="spinner-border spinner-border-sm"></span> Loading...');
                $('#addEventBtn').attr('disabled', true);
            },
            success: function(result) {
                $('.userDetailsModals').html(result);
                $('#eventModal').modal('show');

                $('#addEventBtn').html('<i class="la la-plus"></i>&nbsp;Add Event');
                $('#addEventBtn').attr('disabled', false);
            }
        });
    }

    function openEditModal(id) {
        $.ajax({
            url: "{{ route('manage.webinar.event.edit', ':id') }}".replace(':id', id),
            type: 'GET',
            success: function(response) {
                $('.userDetailsModals').html(response);
                $('#eventModal').modal('show');
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }

    function toggleWebinarStatus(id) {
        if (!id) return;
        $.ajax({
            url: 'program-toggle-status/' + id,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.type === 'SUCCESS') {
                    $('#webinarEventDetails-table').DataTable().ajax.reload(null, false);
                    toastr.success(response.message);
                } else {
                    alert(response.message || 'Failed to update status');
                }
            },
            error: function() {
                alert('Failed to update status');
            }
        });
    }
</script>
@endpush
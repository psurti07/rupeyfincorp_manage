<div class="modal fade" id="viewWorkshopLeads" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewWorkshopLeadsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewWorkshopLeadsLabel">Lead &amp; Program Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <style>
                .my-card {
                    border: 1px solid #e2e2e2;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                }
            </style>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="card">
                            <div class="card-body my-card">
                                <h5>User Details</h5>
                                <hr>
                                @if ($user)
                                <div class="row">
                                    <div class="col-md-6 col-lg-6 col-sm-12" style="border-right:1px solid #e3e3e3">
                                        <dl class="row">
                                            <dt class="col-sm-4">Date:</dt>
                                            <dd class="col-sm-8">{{ $user->user_rec_date ? \Carbon\Carbon::parse($user->user_rec_date)->format('d-m-Y h:i:s A') : '-' }}</dd>
                                        </dl>
                                        <hr />
                                        <dl class="row">
                                            <dt class="col-sm-4">Full Name:</dt>
                                            <dd class="col-sm-8">{{ $user->first_name }} {{ $user->last_name }}</dd>
                                        </dl>
                                        <hr />

                                        <dl class="row">
                                            <dt class="col-sm-4">Email:</dt>
                                            <dd class="col-sm-8">{{ $user->email ?? '-' }}</dd>
                                        </dl>
                                        <hr />

                                        <dl class="row">
                                            <dt class="col-sm-4">Mobile:</dt>
                                            <dd class="col-sm-8">{{ $user->mobile ?? '-' }}</dd>
                                        </dl>
                                        <hr />
                                        <dl class="row">
                                            <dt class="col-sm-4">Earning Goals:</dt>
                                            <dd class="col-sm-8">{{ $user->earning_goal ?? '-' }}</dd>
                                        </dl>
                                        <hr />
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-sm-12">
                                        <dl class="row">
                                            <dt class="col-sm-4">Pincode:</dt>
                                            <dd class="col-sm-8">{{ $user->pincode ?? '-' }}</dd>
                                        </dl>
                                        <hr />
                                        <dl class="row">
                                            <dt class="col-sm-4">City:</dt>
                                            <dd class="col-sm-8">{{ $user->city ?? '-' }}</dd>
                                        </dl>
                                        <hr />

                                        <dl class="row">
                                            <dt class="col-sm-4">State:</dt>
                                            <dd class="col-sm-8">{{ $user->state ?? '-' }}</dd>
                                        </dl>
                                        <hr />

                                        <dl class="row">
                                            <dt class="col-sm-4">Current Occupation:</dt>
                                            <dd class="col-sm-8">{{ $user->occupation ?? '-' }}</dd>
                                        </dl>
                                        <hr />
                                    </div>
                                </div>

                                @if(false)
                                <div class="text-end mt-4">
                                    <button class="btn btn-outline-warning me-2 accDeactivateBtn">
                                        Deactivate Account
                                    </button>

                                    <button class="btn btn-outline-danger">
                                        Delete Account
                                    </button>
                                </div>
                                @endif
                                @else
                                <p class="text-center text-danger mb-0">User not found.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="card">
                            <div class="card-body my-card">
                                <h5>Program Details</h5>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-striped table-border">
                                        <thead>
                                            <th>#</th>
                                            <th>Banner</th>
                                            <th>Name</th>
                                            <th>Speaker</th>
                                            <th>Language</th>
                                        </thead>
                                        <tbody>

                                            @php
                                            $languages = [
                                            1 => 'English',
                                            2 => 'Hindi',
                                            3 => 'Gujarati'
                                            ];
                                            @endphp

                                            @if($orders->count())
                                            @foreach($orders as $index => $order)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>

                                                <td>
                                                    @if($order->event_image)
                                                    <img src="{{ asset('webinar/images/webinarpage/'.$order->event_image) }}" width="50">
                                                    @else
                                                    <span class="text-muted">No Image</span>
                                                    @endif
                                                </td>

                                                <td>{{ $order->event_title }}</td>
                                                <td>{{ $order->mentor_name }}</td>
                                                <td>{{ $order->language ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="5" class="text-center text-danger">
                                                    No Program Found.
                                                </td>
                                            </tr>
                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /* Account operations */
    $("#accDeactivateBtn").on('click', function() {
        swal({
            title: "Are you sure?",
            text: "You want to deactivate this Webinar User.",
            icon: "warning",
            buttons: ["Cancel", "Confirm"],
            dangerMode: true,
        }).then((willDelete) => {
            if (!willDelete) return;
            let userId = $(this).data('userid');

            $.ajax({
                url: `{{ route('manage.webinar.leads.accDeactivate') }}`,
                type: "POST",
                data: {
                    user_id: userId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    $("#viewWorkshopLeads").modal('hide');
                    $('#WebinarLeads-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert("Something went wrong!");
                    console.log(xhr.responseText);
                }
            });
        });
    });


    $("#accDeleteBtn").on('click', function() {
        swal({
            title: "Are you sure?",
            text: "You want to delete this Webinar User.",
            icon: "warning",
            buttons: ["Cancel", "Confirm"],
            dangerMode: true,
        }).then((willDelete) => {
            if (!willDelete) return;
            let userId = $(this).data('userid');

            $.ajax({
                url: `{{ route('manage.webinar.leads.accDelete') }}`,
                type: "POST",
                data: {
                    user_id: userId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    $("#viewWorkshopLeads").modal('hide');
                    $('#WebinarLeads-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    alert("Something went wrong!");
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>
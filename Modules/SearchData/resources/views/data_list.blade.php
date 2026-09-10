@if($userData && !in_array($module,[4,5]))
<div class="row g-3">
    <div class="col-md-12 d-flex align-items-center border p-3 rounded">
        <label for="module" class="fw-bold me-2 mb-0">Module:</label>
        <span id="module">
            @if($module == 4)
            Webinar
            @elseif($module == 5)
            Workshop
            @elseif($userData->acc_type == 1)
            Self Apply
            @elseif($userData->acc_type == 2)
            Loan Agent
            @elseif($userData->acc_type == 3)
            Loan Assistant
            @else
            -
            @endif
        </span>
    </div>
    <div class="col-md-12 d-flex align-items-center border p-3 rounded">
        <label for="mobile" class="fw-bold me-2 mb-0">Mobile:</label>
        <span id="mobile">{{ $userData->mobile ?? '-' }}</span>
    </div>
    <div class="col-md-12 d-flex align-items-center border p-3 rounded">
        <label for="date" class="fw-bold me-2 mb-0">Date:</label>
        <span id="date">{{ date('d-m-Y h:i:s',strtotime($userData->rec_date)) ?? '-' }}</span>
    </div>
    <div class="col-md-12 d-flex align-items-center border p-3 rounded">
        <label for="fullname" class="fw-bold me-2 mb-0">Fullname:</label>
        <span id="fullname">
            @if($userData->first_name || $userData->last_name)
            {{ $userData->first_name ?? '-' }} {{ $userData->last_name ?? '-' }}
            @else
            -
            @endif
        </span>
    </div>
    <div class="col-md-12 d-flex align-items-center border p-3 rounded">
        <label for="email" class="fw-bold me-2 mb-0">Email ID:</label>
        <span id="email">{{ $userData->email ?? '-' }}</span>
    </div>
    <div class="col-md-12 d-flex align-items-center border p-3 rounded">
        <label for="fullname" class="fw-bold me-2 mb-0">Source:</label>
        @if($userData->isUser == 1 && $userData->isDelete == 0)
        <span id="fullname" class="text-warning">Data as a Company Lead.</span>
        @elseif($userData->isUser == 2 && $userData->isDelete == 0)
        <span id="fullname" class="text-success">Registred as a customer.</span>
        @elseif($userData->isDelete == 1)
        <span id="fullname" class="text-danger">Account is deleted.</span>
        @else

        @endif
    </div>
    <div class="col-12 mt-3">
        @if($userData->acc_type == 1)
        @if($userData->isUser == 1 && $userData->isDelete == 0)
        <a href="{{ route('manage.selfapply.company.leads') }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @elseif($userData->isUser == 2 && $userData->isDelete == 0)
        <a href="{{ route('manage.selfapply.customer.details',$userData->id) }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @else
        <a href="javascript:;">
            <button type="submit" id="submit" class="btn btn-outline-primary" disabled>More Details</button>
        </a>
        @endif
        @elseif($userData->acc_type == 2)
        @if($userData->isUser == 1 && $userData->isDelete == 0)
        <a href="{{ route('manage.loanagent.company.leads') }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @elseif($userData->isUser == 2 && $userData->isDelete == 0)
        <a href="{{ route('manage.loanagent.customer.details',$userData->id) }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @else
        <a href="javascript:;">
            <button type="submit" id="submit" class="btn btn-outline-primary" disabled>More Details</button>
        </a>
        @endif
        @elseif($userData->acc_type == 3)
        @if($userData->isUser == 1 && $userData->isDelete == 0)
        <a href="{{ route('manage.loanassistant.company.leads') }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @elseif($userData->isUser == 2 && $userData->isDelete == 0)
        <a href="{{ route('manage.loanassistant.customer.details',$userData->id) }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @else
        <a href="javascript:;">
            <button type="submit" id="submit" class="btn btn-outline-primary" disabled>More Details</button>
        </a>
        @endif
        @elseif($module == 4)
        @if($userData->isUser == 1 && $userData->isDelete == 0)
        <a href="{{ route('manage.webinar.leads') }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @elseif($userData->isUser == 2 && $userData->isDelete == 0)
        <a href="{{ route('manage.webinar') }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @else
        <a href="javascript:;">
            <button type="submit" id="submit" class="btn btn-outline-primary" disabled>More Details</button>
        </a>
        @endif
        @elseif($module == 5)
        @if($userData->isUser == 1 && $userData->isDelete == 0)
        <a href="{{ route('manage.workshop.leads') }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @elseif($userData->isUser == 2 && $userData->isDelete == 0)
        <a href="{{ route('manage.workshop.customer') }}">
            <button type="submit" id="submit" class="btn btn-outline-primary">More Details</button>
        </a>
        @else
        <a href="javascript:;">
            <button type="submit" id="submit" class="btn btn-outline-primary" disabled>More Details</button>
        </a>
        @endif
        @endif
    </div>
</div>
@elseif($userData && in_array($module,[4,5]))
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="serchdata-table" class="table table-bordered " style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Mobile</th>
                        <th>Fullname</th>
                        <th>User</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($userData as $key => $row)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ date('d-m-Y H:i:s', strtotime($row->order_date)) }}</td>
                        <td>{{ $row->mobile }}</td>
                        <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                        <td>
                            @if($row->isUser == 1)
                            <span class="badge bg-warning">Lead</span>
                            @elseif($row->isUser == 2)
                            <span class="badge bg-success">Customer</span>
                            @endif
                        </td>
                        <td>
                            @if($module == 4)
                            @if($row->isUser == 1)
                            <a href="{{ route('manage.webinar.leads') }}"
                                class="btn btn-sm btn-primary">
                                Details
                            </a>
                            @else
                            <a href="{{ route('manage.webinar') }}"
                                class="btn btn-sm btn-success">
                                Details
                            </a>
                            @endif
                            @elseif($module == 5)
                            @if($row->isUser == 1)
                            <a href="{{ route('manage.workshop.leads') }}"
                                class="btn btn-sm btn-primary">
                                Details
                            </a>
                            @else
                            <a href="{{ route('manage.workshop.customer') }}"
                                class="btn btn-sm btn-success">
                                Details
                            </a>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card-body p-0">
    <p class="text-center text-danger">No data available.</p>
</div>
@endif
<script>
         /* Customer forms update end */
    $('#serchdata-table').DataTable({
        responsive: true,
        searching: false,
        pageLength: 50,
        lengthChange: false
    });
</script>
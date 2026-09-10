<div class="modal fade" id="eventModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form id="eventForm" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5>Create Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <label>Program Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="eventdate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Program Type <span class="text-danger">*</span></label>
                            <div class="form-check-size rtl-input">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input me-2" id="inlineRadio1" type="radio" name="program_type" value="0" checked="">
                                    <label class="form-check-label" for="inlineRadio1">Webinar</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input me-2" id="inlineRadio2" type="radio" name="program_type" value="1">
                                    <label class="form-check-label" for="inlineRadio2">Workshop</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Program name <span class="text-danger">*</span></label>
                            <input type="text" name="eventname" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Program Title <span class="text-danger">*</span></label>
                            <input type="text" name="event_title" class="form-control">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Program Main Price <span class="text-danger">*</span></label>
                            <input type="text" name="mainprice" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Program Offer Price <span class="text-danger">*</span></label>
                            <input type="text" name="offerprice" class="form-control">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Program Mentor Name <span class="text-danger">*</span></label>
                            <input type="text" name="event_mentor" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Program Language <span class="text-danger">*</span></label>
                            <select name="event_lag" class="form-control">
                                <option value="">Select</option>
                                <option value="english">English</option>
                                <option value="hindi">Hindi</option>
                                <option value="gujarati">Gujarati</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Program Image <span class="text-danger">*</span></label>
                            <input type="file" name="event_image" class="form-control">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label>Program Description <span class="text-danger">*</span></label>
                        <textarea name="event_desc" class="form-control" id="editor_event"></textarea>
                    </div>

                    <div class="mt-3">
                        <label>Program Link <span class="text-danger">*</span></label>
                        <input type="url" name="link" class="form-control" placeholder="https://www.example.com/">
                    </div>

                    <div class="mt-3">
                        <label>Community Link <span class="text-danger">*</span></label>
                        <input type="url" name="community_link" class="form-control" placeholder="https://www.example.com/">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>
<script src="{{asset('assets/js/editor/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('assets/js/editor/ckeditor/adapters/jquery.js')}}"></script>
<script src="{{asset('assets/js/editor/ckeditor/styles.js')}}"></script>
<script src="{{asset('assets/js/editor/ckeditor/ckeditor.custom.js')}}"></script>
<script>
    $('#eventForm').submit(function(e) {
        e.preventDefault();

        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        let form = $(this);

        let submitBtn = form.find('button[type="submit"]');

        if (submitBtn.prop('disabled')) {
            return false;
        }
        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('manage.webinar.event.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            beforeSend: function() {
                submitBtn.prop('disabled', true);
                submitBtn.data('original-text', submitBtn.html());

                submitBtn.html(`
                <span class="spinner-border spinner-border-sm me-1"></span> Saving... `);
            },

            success: function(res) {
                toastr.success(res.message);

                $('#eventModal').modal('hide');
                $('#eventForm')[0].reset();

                $('#webinarEventDetails-table').DataTable().ajax.reload();
            },

            error: function(err) {
                let errors = err.responseJSON.errors;
                $.each(errors, function(key, val) {
                    toastr.error(val[0]);
                });
            }
        });
    });
    setTimeout(function() {
        if (CKEDITOR.instances['editor_event']) {
            CKEDITOR.instances['editor_event'].destroy(true);
        }

        CKEDITOR.replace('editor_event');
    }, 300);
</script>
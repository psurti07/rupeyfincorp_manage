<div class="modal fade" id="eventModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form id="eventForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $event->id ?? '' }}">
                <div class="modal-header">
                    <h5>Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6">
                            <label>Program Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="eventdate" value="{{ $event->event_datetime ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Program Type <span class="text-danger">*</span></label>
                            <div class="form-check-size rtl-input">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input me-2" id="inlineRadio1" type="radio" name="program_type" value="0" {{ isset($event) && $event->program_type == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inlineRadio1">Webinar</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input me-2" id="inlineRadio2" type="radio" name="program_type" value="1" {{ isset($event) && $event->program_type == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inlineRadio2">Workshop</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Program Name <span class="text-danger">*</span></label>
                            <input type="text" name="eventname" value="{{ $event->event_name ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Program Title <span class="text-danger">*</span></label>
                            <input type="text" name="event_title" value="{{ $event->event_title ?? '' }}" class="form-control">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Program Main Price <span class="text-danger">*</span></label>
                            <input type="text" name="mainprice" value="{{ $event->event_main_price ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Program Offer Price <span class="text-danger">*</span></label>
                            <input type="text" name="offerprice" value="{{ $event->event_offer_price ?? '' }}" class="form-control">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Program Mentor Name <span class="text-danger">*</span></label>
                            <input type="text" name="event_mentor" value="{{ $event->mentor_name ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Program Language <span class="text-danger">*</span></label>
                            <select name="event_lag" class="form-control">
                                <option value="">Select</option>
                                <option value="english" {{ ($event->language ?? '')=='english'?'selected':'' }}>English</option>
                                <option value="hindi" {{ ($event->language ?? '')=='hindi'?'selected':'' }}>Hindi</option>
                                <option value="gujarati" {{ ($event->language ?? '')=='gujarati'?'selected':'' }}>Gujarati</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Program Image </label>
                            <input type="file" name="event_image" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <img src="{{ asset('webinar/images/webinarpage/' . $event->event_image) }}"
                                 width="180" id="edit_imagePreview" class="mt-3 rounded border">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label>Program Description <span class="text-danger">*</span></label>
                        <textarea name="event_desc" id="editor_event" class="form-control">{{ $event->event_desc_1 ?? '' }}</textarea>
                    </div>

                    <div class="mt-3">
                        <label>Program Link <span class="text-danger">*</span></label>
                        <input type="url" name="link" class="form-control"  value="{{ $event->link ?? '' }}">
                    </div>

                    <div class="mt-3">
                        <label>Community Link <span class="text-danger">*</span></label>
                        <input type="url" name="community_link" class="form-control" value="{{ $event->community_link ?? '' }}">
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

        let id = $('input[name=id]').val();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('manage.webinar.event.update', ':id') }}".replace(':id', id),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(res) {
                toastr.success(res.message);

                $('#eventModal').modal('hide');
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
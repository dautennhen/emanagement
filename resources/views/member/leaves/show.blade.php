<div id="event-detail">

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><i class="ti-eye"></i> @lang('app.menu.leaves') @lang('app.details')</h4>
    </div>
    <div class="modal-body">
        {!! Form::open(['id'=>'updateEvent','class'=>'ajax-form','method'=>'GET']) !!}
        <div class="form-body">
            <div class="row">
                <div class="col-md-12 ">
                    <div class="form-group">
                        <label>@lang('modules.leaves.applicantName')</label>
                        <p>
                            {{ ucwords($leave->user->name) }}
                        </p>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-xs-12 ">
                    <div class="form-group">
                        <label>@lang('app.date')</label>
                        <p>{{ $leave->leave_date->format($global->date_format) }} <label class="label label-{{ $leave->type->color }}">{{ ucwords($leave->type->type_name) }}</label>
                            @if($leave->duration == 'half day')
                                <label class="label label-info">{{ ucwords($leave->duration) }}</label>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 ">
                    <div class="form-group">
                        <label>@lang('modules.leaves.reason')</label>
                        <p>{!! $leave->reason !!}</p>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>@lang('app.status')</label>
                        <p>
                            @if($leave->status == 'approved')
                                <strong class="text-success">{{ mb_convert_case(__('app.approved'),MB_CASE_TITLE,"UTF-8") }}</strong>
                            @elseif($leave->status == 'pending')
                                <strong class="text-warning">{{ mb_convert_case(__('app.pending'),MB_CASE_TITLE,"UTF-8") }}</strong>
                            @else
                                <strong class="text-danger">{{ mb_convert_case(__('app.rejected'),MB_CASE_TITLE,"UTF-8") }}</strong>
                            @endif

                        </p>
                    </div>
                </div>

                @if(!is_null($leave->reject_reason))
                    <div class="col-md-12 ">
                        <div class="form-group">
                            <label>@lang('app.reject') @lang('app.reason')</label>
                            <p>{!! $leave->reject_reason !!}</p>
                        </div>
                    </div>
                @endif
    

            </div>
        </div>
        {!! Form::close() !!}

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-white waves-effect" data-dismiss="modal">{{ ucwords(__('app.close')) }}</button>
        @if($leave->status == 'pending')
            <button type="button" class="btn btn-danger btn-outline delete-event waves-effect waves-light"><i class="fa fa-times"></i> @lang('app.delete')</button>
            <button type="button" class="btn btn-info save-event waves-effect waves-light"><i class="fa fa-edit"></i> @lang('app.edit')</button>
        @endif
    </div>

</div>

<script>

    $('.save-event').click(function () {
        $.easyAjax({
            url: '{{route('member.leaves.edit', $leave->id)}}',
            container: '#updateEvent',
            type: "GET",
            data: $('#updateEvent').serialize(),
            success: function (response) {
                if(response.status == 'success'){
                    $('#event-detail').html(response.view);
                }
            }
        })
    })

    $('.delete-event').click(function(){
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted leave application!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('member.leaves.destroy', $leave->id) }}";

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                            url: url,
                            data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });


</script>
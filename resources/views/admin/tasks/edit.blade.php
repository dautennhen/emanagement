@extends('layouts.app')
@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ __($pageTitle) }}</h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
            <li><a href="{{ route('admin.all-tasks.index') }}">{{ __($pageTitle) }}</a></li>
            <li class="active">@lang('app.edit')</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection
 @push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">

<style>
    .panel-black .panel-heading a,
    .panel-inverse .panel-heading a {
        color: unset!important;
    }
</style>

@endpush
@section('content')

<div class="row">
    <div class="col-md-12">

        <div class="panel panel-inverse">
            <div class="panel-heading"> @lang('modules.tasks.updateTask')</div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    {!! Form::open(['id'=>'updateTask','class'=>'ajax-form','method'=>'PUT']) !!}

                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.title')</label>
                                    <input type="text" id="heading" name="heading" class="form-control" value="{{ $task->heading }}">
                                </div>
                            </div>
                            @if(in_array('projects', $modules))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.project')</label>
                                        <select class="select2 form-control" data-placeholder="@lang('app.selectProject')" id="project_id" name="project_id">
                                                <option value=""></option>
                                                @foreach($projects as $project)
                                                    <option
                                                            @if($project->id == $task->project_id) selected @endif
                                                            value="{{ $project->id }}">{{ ucwords($project->project_name) }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.tasks.taskCategory') <a
                                        href="javascript:;" id="createTaskCategory"
                                        class="btn btn-xs btn-outline btn-success"><i
                                            class="fa fa-plus"></i> @lang('modules.taskCategory.addTaskCategory')</a>
                                            </label>
                                    <select class="selectpicker form-control" name="category_id" id="category_id" data-style="form-control">
                                                @forelse($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                            @if($task->task_category_id == $category->id)
                                                            selected
                                                            @endif
                                                    >{{ ucwords($category->category_name) }}</option>
                                                @empty
                                                    <option value="">@lang('messages.noTaskCategoryAdded')</option>
                                                @endforelse
                                            </select>
                                </div>
                            </div>

                            <!--/span-->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.description')</label>
                                    <textarea id="description" name="description" class="form-control summernote">{{ $task->description }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">

                                    <div class="checkbox checkbox-info">
                                        <input id="private-task" name="is_private" value="true"
                                        @if ($task->is_private)
                                            checked
                                        @endif
                                               type="checkbox">
                                        <label for="private-task">@lang('modules.tasks.makePrivate') <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tasks.privateInfo')</span></span></span></a></label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">

                                    <div class="checkbox checkbox-info">
                                        <input id="billable-task" name="billable" value="true"
                                        @if ($task->billable)
                                            checked
                                        @endif
                                               type="checkbox">
                                        <label for="billable-task">@lang('modules.tasks.billable') <a class="mytooltip font-12" href="javascript:void(0)"> <i class="fa fa-info-circle"></i><span class="tooltip-content5"><span class="tooltip-text3"><span class="tooltip-inner2">@lang('modules.tasks.billableInfo')</span></span></span></a></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">

                                    <div class="checkbox checkbox-info">
                                        <input id="dependent-task" name="dependent" value="yes"
                                               type="checkbox" @if($task->dependent_task_id != '') checked @endif>
                                        <label for="dependent-task">@lang('modules.tasks.dependent')</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="dependent-fields" @if($task->dependent_task_id == null) style="display: none" @endif>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.tasks.dependentTask')</label>
                                        <select class="select2 form-control" data-placeholder="@lang('modules.tasks.chooseTask')" name="dependent_task_id" id="dependent_task_id" >
                                            <option value=""></option>
                                            @foreach($allTasks as $allTask)
                                                <option value="{{ $allTask->id }}" @if($allTask->id == $task->dependent_task_id) selected @endif>{{ $allTask->heading }} (@lang('app.dueDate'): {{ $allTask->due_date->format($global->date_format) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">@lang('app.label')
                                            <a href="javascript:;"
                                               id="createTaskLabel"
                                               class="btn btn-xs btn-outline btn-success">
                                                <i class="fa fa-plus"></i> @lang('app.add') @lang('app.menu.taskLabel')
                                            </a>
                                        </label>
                                            <select id="multiselect" name="task_labels[]" title="{{ mb_convert_case(__('app.Nothingselected'), MB_CASE_TITLE, "UTF-8") }}" multiple="multiple" class="selectpicker form-control">
                                            @foreach($taskLabels as $label)
                                                <option data-content="<label class='badge b-all' style='background:{{ $label->label_color }};'>{{ $label->label_name }}</label> " value="{{ $label->id }}">{{ $label->label_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.startDate')</label>
                                    <input type="text" name="start_date" id="start_date2" class="form-control" autocomplete="off" value="@if($task->start_date != '-0001-11-30 00:00:00' && $task->start_date != null) {{ $task->start_date->format($global->date_format) }} @endif">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">@lang('app.dueDate')</label>
                                    <input type="text" name="due_date" id="due_date2" class="form-control" autocomplete="off" value="@if($task->due_date != '-0001-11-30 00:00:00') {{ $task->due_date->format($global->date_format) }} @endif">
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.tasks.assignTo')</label>
                                    <select class="select2 select2-multiple " multiple="multiple"
                                            data-placeholder="@lang('modules.tasks.chooseAssignee')"
                                            name="user_id[]" id="user_id">
                                        @if(is_null($task->project_id))
                                            @foreach($employees as $employee)

                                                @php
                                                    $selected = '';
                                                @endphp

                                                @foreach ($task->users as $item)
                                                    @if($item->id == $employee->id)
                                                        @php
                                                            $selected = 'selected';
                                                        @endphp
                                                    @endif

                                                @endforeach

                                                <option {{ $selected }}
                                                        value="{{ $employee->id }}">{{ ucwords($employee->name) }}
                                                </option>

                                            @endforeach
                                        @else
                                            @foreach($task->project->members as $member)
                                                @php
                                                    $selected = '';
                                                @endphp

                                                @foreach ($task->users as $item)
                                                    @if($item->id == $member->user->id)
                                                        @php
                                                            $selected = 'selected';
                                                        @endphp
                                                    @endif

                                                @endforeach

                                                <option {{ $selected }}
                                                    value="{{ $member->user->id }}">{{ $member->user->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('app.status')</label>
                                    <select name="status" id="status" class="form-control">
                                        @foreach($taskBoardColumns as $taskBoardColumn)
                                            <option @if($task->board_column_id == $taskBoardColumn->id) selected @endif value="{{$taskBoardColumn->id}}">@if($taskBoardColumn->column_name=='Complete'||$taskBoardColumn->column_name=='Completed'||$taskBoardColumn->column_name=='Incomplete'){{ ucwords(__('app.'.$taskBoardColumn->column_name)) }}@else{{ $taskBoardColumn->column_name }}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--/span-->
                            <!--/span-->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">@lang('modules.tasks.priority')</label>

                                    <div class="radio radio-danger">
                                        <input type="radio" name="priority" id="radio13" @if($task->priority == 'high') checked
                                        @endif value="high">
                                        <label for="radio13" class="text-danger">
                                                @lang('modules.tasks.high') </label>
                                    </div>
                                    <div class="radio radio-warning">
                                        <input type="radio" name="priority" @if($task->priority == 'medium') checked @endif
                                        id="radio14" value="medium">
                                        <label for="radio14" class="text-warning">
                                                @lang('modules.tasks.medium') </label>
                                    </div>
                                    <div class="radio radio-success">
                                        <input type="radio" name="priority" id="radio15" @if($task->priority == 'low') checked
                                        @endif value="low">
                                        <label for="radio15" class="text-success">
                                                @lang('modules.tasks.low') </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row m-b-20">
                                <div class="col-md-12">
                                    @if($upload)
                                        <button type="button" class="btn btn-block btn-outline-info btn-sm col-md-2 select-image-button" style="margin-bottom: 10px;display: none "><i class="fa fa-upload"></i> File Select Or Upload</button>
                                        <div id="file-upload-box" >
                                            <div class="row" id="file-dropzone">
                                                <div class="col-md-12">
                                                    <div class="dropzone"
                                                         id="file-upload-dropzone">
                                                        {{ csrf_field() }}
                                                        <div class="fallback">
                                                            <input name="file" type="file" multiple/>
                                                        </div>
                                                        <input name="image_url" id="image_url"type="hidden" />
                                                        <div class="dz-default dz-message">
                                                            <span>{{ mb_convert_case(__('app.Dropfilesheretoupload'), MB_CASE_TITLE, "UTF-8") }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="taskID" id="taskID">
                                    @else
                                        <div class="alert alert-danger">@lang('messages.storageLimitExceed', ['here' => '<a href='.route('admin.billing.packages'). '>Here</a>'])</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!--/row-->
                        <div class="row" id="list">
                            <ul class="list-group" id="files-list">
                                @forelse($task->files as $file)
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-md-9">
                                                {{ $file->filename }}
                                            </div>
                                            <div class="col-md-3">

                                                    <a target="_blank" href="{{ asset_url_local_s3('task-files/'.$task->id.'/'.$file->hashname) }}"
                                                       data-toggle="tooltip" data-original-title="View"
                                                       class="btn btn-info btn-circle"><i
                                                                class="fa fa-search"></i></a>


                                                @if(is_null($file->external_link))
                                                    &nbsp;&nbsp;
                                                    <a href="{{ route('member.task-files.download', $file->id) }}"
                                                       data-toggle="tooltip" data-original-title="Download"
                                                       class="btn btn-default btn-circle"><i
                                                                class="fa fa-download"></i></a>
                                                @endif
                                                &nbsp;&nbsp;
                                                <a href="javascript:;" data-toggle="tooltip"
                                                   data-original-title="Delete"
                                                   data-file-id="{{ $file->id }}"
                                                   class="btn btn-danger btn-circle sa-params" data-pk="list"><i
                                                            class="fa fa-times"></i></a>

                                                <span class="m-l-10">{{ $file->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-md-10">
                                                @lang('messages.noFileUploaded')
                                            </div>
                                        </div>
                                    </li>
                                @endforelse

                            </ul>
                        </div>

                    </div>
                    <div class="form-actions">
                        <button type="button" id="update-task" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- .row -->

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in" id="taskCategoryModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->.
</div>
{{--Ajax Modal Ends--}}

{{--Ajax Modal Ends--}}
<div class="modal fade bs-modal-lg in" id="taskLabelModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->.
</div>
{{--Ajax Modal Ends--}}
@endsection
 @push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.js') }}"></script>


<script>
    $('#multiselect').selectpicker();

    @if($labelIds)
        var labelIds = {{ json_encode($labelIds) }};
        $('#multiselect').selectpicker('val', labelIds);
    @endif

    @if($upload)
        Dropzone.autoDiscover = false;
        //Dropzone class
        myDropzone = new Dropzone("div#file-upload-dropzone", {
            url: "{{ route('admin.task-files.store') }}",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            paramName: "file",
            maxFilesize: 10,
            maxFiles: 10,
            acceptedFiles: "image/*,application/pdf",
            autoProcessQueue: false,
            uploadMultiple: true,
            addRemoveLinks:true,
            parallelUploads:10,
            init: function () {
                myDropzone = this;
                this.on("success", function (file, response) {
                    if(response.status == 'fail') {
                        $.showToastr(response.message, 'error');
                        return;
                    }
                })
            }
        });

        myDropzone.on('sending', function(file, xhr, formData) {
            //console.log(myDropzone.getAddedFiles().length,'sending');
            var ids = '{{ $task->id }}';
            formData.append('task_id', ids);
        });

        myDropzone.on('successmultiple', function () {
        var msgs = "@lang('messages.taskUpdatedSuccessfully')";
        $.showToastr(msgs, 'success');
        window.location.href = '{{ route('admin.all-tasks.index') }}'

    });
    @endif
    //    update task
    $('#update-task').click(function () {

        var status = '{{ $task->board_column->slug }}';
        var currentStatus =  $('#status').val();

        if(status == 'incomplete' && currentStatus == 'completed'){

            $.easyAjax({
                url: '{{route('admin.tasks.checkTask', [$task->id])}}',
                type: "GET",
                data: {},
                success: function (data) {
                    //console.log(data.taskCount);
                    if(data.taskCount > 0){
                        swal({
                            title: "Are you sure?",
                            text: "There is a incomplete sub-task in this task do you want to mark complete!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes, complete it!",
                            cancelButtonText: "No, cancel please!",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        }, function (isConfirm) {
                            if (isConfirm) {
                                updateTask();
                            }
                        });
                    }
                    else{
                        updateTask();
                    }

                }
            });
        }
        else{
            updateTask();
        }

    });

    function updateTask(){
        $.easyAjax({
            url: '{{route('admin.all-tasks.update', [$task->id])}}',
            container: '#updateTask',
            type: "POST",
            data: $('#updateTask').serialize(),
            success: function(response){
                var dropzone = 0;
                @if($upload)
                    dropzone = myDropzone.getQueuedFiles().length;
                @endif

                if(dropzone > 0){
                    taskID = response.taskID;
                    $('#taskID').val(response.taskID);
                    myDropzone.processQueue();
                }
                else{
                    var msgs = "@lang('messages.taskCreatedSuccessfully')";
                    $.showToastr(msgs, 'success');
                    window.location.href = '{{ route('admin.all-tasks.index') }}'
                }
            }
        })
    }

    jQuery('#due_date2, #start_date2').datepicker({
        autoclose: true,
        todayHighlight: true,
        weekStart:'{{ $global->week_start }}',
        format: '{{ $global->date_picker_format }}',
    });

    $(".select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('.summernote').summernote({
        height: 200,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ["view", ["fullscreen"]]
        ]
    });

    $('body').on('click', '.sa-params', function () {
        var id = $(this).data('file-id');
        var deleteView = $(this).data('pk');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted file!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('admin.task-files.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE', 'view': deleteView},
                    success: function (response) {
                        //console.log(response);
                        if (response.status == "success") {
                            $.unblockUI();
                            $('#list ul.list-group').html(response.html);

                        }
                    }
                });
            }
        });
    });

    $('#project_id').change(function () {
        var id = $(this).val();

        // For getting dependent task
        var dependentTaskUrl = '{{route('admin.all-tasks.dependent-tasks', [':id', ':taskId'])}}';
        dependentTaskUrl = dependentTaskUrl.replace(':id', id);
        dependentTaskUrl = dependentTaskUrl.replace(':taskId', '{{ $task->id }}');
        $.easyAjax({
            url: dependentTaskUrl,
            type: "GET",
            success: function (data) {
                $('#dependent_task_id').html(data.html);
            }
        })
    });
    $('#dependent-task').change(function () {
        if ($(this).is(':checked')) {
            $('#dependent-fields').show();
        } else {
            $('#dependent-fields').hide();
        }
    })
</script>
<script>
    $('#createTaskCategory').click(function(){
        var url = '{{ route('admin.taskCategory.create-cat')}}';
        $('#modelHeading').html("@lang('modules.taskCategory.manageTaskCategory')");
        $.ajaxModal('#taskCategoryModal', url);
    })

    $('#createTaskLabel').click(function(){
        var url = '{{ route('admin.task-label.create-label')}}';
        $('#modelHeading').html("");
        $.ajaxModal('#taskLabelModal', url);
    })

</script>

@endpush

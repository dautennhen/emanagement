<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<div class="rpanel-title"> @lang('app.task') #{{ $task->id }} <span><i class="ti-close right-side-toggle"></i></span> </div>
<div class="r-panel-body">

    <div class="row">
        <div class="col-xs-12">
            @if ($task->board_column->slug != 'completed')

                @if (is_null($task->activeTimer) && !is_null($task->is_task_user))
                    <a href="javascript:;" data-toggle="tooltip" id="start-task-timer"
                    data-original-title="@lang('modules.timeLogs.startTimer')" class="btn btn-success btn-sm m-b-10 btn-rounded btn-outline pull-right m-l-5"> <i class="fa fa-play"></i></a>
                @elseif (!is_null($task->activeTimer) && !is_null($task->is_task_user))
                    <a href="javascript:;" data-toggle="tooltip" id="stop-task-timer"
                    data-original-title="@lang('app.stop')" data-time-id="{{ $task->activeTimer->id }}" class="btn btn-inverse btn-sm m-b-10 btn-rounded btn-outline pull-right m-l-5 w-100"> <span id="active-task-timer" class="b-r m-r-5 p-r-5">{{ $task->activeTimer->timer }}</span> <i class="fa fa-stop text-danger"></i></a>
                @endif
            @endif

            <a href="javascript:;" id="completedButton" class="btn btn-success btn-sm m-b-10 btn-rounded btn-outline @if($task->board_column->slug == 'completed') hidden @endif "  onclick="markComplete('completed')" ><i class="fa fa-check"></i> @lang('modules.tasks.markComplete')</a>
            <a href="javascript:;" id="inCompletedButton" class="btn btn-default btn-outline btn-sm m-b-10 btn-rounded @if($task->board_column->slug != 'completed') hidden @endif"  onclick="markComplete('incomplete')"><i class="fa fa-times"></i> @lang('modules.tasks.markIncomplete')</a>
            @if($task->board_column->slug != 'completed' && $user->cans('edit_tasks'))
                <a href="javascript:;" id="reminderButton" class="btn btn-info btn-sm m-b-10 btn-rounded btn-outline pull-right" title="@lang('messages.remindToAssignedEmployee')"><i class="fa fa-envelope"></i> @lang('modules.tasks.reminder')</a>
            @endif
            @if ($user->cans('edit_tasks'))
                <a href="{{route('member.all-tasks.edit',$task->id)}}" class="btn btn-info btn-sm m-b-10 btn-rounded btn-outline pull-right m-r-5"> <i class="fa fa-edit"></i> @lang('app.edit')</a>                   
            @endif
        </div>
        <div class="col-xs-12">
            <h5>{{ ucwords($task->heading) }}
                @if($task->task_category_id)
                    <label class="label label-default text-dark m-l-5 font-light">{{ ucwords($task->category->category_name) }}</label>
                @endif

                <label class="m-l-5 font-light label
            @if($task->priority == 'high')
                        label-danger
                    @elseif($task->priority == 'medium')
                        label-warning
                    @else
                        label-success
                    @endif
                        ">
                    <span class="text-dark">@lang('modules.tasks.priority') ></span>  {{ ucfirst($task->priority) }}
                </label>

            </h5>
            @if(!is_null($task->project_id))
                <p><i class="icon-layers"></i> {{ ucfirst($task->project->project_name) }}</p>
            @endif
        </div>

        <ul class="nav customtab nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home1" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true">@lang('app.task')</a></li>
            <li role="presentation" class=""><a href="#profile1" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false" id="count_sub_task">@lang('modules.tasks.subTask')({{ count($task->subtasks) }})</a></li>
            <li role="presentation" class=""><a href="#messages1" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false">@lang('app.file') ({{ sizeof($task->files) }})</a></li>
            <li role="presentation" class=""><a href="#settings1" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false">@lang('modules.tasks.comment') ({{ count($task->comments) }})</a></li>
            <li role="presentation" class="pull-right">  <a href="javascript:;" id="view-task-history" data-task-id="{{ $task->id }}" class="pull-right text-muted font-12 btn btn-sm btn-default btn-outline"> <i class="fa fa-history"></i> <span class="hidden-xs">@lang('app.view') @lang('modules.tasks.history')</span></a></li>
            <li role="presentation" class="pull-right" >  <a href="javascript:;" class="close-task-history pull-right text-muted" style="display:none"><span class="hidden-xs">@lang('app.close') @lang('modules.tasks.history')</span> <i class="fa fa-times"></i></a></li>
        </ul>

        <div class="tab-content" id="task-detail-section">
            <div role="tabpanel" class="tab-pane fade active in" id="home1">
                <div class="col-xs-12" >
                    <div class="col-xs-6 col-md-3 font-12">
                        <label class="font-12" for="">@lang('modules.tasks.assignTo')</label><br>
                         @foreach ($task->users as $item)
                            <img src="{{ $item->image_url }}" data-toggle="tooltip" data-original-title="{{ ucwords($item->name) }}" data-placement="right" class="img-circle" width="25" height="25" alt="">
                            {{-- {{ucwords($item->name)}} --}}
                        @endforeach
                    </div>
                    @if($task->created_by)
                    <div class="col-xs-6 col-md-3 font-12">
                        <label class="font-12" for="">@lang('modules.tasks.assignBy')</label><br>
                        <img src="{{ $task->create_by->image_url }}" data-toggle="tooltip" data-original-title="{{ ucwords($task->create_by->name) }}" class="img-circle" width="25" height="25" alt="">

                        {{-- {{ ucwords($task->create_by->name) }} --}}
                    </div>
                    @endif

                    @if($task->start_date)
                    <div class="col-xs-6 col-md-3 font-12">
                        <label class="font-12" for="">@lang('app.startDate')</label><br>
                        <span class="text-success" >{{ $task->start_date->format($global->date_format) }}</span><br>
                    </div>
                    @endif
                    <div class="col-xs-6 col-md-3 font-12">
                        <label class="font-12" for="">@lang('app.dueDate')</label><br>
                        <span @if($task->due_date->isPast()) class="text-danger" @endif>
                            {{ $task->due_date->format($global->date_format) }}
                        </span>

                        <span style="color: {{ $task->board_column->label_color }}" id="columnStatus"> @if($task->board_column->column_name=='Complete'||$task->board_column->column_name=='Completed'||$task->board_column->column_name=='Incomplete'){{ mb_convert_case(__('app.'.$task->board_column->column_name),MB_CASE_TITLE,"UTF-8") }}@else{{ $task->board_column->column_name }}@endif</span>
                    </div>

                    @if(sizeof($task->label))
                        <div class="col-xs-6 col-md-12 font-12 m-t-10 m-b-10">
                            <label class="font-12" for="">@lang('app.label')</label><br>
                            <span>
                                @foreach($task->label as $key => $label)
                                    <label class="badge" style="background:{{ $label->label->label_color }}">{{ ucwords($label->label->label_name) }} </label>
                                @endforeach
                            </span>
                        </div>
                    @endif

                    <div class="col-xs-12 col-md-12 m-t-10">
                        <label class="font-12" for="">@lang('app.description')</label><br>
                        <div class="col-xs-12 task-description b-all p-10 m-t-20">
                            {!! ucfirst($task->description) !!}
                        </div>

                    </div>

                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="profile1">
                <div class="col-xs-12 m-t-5">
                    <h5><i class="ti-check-box"></i> @lang('modules.tasks.subTask')
                        @if (count($task->subtasks) > 0)
                            <span class="pull-right"><span class="donut" data-peity='{ "fill": ["#00c292", "#eeeeee"],    "innerRadius": 5, "radius": 8 }'>{{ count($task->completedSubtasks) }}/{{ count($task->subtasks) }}</span> <span class="text-muted font-12">{{ floor((count($task->completedSubtasks)/count($task->subtasks))*100) }}%</span></span>
                        @endif

                    </h5>

                    <ul class="list-group b-t" id="sub-task-list">
                        @foreach($task->subtasks as $subtask)
                            <li class="list-group-item row">
                                <div class="col-xs-9">
                                    <div class="checkbox checkbox-success checkbox-circle task-checkbox">
                                        <input class="task-check" data-sub-task-id="{{ $subtask->id }}" id="checkbox{{ $subtask->id }}" type="checkbox"
                                               @if($subtask->status == 'complete') checked @endif>
                                        <label for="checkbox{{ $subtask->id }}">&nbsp;</label>
                                        <span>{{ ucfirst($subtask->title) }}</span>
                                    </div>
                                    @if($subtask->due_date)<span class="text-muted m-l-5"> - @lang('modules.invoices.due'): {{ $subtask->due_date->format($global->date_format) }}</span>@endif
                                </div>

                                <div class="col-xs-3 text-right">
                                    <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" class="edit-sub-task"><i class="fa fa-pencil"></i></a>&nbsp;
                                    <a href="javascript:;" data-sub-task-id="{{ $subtask->id }}" class="delete-sub-task"><i class="fa fa-trash"></i></a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-xs-12 m-t-20 m-b-10">
                    <a href="javascript:;"  data-task-id="{{ $task->id }}" class="add-sub-task"><i class="icon-plus"></i> @lang('app.add') @lang('modules.tasks.subTask')</a>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="messages1">
                <div class="col-xs-12 m-t-10">
                    <a href="javascript:;" id="uploadedFiles" class="btn btn-primary btn-sm btn-rounded btn-outline"><i class="fa fa-image"></i> @lang('modules.tasks.uplodedFiles') (<span id="totalUploadedFiles">{{ sizeof($task->files) }}</span>) </a>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="settings1">


                <div class="col-xs-12 m-t-15">
                    <h5>@lang('modules.tasks.comment')</h5>
                </div>

                <div class="col-xs-12" id="comment-container">
                    <div id="comment-list">
                        @forelse($task->comments as $comment)
                            <div class="row b-b m-b-5 font-12">
                                <div class="col-xs-12">
                                    <h5>{{ ucwords($comment->user->name) }} <span class="text-muted font-12">{{ ucfirst($comment->created_at->diffForHumans()) }}</span></h5>
                                </div>
                                <div class="col-xs-10">
                                    {!! ucfirst($comment->comment)  !!}
                                </div>
                                @if($comment->user->id == $user->id || $user->cans('delete_tasks'))
                                    <div class="col-xs-2 text-right">
                                        <a href="javascript:;" data-comment-id="{{ $comment->id }}" class="text-danger " onclick="deleteComment('{{ $comment->id }}');return false;">@lang('app.delete')</a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-xs-12">
                                @lang('messages.noRecordFound')
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="form-group" id="comment-box">
                    <div class="col-xs-12">
                        <textarea name="comment" id="task-comment" class="summernote" placeholder="@lang('modules.tasks.comment')"></textarea>
                    </div>
                    <div class="col-xs-12">
                        <a href="javascript:;" id="submit-comment" class="btn btn-info btn-sm"><i class="fa fa-send"></i> @lang('app.submit')</a>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-xs-12" id="task-history-section">
        </div>

    </div>

</div>


<script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/moment/moment.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('plugins/bower_components/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/peity/jquery.peity.init.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script>
     var dem=('{{ count($task->subtasks) }}');
     var demSubmit=('{{ count($task->subtasks) }}');

    $('body').on('click', '.edit-sub-task', function () {
        var id = $(this).data('sub-task-id');
        var url = '{{ route('member.sub-task.edit', ':id')}}';
        url = url.replace(':id', id);

        $('#subTaskModelHeading').html('Sub Task');
        $.ajaxModal('#subTaskModal', url);
    });

    $('body').on('click', '.add-sub-task', function () {
        //console.log('add-sub-task');
        var id = $(this).data('task-id');
        var url = '{{ route('member.sub-task.create')}}?task_id='+id;

        $('#subTaskModelHeading').html('Sub Task');
        $.ajaxModal('#subTaskModal', url);
    });


    $('#reminderButton').click(function () {
        swal({
            title: "Are you sure?",
            text: "Do you want to send reminder to assigned employee?",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "No, cancel please!",
                confirm: {
                    text: "Yes, send it!",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {

                var url = '{{ route('member.all-tasks.reminder', $task->id)}}';

                $.easyAjax({
                    type: 'GET',
                    url: url,
                    success: function (response) {
                        //
                    }
                });
            }
        });
    })

    function saveSubTask() {
        $.easyAjax({
            url: '{{route('member.sub-task.store')}}',
            container: '#createSubTask',
            type: "POST",
            data: $('#createSubTask').serialize(),
            success: function (response) {
                $('#subTaskModal').modal('hide');
                $('#sub-task-list').html(response.view);
                
            }
        });
        if(demSubmit!=dem)
        {
            demSubmit=demSubmit+1;
            $('#count_sub_task').html("@lang('modules.tasks.subTask')("+demSubmit+")");
        }
        else
        {
            dem=(parseInt(dem));
            demSubmit=dem+1;
            $('#count_sub_task').html("@lang('modules.tasks.subTask')("+demSubmit+")");
            dem=demSubmit;
        }
    }

    function updateSubTask(id) {
        var url = '{{ route('member.sub-task.update', ':id')}}';
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            container: '#createSubTask',
            type: "POST",
            data: $('#createSubTask').serialize(),
            success: function (response) {
                $('#subTaskModal').modal('hide');
                $('#sub-task-list').html(response.view)
            }
        })
    }

    $('#view-task-history').click(function () {
        var id = $(this).data('task-id');

        var url = '{{ route('member.all-tasks.history', ':id')}}';
        url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            type: "GET",
            success: function (response) {
                $('#task-detail-section').hide();
                $('#task-history-section').html(response.view);
                $('#view-task-history').hide();
                $('.close-task-history').show();
            }
        })

    })

    $('.close-task-history').click(function () {
        $('#task-detail-section').show();
        $('#task-history-section').html('');
        $(this).hide();
        $('#view-task-history').show();

    })

    $('.summernote').summernote({
        height: 100,                 // set editor height
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


    $('body').on('click', '.delete-sub-task', function () {
        var id = $(this).data('sub-task-id');
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted sub task!",
            dangerMode: true,
            icon: 'warning',
            buttons: {
                cancel: "No, cancel please!",
                confirm: {
                    text: "Yes, delete it!",
                    value: true,
                    visible: true,
                    className: "danger",
                }
            }
        }).then(function (isConfirm) {
            if (isConfirm) {

                var url = "{{ route('member.sub-task.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $('#sub-task-list').html(response.view);
                        }
                    }
                });
                if(demSubmit!=dem)
                {
                    demSubmit=demSubmit-1;
                    $('#count_sub_task').html("@lang('modules.tasks.subTask')("+demSubmit+")");
                }
                else
                {
                    dem=(parseInt(dem));
                    demSubmit=dem-1;
                    $('#count_sub_task').html("@lang('modules.tasks.subTask')("+demSubmit+")");
                    dem=demSubmit;
                }
            };
            
        });
       
    });

    //    change sub task status
    $('#sub-task-list').on('click', '.task-check', function () {
        if ($(this).is(':checked')) {
            var status = 'complete';
        }else{
            var status = 'incomplete';
        }

        var id = $(this).data('sub-task-id');
        var url = "{{route('member.sub-task.changeStatus')}}";
        var token = "{{ csrf_token() }}";

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, subTaskId: id, status: status},
            success: function (response) {
                if (response.status == "success") {
                    $('#sub-task-list').html(response.view);
                }
            }
        })
    });

    $('#uploadedFiles').click(function () {

        var url = '{{ route("member.all-tasks.show-files", ':id') }}';

        var id = {{ $task->id }};
        url = url.replace(':id', id);

        $('#subTaskModelHeading').html('Sub Task');
        $.ajaxModal('#subTaskModal', url);
    });

    $('#submit-comment').click(function () {
        var comment = $('#task-comment').val();
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: '{{ route("member.task-comment.store") }}',
            type: "POST",
            data: {'_token': token, comment: comment, taskId: '{{ $task->id }}'},
            success: function (response) {
                if (response.status == "success") {
                    $('#comment-list').html(response.view);
                    $('.summernote').summernote("reset");
                }
            }
        })
    });


    function deleteComment(id) {

        var commentId = id;
        var token = '{{ csrf_token() }}';

        var url = '{{ route("member.task-comment.destroy", ':id') }}';
        url = url.replace(':id', commentId);

        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': token, '_method': 'DELETE', commentId: commentId},
            success: function (response) {
                if (response.status == "success") {
                    $('#comment-list').html(response.view);
                }
            }
        })
    }

    //    change task status
    function markComplete(status) {

        var id = {{ $task->id }};

        if(status == 'completed'){
            var checkUrl = '{{route('member.tasks.checkTask', ':id')}}';
            checkUrl = checkUrl.replace(':id', id);
            $.easyAjax({
                url: checkUrl,
                type: "GET",
                container: '#task-list-panel',
                data: {},
                success: function (data) {
                    if(data.taskCount > 0){
                        swal({
                            title: "Are you sure?",
                            text: "There is a incomplete sub-task in this task do you want to mark complete!",
                            dangerMode: true,
                            icon: 'warning',
                            buttons: {
                                cancel: "No, cancel please!",
                                confirm: {
                                    text: "Yes, complete it!",
                                    value: true,
                                    visible: true,
                                    className: "danger",
                                }
                            }
                        }).then(function (isConfirm) {
                            if (isConfirm) {
                                updateTask(id,status)
                            }
                        });
                    }
                    else{
                        updateTask(id,status)
                    }

                }
            });
        }
        else{
            updateTask(id,status)
        }


    }

    // Update Task
    function updateTask(id,status){
        var url = "{{route('member.tasks.changeStatus')}}";
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            url: url,
            type: "POST",
            container: '.r-panel-body',
            data: {'_token': token, taskId: id, status: status, sortBy: 'id'},
            async: false,
            success: function (data) {
                $('#columnStatus').css('color', data.textColor);
                $('#columnStatus').html(data.column);
                if(status == 'completed'){

                    $('#inCompletedButton').removeClass('hidden');
                    $('#completedButton').addClass('hidden');
                    if($('#reminderButton').length){
                        $('#reminderButton').addClass('hidden');
                    }
                }
                else{
                    $('#completedButton').removeClass('hidden');
                    $('#inCompletedButton').addClass('hidden');
                    if($('#reminderButton').length){
                        $('#reminderButton').removeClass('hidden');
                    }
                }

                if( typeof table !== 'undefined'){
                    table._fnDraw();
                }
                else{
                    if ($.fn.showTable) {
                        showTable();
                    }
                }
                refreshTask(id);
            }
        })
    }

    $('#start-task-timer').click(function () {
        var task_id = "{{ $task->id }}";
        var project_id = "{{ $task->project_id }}";
        var user_id = "{{ user()->id }}";
        var memo = "{{ $task->heading }}";
        var token = "{{ csrf_token() }}";

        $.easyAjax({
            url: '{{route('member.time-log.store')}}',
            container: '#startTimer',
            type: "POST",
            data: {task_id: task_id, project_id: project_id, memo: memo, '_token': token, user_id: user_id},
            success: function (data) {
                refreshTask(task_id);
            }
        })
    });

    $('#stop-task-timer').click(function () {
        var id = $(this).data('time-id');
        var url = '{{route('member.all-time-logs.stopTimer', ':id')}}';
        url = url.replace(':id', id);
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            url: url,
            type: "POST",
            data: {timeId: id, _token: token},
            success: function (data) {
                refreshTask("{{ $task->id }}");
            }
        })
    });

    function refreshTask(taskId) {
        var id = taskId;
        var url = "{{ route('member.all-tasks.show',':id') }}";
        url = url.replace(':id', id);

        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response.status == "success") {
                    $('#right-sidebar-content').html(response.view);
                }
            }
        });
    }

        setTimeout(function(){
          $('[data-toggle="tooltip"]').tooltip();  
        }, 3000)
        
</script>

@if ($task->board_column->slug != 'completed' && !is_null($task->activeTimer))
    <script>

        $(document).ready(function(e) {
            var $worked = $("#active-task-timer");
            function updateTimer() {
                var myTime = $worked.html();
                var ss = myTime.split(":");

                var hours = ss[0];
                var mins = ss[1];
                var secs = ss[2];
                secs = parseInt(secs)+1;

                if(secs > 59){
                    secs = '00';
                    mins = parseInt(mins)+1;
                }

                if(mins > 59){
                    secs = '00';
                    mins = '00';
                    hours = parseInt(hours)+1;
                }

                if(hours.toString().length < 2) {
                    hours = '0'+hours;
                }
                if(mins.toString().length < 2) {
                    mins = '0'+mins;
                }
                if(secs.toString().length < 2) {
                    secs = '0'+secs;
                }
                var ts = hours+':'+mins+':'+secs;

                $worked.html(ts);
                setTimeout(updateTimer, 1000);
            }
            setTimeout(updateTimer, 1000);
        });

    </script>
@endif

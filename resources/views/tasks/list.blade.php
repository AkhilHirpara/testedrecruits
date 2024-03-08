<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <div class="container mt-5">
            
            @if(session('status') == 'added')
            <ul>
                <li>Added Successfully</li>
            </ul>
            @endif
            @if(session('status') == 'updated')
            <ul>
                <li>Updated Successfully</li>
            </ul>
            @endif
            @if(session('status') == 'deleted')
            <ul>
                <li>Deleted Successfully</li>
            </ul>
            @endif

            <a href="{{ URL::to('/tasks/create') }}" class="btn btn-dark">Add</a>
            <select class="form-control form-control-solid mt-2" name="project_id" id="project_id">
                <option value="">Select Project</option>
                @foreach($page_data['project_list'] as $value)
                    <option {{ (isset($page_data['selected_id']) && $page_data['selected_id'] == $value->id) ? 'selected' : '' }} value="{{$value->id}}">{{$value->project_name}}</option>
                @endforeach
            </select>
            <hr>
            <table id="table" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Task Id</th>
                        <th>Task name</th>
                        <th>Project</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBodyContents">
                @if(count($page_data['task_details']) > 0)
                    @foreach($page_data['task_details'] as $key => $value)
                        <tr class="tableRow" data-id="{{ $value->id }}">
                            <td>{{ $value->id }}</td> 
                            <td>{{ $value->task_name }}</td>
                            <td>{{ $value->project->project_name }}</td>
                            <td>{{ $value->description }}</td>
                            <td>{{ $value->priority }}</td>
                            <td>{{ ($value->created_at) ? $value->created_at->format('d-m-Y H:i:s') : '-'  }}</td>
                            <td>{{ ($value->created_at) ? $value->updated_at->format('d-m-Y H:i:s') : '-'  }}</td>
                            <td class="d-flex">
                                <a href="{{ URL::to('tasks/' . $value->id . '/edit') }}"><button class="btn btn-dark" type="button">Edit</button></a>&nbsp;
                                <form action="{{ URL::to('tasks/'.  $value->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table> 
        </div>

        <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>
        <script type="text/javascript">
            $(function () {
                $(document).ready(function() {
                    $("#table").DataTable({
                        order: [[3, 'asc']]
                    });

                    $('#project_id').on('change', function() {
                        if(this.value != '')
                            window.location.href = '/tasks/'+this.value+'/find';
                        else
                            window.location.href = '/tasks';
                    });
                });
                $("#tableBodyContents").sortable({
                    items: "tr",
                    cursor: 'move',
                    opacity: 0.6,
                    update: function() {
                        sendOrderToServer();
                    }
                });

                function sendOrderToServer() {
                    var order = [];
                    var token = $('meta[name="csrf-token"]').attr('content');
                    $('tr.tableRow').each(function(index,element) {
                        order.push({
                            id: $(this).attr('data-id'),
                            position: index+1
                        });
                    });
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ url('task-reorder') }}",
                            data: {
                            order: order,
                            _token: token
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                location.reload();
                            } else {
                                alert('Something went wrong');
                            }
                        }
                    });
                }
            });
        </script>
    </body>
</html>
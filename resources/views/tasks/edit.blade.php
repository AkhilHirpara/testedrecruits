<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <div class="container mt-5">
            @if($errors->any())
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <a class="btn btn-dark" href="{{ URL::to('/tasks') }}">Back</a>
            @php
                $taskDetails = $page_data['task_details'][0];
            @endphp
            <form method="POST" action="{{ route('tasks.update', ['task' => $taskDetails['id']]) }}">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <label for="task_name" class="form-label">Task Name</label>
                    <input type="text" class="form-control" name="task_name" value="{{ $taskDetails['task_name'] }}" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" name="description" required>{{ $taskDetails['description'] }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <select class="form-control form-control-solid" name="project_id" required>
                        <option value="">Select Project</option>
                        @foreach($page_data['project_list'] as $value)
                            <option {{$taskDetails['project_id'] }} {{ ($taskDetails['project_id'] == $value->id ) ? 'selected' : '' }} value="{{$value->id}}">{{$value->project_name}}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary btn-sm me-3 text-center">Submit</button>
            </form>
        </div>    
    </body>
</html>
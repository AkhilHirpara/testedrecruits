<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tasks;
use App\Models\Projects;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['project_list'] = Projects::select('id', 'project_name')->get();
        $data['task_details'] = Tasks::with('project')->orderBy('priority', 'ASC')->get();
        return view('tasks.list')->with('page_data', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['project_list'] = Projects::select('id', 'project_name')->get();
        return view('tasks.create')->with('page_data', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate
        $rules = array(
            'task_name'  => 'required',
            'description'  => 'required',
            'project_id'  => 'required',
        );
        $messages = array(
            'task_name.required'    => 'The Task name field is required.',
            'description.required'     => 'The Description field is required.',
            'project_id.required'     => 'The Project field is required.',
        );
        $validator = Validator::make($request->all(), $rules, $messages);

        // process the request
        if ($validator->fails()) {
            return Redirect::to('tasks/create')
                ->withErrors($validator);
        } else {
            // store data
            $class = new Tasks();
            $class->task_name  = $request->get('task_name');
            $maxPriority = Tasks::max('priority');
            $class->priority  = $maxPriority + 1;
            $class->description    = $request->get('description');
            $class->project_id    = $request->get('project_id');
            $class->save();

            // redirect
            return Redirect::to('tasks')->with('status','added');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {   
        $data['project_list'] = Projects::select('id', 'project_name')->get();
        $data['task_details'] = Tasks::select('id', 'task_name', 'description','project_id')
        ->where('id',$id)
        ->get();
        return view('tasks.edit')->with('page_data', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = array(
            'task_name'  => 'required',
            'description'  => 'required',
            'project_id'  => 'required',
        );
        $messages = array(
            'task_name.required'    => 'The Task name field is required.',
            'description.required'     => 'The Priority field is required.',
            'project_id.required'     => 'The Project field is required.',
        );
        $validator = Validator::make($request->all(), $rules, $messages);

        // process the request
        if ($validator->fails()) {
            return Redirect::to('tasks/'.$id.'/edit')
                ->withErrors($validator);
        } else {
            $task = Tasks::find($id);
            // store data
            $task->task_name   = $request->get('task_name');
            $task->description   = $request->get('description');
            $task->project_id   = $request->get('project_id');
            $task->save();

            // redirect
            return Redirect::to('tasks')->with('status','updated');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Tasks::find($id);
        $task->delete();

        // redirect
        return Redirect::to('tasks')->with('status', 'deleted');
    }

    /**
     * Reorder the priorities of tasks
     */
    public function reorder(Request $request)
    {
        $tasks = Tasks::all();
        foreach ($tasks as $task) {
            foreach ($request->order as $order) {
                if ($order['id'] == $task->id) {
                    $task->update(['priority' => $order['position']]);
                }
            }
        }
        return response(['status' => 'success','message' => 'Update Successfully'], 200);
    }

    /**
     * Find the tasks associated with project
     */
    public function find(string $id)
    {   
        $data['project_list'] = Projects::select('id', 'project_name')->get();
        $data['task_details'] = Tasks::with('project')->orderBy('priority', 'ASC')
        ->where('project_id',$id)
        ->get();
        $data['selected_id'] = $id;
        return view('tasks.list')->with('page_data', $data);
    }
}

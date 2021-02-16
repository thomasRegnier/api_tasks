<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = User::find(Auth::user()->id);

        $tasks = $user->tasks()->orderBy('updated_at', 'desc')
            ->when(request('completed'), function ($query) {
                $query->where('completed', 1);
            })
            ->get();

        return response([
            'tasks' => $tasks
        ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validation = Validator::make($request->all(), [
            'body' => ['required', 'string'],
        ]);


        if ($validation->fails()) {
            $message = $validation->messages()->toArray();
            return response()
                ->json(['error' => $message], 422);
        }

        $user = User::find(Auth::user()->id);
        $tsk = new Task(
            [
                'body' => $request['body'],
                'user_id' => Auth::user()->id,
                'completed' => 0
            ]
        );
        $task =   $user->tasks()->save($tsk);

        return response()
            ->json([$task], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Task $task)
    {
        //
        $task = Task::find($request['id']);

        if(!$task){
            return response()
            ->json(["error"], 404);
        }

        if ($task->user_id != Auth::user()->id) {
            return response()
                ->json(['permission denied'], 403);
        }

        return response()
            ->json([$task], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task, $id)
    {

        $task = Task::findOrFail($request['id']);

        if ($task->user_id != Auth::user()->id) {
            return response()
                ->json(['permission denied'], 403);
        }

        $validation = Validator::make($request->all(), [
            'body' => ['required', 'string'],
            'completed' => ['required', 'integer']
        ]);

        if ($validation->fails()) {
            $message = $validation->messages()->toArray();
            return response()
                ->json(['error' => $message], 422);
        }

        $task->update([
            'body' => $request->body,
            'completed' => $request->completed,
        ]);


        return response()
            ->json(['error' => $task], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Task $task)
    {
        //

        $task = Task::find($request['id']);

        if(!$task){
            return response()
            ->json(["error"], 404);
        }

        if ($task->user_id != Auth::user()->id) {
            return response()
                ->json(['permission denied'], 403);
        }

        $task->delete();

        return response()
            ->json([], 200);
    }
}

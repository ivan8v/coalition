<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskStoreRequest;
use App\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function index(Request $request)
    {
        $tasks = Task::when(
            ($request->has('projectFilter')
                && ($request->get('projectFilter') != '')),
            function ($query) use ($request) {
                $query->whereProjectId($request->get('projectFilter'));
            }
        )->get();

        return response()->json($tasks);
    }

    public function store(TaskStoreRequest $request)
    {
        $priority = $request->get('priority');
        $name = $request->get('name');
        $projectId = $request->get('projectId');

        $newTask = Task::create(
            [
                'name'       => $name,
                'priority'   => $priority,
                'project_id' => $projectId,
            ]
        );

        return ($newTask)
            ? response()->json($newTask)
            : response()->json(
                'error saving task',
                501
            );
    }

    public function update(Request $request, $id) {
        $task = Task::find($id);
        $result = $task->update($request->all());

        return response()->json($result);
    }

    public function destroy($id)
    {
        $result = Task::destroy($id);

        return ($result)
            ? response()->json($id)
            : response()->json(
                'error deleting task',
                501
            );
    }

}

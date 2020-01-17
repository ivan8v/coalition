<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Project;
use App\Task;

class ProjectController extends Controller
{

    public function index()
    {
        $projects = Project::all();

        return response()->json(ProjectResource::collection($projects));
    }

    public function withTasks()
    {
        $projects = Project::with('tasks')->get();

        return response()->json(ProjectResource::collection($projects));
    }

}

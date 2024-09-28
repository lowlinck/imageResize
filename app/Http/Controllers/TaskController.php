<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function getStatuses(Request $request)
    {
        $taskIds = $request->query('ids');

        if (!$taskIds) {
            return response()->json(['error' => 'No task IDs provided'], 400);
        }

        $tasks = Task::whereIn('id', explode(',', $taskIds))->get();

        return response()->json(['tasks' => $tasks], 200);
    }
}


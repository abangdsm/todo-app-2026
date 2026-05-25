<?php

namespace App\Http\Controllers;

use App\Models\SubTask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubTaskController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'title' => 'required|string|max:255',
        ]);

        $task = Task::findOrFail($request->task_id);
        $user = $request->user();

        if ($task->user_id !== $user->id) {
            abort(403);
        }

        $subTask = $task->subTasks()->create([
            'user_id' => $user->id,
            'title' => $request->title,
            'is_completed' => false,
        ]);

        return response()->json($subTask, 201);
    }

    public function toggleComplete(Request $request, SubTask $subTask)
    {
        $user = $request->user();

        if ($subTask->task->user_id !== $user->id) {
            abort(403);
        }

        $subTask->is_completed = !$subTask->is_completed;
        $subTask->save();

        if ($subTask->is_completed) {
            $user->updateStreak();
        }

        return response()->json(['is_completed' => $subTask->is_completed]);
    }

    public function destroy(Request $request, SubTask $subTask)
    {
        if ($subTask->task->user_id !== $request->user()->id) {
            abort(403);
        }

        $subTask->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

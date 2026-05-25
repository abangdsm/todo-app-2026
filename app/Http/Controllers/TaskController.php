<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\SubTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index(Request $request)  // ← Terima $request
    {
        $user = $request->user();  // ← Ambil user dari request

        $tasks = $user->tasks()
            ->with('subTasks')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('order')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'priority_badge' => $task->priority_badge,
                    'due_date' => $task->due_date?->format('Y-m-d'),
                    'is_completed' => $task->is_completed,
                    'order' => $task->order,
                    'sub_tasks' => $task->subTasks->map(function ($sub) {
                        return [
                            'id' => $sub->id,
                            'title' => $sub->title,
                            'is_completed' => $sub->is_completed,
                        ];
                    }),
                ];
            });

        // Cek request JSON dengan cara yang aman
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json($tasks);
        }

        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $user = $request->user();
        $maxOrder = $user->tasks()->max('order') ?? 0;

        $task = $user->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'order' => $maxOrder + 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json($task->load('subTasks'), 201);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created!');
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeUser($request, $task);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

        if ($request->expectsJson()) {
            return response()->json($task->load('subTasks'));
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated!');
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorizeUser($request, $task);
        $task->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Deleted']);
        }

        return redirect()->route('tasks.index')->with('success', 'Task deleted!');
    }

    public function toggleComplete(Request $request, Task $task)
    {
        $this->authorizeUser($request, $task);

        $task->is_completed = !$task->is_completed;
        $task->save();

        if ($task->is_completed) {
            $request->user()->updateStreak();  // ← Pakai request->user()
        }

        return response()->json(['is_completed' => $task->is_completed]);
    }

    public function updateOrder(Request $request)
    {
        $order = $request->order;

        foreach ($order as $index => $taskId) {
            Task::where('id', $taskId)
                ->where('user_id', $request->user()->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Order updated']);
    }

    public function updateDeadline(Request $request, Task $task)
    {
        $this->authorizeUser($request, $task);

        $request->validate([
            'due_date' => 'nullable|date',
        ]);

        $task->due_date = $request->due_date;
        $task->save();

        return response()->json(['due_date' => $task->due_date]);
    }

    public function calendarData(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $startDate = "{$request->year}-{$request->month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $tasks = $request->user()->tasks()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->with('subTasks')
            ->get()
            ->groupBy(function ($task) {
                return $task->due_date->format('Y-m-d');
            })
            ->map(function ($tasksOnDate) {
                return $tasksOnDate->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'priority' => $task->priority,
                        'priority_badge' => $task->priority_badge,
                        'is_completed' => $task->is_completed,
                        'sub_tasks_count' => $task->subTasks->count(),
                        'sub_tasks_completed' => $task->subTasks->where('is_completed', true)->count(),
                    ];
                });
            });

        return response()->json($tasks);
    }

    public function exportJson(Request $request)
    {
        $user = $request->user();
        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'tasks' => $user->tasks()->with('subTasks')->get()->map(function ($task) {
                return [
                    'title' => $task->title,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date,
                    'is_completed' => $task->is_completed,
                    'sub_tasks' => $task->subTasks->map(function ($sub) {
                        return [
                            'title' => $sub->title,
                            'is_completed' => $sub->is_completed,
                        ];
                    }),
                ];
            }),
            'exported_at' => now()->toDateTimeString(),
        ];

        $filename = 'tasks_backup_' . now()->format('Y-m-d_His') . '.json';
        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function importJson(Request $request)
    {
        $request->validate([
            'json_file' => 'required|file|mimes:json|max:5120',
        ]);

        $content = file_get_contents($request->file('json_file')->path());
        $data = json_decode($content, true);

        if (!isset($data['tasks']) || !is_array($data['tasks'])) {
            return back()->with('error', 'Invalid JSON format');
        }

        DB::transaction(function () use ($data, $request) {
            $user = $request->user();
            $user->tasks()->delete();

            foreach ($data['tasks'] as $taskData) {
                $task = $user->tasks()->create([
                    'title' => $taskData['title'],
                    'description' => $taskData['description'] ?? null,
                    'priority' => $taskData['priority'] ?? 'medium',
                    'due_date' => $taskData['due_date'] ?? null,
                    'is_completed' => $taskData['is_completed'] ?? false,
                    'order' => 0,
                ]);

                if (isset($taskData['sub_tasks']) && is_array($taskData['sub_tasks'])) {
                    foreach ($taskData['sub_tasks'] as $subData) {
                        $task->subTasks()->create([
                            'user_id' => $user->id,
                            'title' => $subData['title'],
                            'is_completed' => $subData['is_completed'] ?? false,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('tasks.index')->with('success', 'Data imported successfully!');
    }

    private function authorizeUser(Request $request, $task)
    {
        if ($task->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
    }
}

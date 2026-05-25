<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📋 Task Manager
            </h2>
            <div class="flex gap-3">
                <button id="darkModeToggle" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded-lg">
                    🌙 Dark
                </button>
                <button id="exportJsonBtn" class="px-3 py-1 bg-blue-500 text-white rounded-lg">
                    📥 Export JSON
                </button>
                <label class="px-3 py-1 bg-green-500 text-white rounded-lg cursor-pointer">
                    📤 Import JSON
                    <input type="file" id="importJsonInput" accept=".json" class="hidden">
                </label>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Daily Quote -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div x-data="{ quote: { text: 'Loading...', author: '' } }" x-init="fetch('https://quotes.liupurnomo.com/api/quotes/random')
                        .then(res => res.json())
                        .then(data => quote = data.data)">
                        <p class="text-lg italic">"<span x-text="quote.text"></span>"</p>
                        <p class="text-right mt-2">— <span x-text="quote.author"></span></p>
                    </div>
                </div>
            </div>

            <!-- Streak Counter -->
            <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-lg p-4 mb-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm">🔥 Current Streak</p>
                        <p class="text-3xl font-bold">{{ auth()->user()->current_streak }} days</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm">🏆 Best Streak</p>
                        <p class="text-3xl font-bold">{{ auth()->user()->best_streak }} days</p>
                    </div>
                </div>
            </div>

            <!-- Create Task Button -->
            <div class="mb-6">
                <button @click="openTaskModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    + New Task
                </button>
            </div>

            <!-- Calendar View -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div id="calendar"></div>
                </div>
            </div>

            <!-- Tasks List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">📝 My Tasks</h3>
                    <div id="tasks-list" x-data="taskManager()" x-init="initTasks()">
                        <div id="task-container">
                            <template x-for="task in tasks" :key="task.id">
                                <div class="task-item border-b border-gray-200 dark:border-gray-700 py-3"
                                    :data-id="task.id">
                                    <div class="flex items-start gap-3">
                                        <div class="drag-handle cursor-move text-gray-400">⋮⋮</div>
                                        <input type="checkbox" :checked="task.is_completed"
                                            @change="toggleTask(task.id)" class="mt-1">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span :class="task.priority_badge" class="px-2 py-1 rounded text-xs">
                                                    <span x-text="task.priority"></span>
                                                </span>
                                                <span class="font-medium"
                                                    :class="{ 'line-through text-gray-400': task.is_completed }"
                                                    x-text="task.title"></span>
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1" x-text="task.description"></div>
                                            <div class="text-xs text-gray-400 mt-1" x-show="task.due_date">
                                                📅 <span x-text="task.due_date"></span>
                                            </div>

                                            <!-- Sub Tasks -->
                                            <div class="ml-6 mt-2">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <input type="text" placeholder="Add subtask..."
                                                        id="subtask-input-${task.id}"
                                                        class="text-sm border rounded px-2 py-1 dark:bg-gray-700">
                                                    <button @click="addSubTask(task.id)"
                                                        class="text-xs bg-gray-500 text-white px-2 py-1 rounded">+</button>
                                                </div>
                                                <div class="space-y-1">
                                                    <template x-for="sub in task.sub_tasks" :key="sub.id">
                                                        <div class="flex items-center gap-2">
                                                            <input type="checkbox" :checked="sub.is_completed"
                                                                @change="toggleSubTask(sub.id)" class="w-3 h-3">
                                                            <span class="text-sm"
                                                                :class="{ 'line-through text-gray-400': sub.is_completed }"
                                                                x-text="sub.title"></span>
                                                            <button @click="deleteSubTask(sub.id)"
                                                                class="text-red-500 text-xs ml-2">✕</button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button @click="editTask(task)" class="text-blue-500">✏️</button>
                                            <button @click="deleteTask(task.id)" class="text-red-500">🗑️</button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold mb-4" id="modalTitle">New Task</h3>
            <input type="hidden" id="taskId">
            <input type="text" id="taskTitle" placeholder="Task title"
                class="w-full border rounded px-3 py-2 mb-3 dark:bg-gray-700">
            <textarea id="taskDesc" placeholder="Description" class="w-full border rounded px-3 py-2 mb-3 dark:bg-gray-700"
                rows="3"></textarea>
            <select id="taskPriority" class="w-full border rounded px-3 py-2 mb-3 dark:bg-gray-700">
                <option value="low">🟢 Low</option>
                <option value="medium">🟡 Medium</option>
                <option value="high">🔴 High</option>
            </select>
            <input type="date" id="taskDueDate" class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700">
            <div class="flex justify-end gap-2">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                <button onclick="saveTask()" class="px-4 py-2 bg-blue-500 text-white rounded">Save</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function taskManager() {
                return {
                    tasks: [],
                    initTasks() {
                        this.fetchTasks();

                        // SortableJS for drag & drop
                        const container = document.getElementById('task-container');
                        new Sortable(container, {
                            handle: '.drag-handle',
                            onEnd: (evt) => {
                                const order = Array.from(container.children).map(el => el.dataset.id);
                                this.updateOrder(order);
                            }
                        });
                    },
                    fetchTasks() {
                        fetch('/tasks')
                            .then(res => res.json())
                            .then(data => this.tasks = data);
                    },
                    toggleTask(id) {
                        fetch(`/tasks/${id}/toggle`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(() => this.fetchTasks());
                    },
                    updateOrder(order) {
                        fetch('/tasks/update-order', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                order
                            })
                        }).then(() => this.fetchTasks());
                    },
                    addSubTask(taskId) {
                        const input = document.getElementById(`subtask-input-${taskId}`);
                        const title = input.value;
                        if (!title) return;

                        fetch('/sub-tasks', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                task_id: taskId,
                                title
                            })
                        }).then(() => {
                            this.fetchTasks();
                            input.value = '';
                        });
                    },
                    toggleSubTask(id) {
                        fetch(`/sub-tasks/${id}/toggle`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(() => this.fetchTasks());
                    },
                    deleteSubTask(id) {
                        fetch(`/sub-tasks/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(() => this.fetchTasks());
                    },
                    deleteTask(id) {
                        if (confirm('Delete this task?')) {
                            fetch(`/tasks/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(() => this.fetchTasks());
                        }
                    },
                    editTask(task) {
                        document.getElementById('modalTitle').innerText = 'Edit Task';
                        document.getElementById('taskId').value = task.id;
                        document.getElementById('taskTitle').value = task.title;
                        document.getElementById('taskDesc').value = task.description || '';
                        document.getElementById('taskPriority').value = task.priority;
                        document.getElementById('taskDueDate').value = task.due_date || '';
                        document.getElementById('taskModal').classList.remove('hidden');
                        document.getElementById('taskModal').classList.add('flex');
                    }
                }
            }

            function openTaskModal() {
                document.getElementById('modalTitle').innerText = 'New Task';
                document.getElementById('taskId').value = '';
                document.getElementById('taskTitle').value = '';
                document.getElementById('taskDesc').value = '';
                document.getElementById('taskPriority').value = 'medium';
                document.getElementById('taskDueDate').value = '';
                document.getElementById('taskModal').classList.remove('hidden');
                document.getElementById('taskModal').classList.add('flex');
            }

            function closeModal() {
                document.getElementById('taskModal').classList.add('hidden');
                document.getElementById('taskModal').classList.remove('flex');
            }

            function saveTask() {
                const id = document.getElementById('taskId').value;
                const data = {
                    title: document.getElementById('taskTitle').value,
                    description: document.getElementById('taskDesc').value,
                    priority: document.getElementById('taskPriority').value,
                    due_date: document.getElementById('taskDueDate').value,
                    _token: '{{ csrf_token() }}'
                };

                const url = id ? `/tasks/${id}` : '/tasks';
                const method = id ? 'PUT' : 'POST';

                fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(() => {
                        closeModal();
                        document.getElementById('taskManager').__x.$data.fetchTasks();
                    });
            }

            // Dark Mode
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
            darkModeToggle.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('darkMode', isDark);
            });

            // Export/Import
            document.getElementById('exportJsonBtn').addEventListener('click', () => window.location.href = '/export/json');
            document.getElementById('importJsonInput').addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('json_file', file);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('/import/json', {
                    method: 'POST',
                    body: formData
                }).then(() => window.location.reload());
            });
        </script>
    @endpush
</x-app-layout>

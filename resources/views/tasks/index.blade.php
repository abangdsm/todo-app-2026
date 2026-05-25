<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex gap-3">
                <a href="{{ route('calendar.view') }}"
                    class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl hover:scale-105 transition-all duration-300 shadow-lg text-white">
                    📅 Calendar
                </a>
                <button onclick="exportJson()"
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl hover:scale-105 transition-all duration-300 shadow-lg text-white">
                    📥 Export
                </button>
                <label
                    class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl hover:scale-105 transition-all duration-300 shadow-lg cursor-pointer text-white">
                    📤 Import
                    <input type="file" id="importJsonInput" accept=".json" class="hidden">
                </label>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Streak Card -->
                <div
                    class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-white shadow-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Current Streak</p>
                            <p class="text-4xl font-bold mt-2">{{ auth()->user()->current_streak ?? 0 }} <span
                                    class="text-lg">days</span></p>
                        </div>
                        <div class="text-5xl">🔥</div>
                    </div>
                </div>

                <!-- Best Streak Card -->
                <div
                    class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-6 text-white shadow-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Best Streak</p>
                            <p class="text-4xl font-bold mt-2">{{ auth()->user()->best_streak ?? 0 }} <span
                                    class="text-lg">days</span></p>
                        </div>
                        <div class="text-5xl">🏆</div>
                    </div>
                </div>

                <!-- Total Tasks Card -->
                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl p-6 text-white shadow-2xl transform hover:scale-105 transition-all duration-300"
                    id="totalTasksCard">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Tasks</p>
                            <p class="text-4xl font-bold mt-2" id="totalTasksCount">0</p>
                        </div>
                        <div class="text-5xl">📋</div>
                    </div>
                </div>
            </div>

            <!-- Daily Quote -->
            <div
                class="backdrop-blur-xl bg-white/30 dark:bg-gray-800/30 rounded-2xl p-6 mb-8 shadow-xl border border-white/20">
                <div class="flex items-start gap-4">
                    <div class="text-4xl">💭</div>
                    <div class="flex-1" id="quoteContainer">
                        <div class="animate-pulse flex space-x-4">
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-white/20 rounded w-3/4"></div>
                                <div class="h-3 bg-white/20 rounded w-1/4"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Task Button -->
            <div class="mb-8 text-center">
                <button onclick="openTaskModal()"
                    class="group relative px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl text-white font-bold text-lg hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl">
                    <span class="relative z-10">✨ Create New Task</span>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-lg opacity-50 group-hover:opacity-75 transition-all duration-300">
                    </div>
                </button>
            </div>

            <!-- Tasks List -->
            <div
                class="backdrop-blur-xl bg-white/30 dark:bg-gray-800/30 rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center gap-2">
                        <span>📝</span> My Tasks
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2"
                            id="tasksCountBadge"></span>
                    </h3>
                    <div id="tasks-container" class="space-y-3">
                        <!-- Tasks will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div id="taskModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl p-8 w-96 max-w-md transform transition-all duration-300 scale-100 shadow-2xl border border-white/20">
            <h3 class="text-2xl font-bold mb-4 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent"
                id="modalTitle">New Task</h3>
            <input type="hidden" id="taskId">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Task Title</label>
                    <input type="text" id="taskTitle" placeholder="Enter task title..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2 bg-white/50 dark:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Description</label>
                    <textarea id="taskDesc" placeholder="Add description..." rows="3"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2 bg-white/50 dark:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Priority</label>
                    <select id="taskPriority"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2 bg-white/50 dark:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="low">🟢 Low</option>
                        <option value="medium">🟡 Medium</option>
                        <option value="high">🔴 High</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Due Date</label>
                    <input type="date" id="taskDueDate"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2 bg-white/50 dark:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button onclick="closeModal()"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-xl transition-all duration-300">
                    Cancel
                </button>
                <button onclick="saveTask()" id="saveTaskBtn"
                    class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-xl transition-all duration-300 shadow-lg">
                    Save Task
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Task Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-96 max-w-md transform transition-all duration-300 scale-100 shadow-2xl border border-white/20">
            <div class="text-center">
                <div class="text-6xl mb-4">🗑️</div>
                <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-white">Delete Task?</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">This action cannot be undone. Are you sure?</p>
                <div class="flex gap-3 justify-center">
                    <button onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-xl transition-all duration-300">
                        Cancel
                    </button>
                    <button id="confirmDeleteBtn"
                        class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-lg">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Subtask Modal -->
    <div id="deleteSubtaskModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-96 max-w-md transform transition-all duration-300 scale-100 shadow-2xl border border-white/20">
            <div class="text-center">
                <div class="text-6xl mb-4">🗑️</div>
                <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-white">Delete Subtask?</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">This subtask will be permanently removed.</p>
                <div class="flex gap-3 justify-center">
                    <button onclick="closeDeleteSubtaskModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-xl transition-all duration-300">
                        Cancel
                    </button>
                    <button id="confirmDeleteSubtaskBtn"
                        class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-lg">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast"
        class="fixed bottom-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-xl shadow-2xl transform translate-x-full transition-all duration-300 z-50">
        <span id="toastMessage"></span>
    </div>
    @push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            toastMessage.innerText = message;
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
            toast.classList.remove('bg-green-600', 'bg-red-600');
            toast.classList.add(type === 'success' ? 'bg-green-600' : 'bg-red-600');
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                toast.classList.remove('translate-x-0');
            }, 3000);
        }

        async function loadQuote() {
            const quoteContainer = document.getElementById('quoteContainer');
            try {
                const response = await fetch('https://quotes.liupurnomo.com/api/quotes/random');
                const data = await response.json();
                if (data.status === 'SUCCESS') {
                    quoteContainer.innerHTML = `
                        <p class="text-lg italic text-gray-800 dark:text-gray-200">"${escapeHtml(data.data.text)}"</p>
                        <p class="text-right mt-2 text-gray-600 dark:text-gray-400">— ${escapeHtml(data.data.author)}</p>
                        <p class="text-right text-xs text-gray-400 dark:text-gray-500">#${escapeHtml(data.data.category)}</p>
                    `;
                }
            } catch (error) {
                quoteContainer.innerHTML =
                    `<p class="text-gray-600">"Hidup itu seperti ngoding, kadang error kadang running."</p>`;
            }
        }

        let tasks = [];

        async function loadTasks() {
            try {
                const response = await fetch('/tasks', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                tasks = await response.json();
                renderTasks();
                updateStats();
            } catch (error) {
                console.error(error);
            }
        }

        function updateStats() {
            const total = tasks.length;
            const completed = tasks.filter(t => t.is_completed).length;
            document.getElementById('totalTasksCount').innerText = total;
            document.getElementById('tasksCountBadge').innerHTML = `(${completed}/${total} completed)`;
        }

        function getPriorityBadge(priority) {
            const badges = {
                high: 'bg-gradient-to-r from-red-500 to-red-600 text-white',
                medium: 'bg-gradient-to-r from-yellow-500 to-orange-500 text-white',
                low: 'bg-gradient-to-r from-green-500 to-emerald-500 text-white'
            };
            return badges[priority] || badges.medium;
        }

        function getPriorityIcon(priority) {
            const icons = {
                high: '🔴',
                medium: '🟡',
                low: '🟢'
            };
            return icons[priority] || '🟡';
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function handleSubtaskKeyPress(event, taskId) {
            if (event.key === 'Enter') {
                event.preventDefault();
                addSubTask(taskId);
            }
        }

        function renderTasks() {
            const container = document.getElementById('tasks-container');
            if (tasks.length === 0) {
                container.innerHTML =
                    `<div class="text-center py-12"><div class="text-6xl mb-4">🎯</div><p>No tasks yet. Create one!</p></div>`;
                return;
            }
            container.innerHTML = tasks.map(task => `
                <div class="task-item bg-white/50 dark:bg-gray-700/50 rounded-xl p-4 border border-white/20" data-id="${task.id}" draggable="true">
                    <div class="flex items-start gap-3">
                        <div class="drag-handle cursor-move text-gray-400 mt-1">⋮⋮</div>
                        <input type="checkbox" ${task.is_completed ? 'checked' : ''} onchange="toggleTask(${task.id})" class="mt-1 w-5 h-5">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-xl text-xs font-semibold ${getPriorityBadge(task.priority)}">
                                    ${getPriorityIcon(task.priority)} ${task.priority.toUpperCase()}
                                </span>
                                <span class="font-semibold ${task.is_completed ? 'line-through' : ''}">${escapeHtml(task.title)}</span>
                            </div>
                            ${task.description ? `<div class="text-sm text-gray-600 mt-2">${escapeHtml(task.description)}</div>` : ''}
                            ${task.due_date ? `<div class="text-xs text-gray-500 mt-2">📅 ${task.due_date}</div>` : ''}
                            <div class="ml-6 mt-3">
                                <div class="flex gap-2 mb-2">
                                    <input type="text" id="subtask-input-${task.id}" placeholder="Add subtask..." onkeypress="handleSubtaskKeyPress(event, ${task.id})" class="flex-1 text-sm border rounded-lg px-3 py-1">
                                    <button onclick="addSubTask(${task.id})" class="px-3 py-1 bg-purple-500 text-white rounded-lg text-sm">+ Add</button>
                                </div>
                                <div class="space-y-1">
                                    ${task.sub_tasks.map(sub => `
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" ${sub.is_completed ? 'checked' : ''} onchange="toggleSubTask(${sub.id})">
                                                    <span class="${sub.is_completed ? 'line-through' : ''}">${escapeHtml(sub.title)}</span>
                                                </div>
                                                <button onclick="deleteSubTask(${sub.id})" class="px-2 py-1 bg-red-500/20 hover:bg-red-500 text-red-600 hover:text-white rounded-lg text-xs">🗑️ Hapus</button>
                                            </div>
                                        `).join('')}
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editTask(${task.id})" class="text-blue-500">✏️</button>
                            <button onclick="deleteTask(${task.id})" class="text-red-500">🗑️</button>
                        </div>
                    </div>
                </div>
            `).join('');
            attachDragEvents();
        }

        // Drag & Drop
        let draggedItem = null;

        function attachDragEvents() {
            document.querySelectorAll('.task-item').forEach(item => {
                item.setAttribute('draggable', 'true');
                item.ondragstart = (e) => {
                    draggedItem = item;
                    e.dataTransfer.setData('text/plain', item.dataset.id);
                    item.style.opacity = '0.5';
                };
                item.ondragend = () => {
                    if (draggedItem) draggedItem.style.opacity = '1';
                    draggedItem = null;
                };
            });
        }
        document.getElementById('tasks-container')?.addEventListener('dragover', (e) => e.preventDefault());
        document.getElementById('tasks-container')?.addEventListener('drop', async (e) => {
            e.preventDefault();
            const targetItem = e.target.closest('.task-item');
            if (draggedItem && targetItem && draggedItem !== targetItem) {
                const container = document.getElementById('tasks-container');
                const items = [...container.children];
                if (items.indexOf(draggedItem) < items.indexOf(targetItem)) {
                    targetItem.after(draggedItem);
                } else {
                    targetItem.before(draggedItem);
                }
                const order = [...container.children].map(el => el.dataset.id);
                await fetch('/tasks/update-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        order
                    })
                });
                await loadTasks();
            }
        });

        async function toggleTask(id) {
            await fetch(`/tasks/${id}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            await loadTasks();
            showToast('Task updated!');
        }
        async function toggleSubTask(id) {
            await fetch(`/sub-tasks/${id}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            await loadTasks();
        }
        async function addSubTask(taskId) {
            const input = document.getElementById(`subtask-input-${taskId}`);
            const title = input?.value.trim();
            if (!title) return;
            await fetch('/sub-tasks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    task_id: taskId,
                    title
                })
            });
            await loadTasks();
            if (input) input.value = '';
            showToast('Subtask added!');
        }

        let pendingDeleteId = null;

        function openDeleteModal(id) {
            pendingDeleteId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
        }
        document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
            if (pendingDeleteId) {
                await fetch(`/tasks/${pendingDeleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                await loadTasks();
                showToast('Task deleted!');
                closeDeleteModal();
            }
        });

        function deleteTask(id) {
            openDeleteModal(id);
        }

        let pendingSubtaskId = null;

        function openDeleteSubtaskModal(id) {
            pendingSubtaskId = id;
            document.getElementById('deleteSubtaskModal').classList.remove('hidden');
            document.getElementById('deleteSubtaskModal').classList.add('flex');
        }

        function closeDeleteSubtaskModal() {
            document.getElementById('deleteSubtaskModal').classList.add('hidden');
            document.getElementById('deleteSubtaskModal').classList.remove('flex');
        }
        document.getElementById('confirmDeleteSubtaskBtn')?.addEventListener('click', async () => {
            if (pendingSubtaskId) {
                await fetch(`/sub-tasks/${pendingSubtaskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                await loadTasks();
                showToast('Subtask deleted!');
                closeDeleteSubtaskModal();
            }
        });

        function deleteSubTask(id) {
            openDeleteSubtaskModal(id);
        }

        function openTaskModal() {
            document.getElementById('modalTitle').innerText = '✨ Create New Task';
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
        async function saveTask() {
            const id = document.getElementById('taskId').value;
            const data = {
                title: document.getElementById('taskTitle').value,
                description: document.getElementById('taskDesc').value,
                priority: document.getElementById('taskPriority').value,
                due_date: document.getElementById('taskDueDate').value
            };
            const url = id ? `/tasks/${id}` : '/tasks';
            const method = id ? 'PUT' : 'POST';
            await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });
            closeModal();
            await loadTasks();
            showToast(id ? 'Task updated!' : 'Task created!');
        }

        function editTask(id) {
            const task = tasks.find(t => t.id === id);
            if (!task) return;
            document.getElementById('modalTitle').innerText = '✏️ Edit Task';
            document.getElementById('taskId').value = task.id;
            document.getElementById('taskTitle').value = task.title;
            document.getElementById('taskDesc').value = task.description || '';
            document.getElementById('taskPriority').value = task.priority;
            document.getElementById('taskDueDate').value = task.due_date || '';
            document.getElementById('taskModal').classList.remove('hidden');
            document.getElementById('taskModal').classList.add('flex');
        }

        function exportJson() {
            window.location.href = '/export/json';
        }
        document.getElementById('importJsonInput')?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = async (ev) => {
                await fetch('/import/json', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: ev.target.result
                });
                window.location.reload();
            };
            reader.readAsText(file);
        });

        loadQuote();
        loadTasks();
    </script>
    @endpush
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                ✨ TaskFlow Pro
            </h2>
            <div class="flex gap-3">
                <button onclick="exportJson()"
                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl hover:scale-105 transition-all duration-300 shadow-lg">
                    📥 Export
                </button>
                <label
                    class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl hover:scale-105 transition-all duration-300 shadow-lg cursor-pointer">
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

            <!-- Daily Quote with Glassmorphism (API Kamu) -->
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

            <!-- Create Task Button with Animation -->
            <div class="mb-8 text-center">
                <button onclick="openTaskModal()"
                    class="group relative px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl text-white font-bold text-lg hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl">
                    <span class="relative z-10">✨ Create New Task</span>
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur-lg opacity-50 group-hover:opacity-75 transition-all duration-300">
                    </div>
                </button>
            </div>

            <!-- Tasks List with Glassmorphism -->
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

    <!-- Task Modal with Glassmorphism -->
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
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2 bg-white/50 dark:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Description</label>
                    <textarea id="taskDesc" placeholder="Add description..." rows="3"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2 bg-white/50 dark:bg-gray-700/50 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all"></textarea>
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

    <!-- Delete Confirmation Modal -->
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

    <!-- Toast Notification -->
    <div id="toast"
        class="fixed bottom-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-xl shadow-2xl transform translate-x-full transition-all duration-300 z-50">
        <span id="toastMessage"></span>
    </div>

    <script>
        // CSRF Token setup
        const csrfToken = '{{ csrf_token() }}';

        // Toast Notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            toastMessage.innerText = message;
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
            toast.classList.remove('bg-green-600', 'bg-red-600');
            if (type === 'success') {
                toast.classList.add('bg-green-600');
            } else {
                toast.classList.add('bg-red-600');
            }
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                toast.classList.remove('translate-x-0');
            }, 3000);
        }

        // Load Quote dari API Kamu
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
                } else {
                    throw new Error('API error');
                }
            } catch (error) {
                quoteContainer.innerHTML = `
                    <p class="text-gray-600 dark:text-gray-400">"Hidup itu seperti ngoding, kadang error kadang running."</p>
                    <p class="text-right mt-2 text-gray-500 dark:text-gray-500">— DeepSeeker</p>
                `;
            }
        }

        let tasks = [];

        async function loadTasks() {
            try {
                const response = await fetch('/tasks', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!response.ok) throw new Error('Network error');
                tasks = await response.json();
                renderTasks();
                updateStats();
            } catch (error) {
                console.error('Error loading tasks:', error);
                document.getElementById('tasks-container').innerHTML = `
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">⚠️</div>
                        <p class="text-red-500">Gagal memuat tugas. Refresh halaman!</p>
                        <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-xl">Refresh</button>
                    </div>
                `;
            }
        }

        function updateStats() {
            const totalTasks = tasks.length;
            const completedTasks = tasks.filter(t => t.is_completed).length;
            document.getElementById('totalTasksCount').innerText = totalTasks;
            document.getElementById('tasksCountBadge').innerHTML = `(${completedTasks}/${totalTasks} completed)`;
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
                container.innerHTML = `
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">🎯</div>
                        <p class="text-gray-500 dark:text-gray-400 text-lg">No tasks yet. Create your first task!</p>
                        <button onclick="openTaskModal()" class="mt-4 text-purple-600 dark:text-purple-400 hover:underline">+ Create one now</button>
                    </div>
                `;
                return;
            }

            container.innerHTML = tasks.map(task => `
                <div class="task-item bg-white/50 dark:bg-gray-700/50 rounded-xl p-4 border border-white/20 hover:shadow-lg transition-all duration-300" data-id="${task.id}" draggable="true">
                    <div class="flex items-start gap-3">
                        <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600 mt-1">⋮⋮</div>
                        <input type="checkbox" ${task.is_completed ? 'checked' : ''} onchange="toggleTask(${task.id})" class="mt-1 w-5 h-5 rounded">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-3 py-1 rounded-xl text-xs font-semibold ${getPriorityBadge(task.priority)}">
                                    ${getPriorityIcon(task.priority)} ${task.priority.toUpperCase()}
                                </span>
                                <span class="font-semibold text-lg ${task.is_completed ? 'line-through text-gray-400' : 'text-gray-800 dark:text-white'}">
                                    ${escapeHtml(task.title)}
                                </span>
                            </div>
                            ${task.description ? `<div class="text-sm text-gray-600 dark:text-gray-300 mt-2">${escapeHtml(task.description)}</div>` : ''}
                            ${task.due_date ? `<div class="text-xs text-gray-500 dark:text-gray-400 mt-2">📅 Due: ${task.due_date}</div>` : ''}
                            
                            <div class="ml-6 mt-3">
                                <div class="flex items-center gap-2 mb-2">
                                    <input type="text" id="subtask-input-${task.id}" placeholder="Add a subtask..." 
                                           onkeypress="handleSubtaskKeyPress(event, ${task.id})"
                                           class="flex-1 text-sm border rounded-lg px-3 py-1 bg-white/50 dark:bg-gray-700/50 focus:ring-2 focus:ring-purple-500">
                                    <button onclick="addSubTask(${task.id})" 
                                            class="px-3 py-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg text-sm hover:scale-105 transition">
                                        + Add
                                    </button>
                                </div>
                                <div class="space-y-1">
                                    ${task.sub_tasks.map(sub => `
                                            <div class="flex items-center gap-2 group justify-between">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" ${sub.is_completed ? 'checked' : ''} onchange="toggleSubTask(${sub.id})" class="w-4 h-4">
                                                    <span class="text-sm ${sub.is_completed ? 'line-through text-gray-400' : 'text-gray-700 dark:text-gray-300'}">${escapeHtml(sub.title)}</span>
                                                </div>
                                                <button onclick="deleteSubTask(${sub.id})" 
                                                        class="px-2 py-1 bg-red-500/20 hover:bg-red-500 text-red-600 hover:text-white rounded-lg text-xs transition">
                                                    🗑️ Hapus
                                                </button>
                                            </div>
                                        `).join('')}
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editTask(${task.id})" class="text-blue-500 hover:scale-110 transition">✏️</button>
                            <button onclick="deleteTask(${task.id})" class="text-red-500 hover:scale-110 transition">🗑️</button>
                        </div>
                    </div>
                </div>
            `).join('');
            attachDragEvents();
        }

        let draggedItem = null;

        function attachDragEvents() {
            document.querySelectorAll('.task-item').forEach(item => {
                item.setAttribute('draggable', 'true');
                item.addEventListener('dragstart', (e) => {
                    draggedItem = this;
                    e.dataTransfer.setData('text/plain', item.dataset.id);
                    item.style.opacity = '0.5';
                });
                item.addEventListener('dragend', (e) => {
                    if (draggedItem) draggedItem.style.opacity = '1';
                    draggedItem = null;
                });
            });
        }

        document.addEventListener('dragover', (e) => {
            if (e.target.closest('#tasks-container')) e.preventDefault();
        });

        document.addEventListener('drop', async (e) => {
            e.preventDefault();
            const targetItem = e.target.closest('.task-item');
            if (draggedItem && targetItem && draggedItem !== targetItem) {
                const container = document.getElementById('tasks-container');
                const items = [...container.children];
                const draggedIndex = items.indexOf(draggedItem);
                const targetIndex = items.indexOf(targetItem);

                if (draggedIndex < targetIndex) {
                    targetItem.insertAdjacentElement('afterend', draggedItem);
                } else {
                    targetItem.insertAdjacentElement('beforebegin', draggedItem);
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
            showToast('Task status updated!');
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

        async function deleteSubTask(id) {
            if (confirm('Delete this subtask?')) {
                await fetch(`/sub-tasks/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                await loadTasks();
                showToast('Subtask deleted!');
            }
        }

        let pendingDeleteId = null;

        function openDeleteModal(id) {
            pendingDeleteId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            pendingDeleteId = null;
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
            const saveBtn = document.getElementById('saveTaskBtn');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mx-auto"></div>';
            saveBtn.disabled = true;

            const id = document.getElementById('taskId').value;
            const data = {
                title: document.getElementById('taskTitle').value,
                description: document.getElementById('taskDesc').value,
                priority: document.getElementById('taskPriority').value,
                due_date: document.getElementById('taskDueDate').value,
                _token: csrfToken
            };

            try {
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
            } catch (error) {
                showToast('Error saving task!', 'error');
            } finally {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
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
            showToast('Exporting tasks...');
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
                showToast('Tasks imported!');
                window.location.reload();
            };
            reader.readAsText(file);
        });

        loadQuote();
        loadTasks();
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <a href="{{ route('tasks.index') }}"
                class="px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl hover:scale-105 transition-all duration-300 shadow-lg text-white">
                ← Back to Tasks
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="backdrop-blur-xl bg-white/30 dark:bg-gray-800/30 rounded-2xl p-6 shadow-xl border border-white/20">

                <!-- Calendar Header -->
                <div class="flex justify-between items-center mb-6">
                    <button onclick="prevMonth()"
                        class="px-4 py-2 bg-purple-500/20 hover:bg-purple-500 text-purple-600 hover:text-white rounded-xl transition">
                        ◀ Prev
                    </button>
                    <h3 id="currentMonthYear" class="text-2xl font-bold text-gray-800 dark:text-white"></h3>
                    <button onclick="nextMonth()"
                        class="px-4 py-2 bg-purple-500/20 hover:bg-purple-500 text-purple-600 hover:text-white rounded-xl transition">
                        Next ▶
                    </button>
                </div>

                <!-- Day Names -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @php $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; @endphp
                    @foreach ($days as $day)
                        <div class="text-center font-semibold text-sm text-gray-600 dark:text-gray-400 py-2">
                            {{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Grid -->
                <div id="calendarGrid" class="grid grid-cols-7 gap-2">
                    <!-- Filled by JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Task Detail Modal -->
    <div id="taskDetailModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-96 max-w-md shadow-2xl border border-white/20">
            <h3 class="text-xl font-bold mb-3">📋 Tasks on <span id="selectedDate"></span></h3>
            <div id="tasksOnDate" class="space-y-2 max-h-96 overflow-y-auto"></div>
            <div class="flex justify-end mt-4">
                <button onclick="closeTaskDetailModal()"
                    class="px-4 py-2 bg-gray-500 text-white rounded-xl">Close</button>
            </div>
        </div>
    </div>

    <style>
        .calendar-day {
            min-height: 100px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .calendar-day.has-tasks {
            background: rgba(139, 92, 246, 0.1);
        }

        .calendar-day.has-tasks:hover {
            background: rgba(139, 92, 246, 0.2);
            transform: scale(1.02);
        }

        .calendar-day.today {
            border: 2px solid #8b5cf6;
        }

        .task-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
        }
    </style>

    <script>
        const csrfToken = '{{ csrf_token() }}';
        let currentDate = new Date();
        let calendarData = {};

        function getPriorityBadge(priority) {
            return {
                high: 'bg-red-500',
                medium: 'bg-yellow-500',
                low: 'bg-green-500'
            } [priority] || 'bg-gray-500';
        }

        async function loadCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth() + 1;
            const response = await fetch(`/calendar-data?month=${month}&year=${year}`);
            calendarData = await response.json();
            renderCalendar();
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            document.getElementById('currentMonthYear').innerText = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date().toISOString().split('T')[0];

            let html = '';
            let day = 1;

            // Empty cells for previous month
            for (let i = 0; i < firstDay; i++) {
                html += `<div class="calendar-day bg-gray-50 dark:bg-gray-800/30 rounded-xl p-2 opacity-50"></div>`;
            }

            // Current month days
            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const tasks = calendarData[dateStr] || [];
                const hasTasks = tasks.length > 0;
                const isToday = dateStr === today;

                let dots = '';
                if (hasTasks) {
                    const colors = tasks.slice(0, 3).map(t => getPriorityBadge(t.priority));
                    dots =
                        `<div class="mt-1 flex gap-1 justify-center">${colors.map(c => `<div class="task-dot ${c}"></div>`).join('')}</div>`;
                    if (tasks.length > 3) dots += `<span class="text-xs text-gray-500 ml-1">+${tasks.length - 3}</span>`;
                }

                html += `<div class="calendar-day ${hasTasks ? 'has-tasks' : 'bg-white/50 dark:bg-gray-800/50'} rounded-xl p-2 ${isToday ? 'today' : ''}" 
                              onclick="showTasks('${dateStr}')">
                            <div class="text-right font-semibold ${isToday ? 'text-purple-600' : ''}">${d}</div>
                            ${dots}
                         </div>`;
            }

            document.getElementById('calendarGrid').innerHTML = html;
        }

        function showTasks(dateStr) {
            const tasks = calendarData[dateStr] || [];
            if (tasks.length === 0) return;

            document.getElementById('selectedDate').innerText = dateStr;
            const container = document.getElementById('tasksOnDate');
            container.innerHTML = tasks.map(task => `
                <div class="bg-white/50 dark:bg-gray-700/50 rounded-lg p-3 border border-white/20">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="px-2 py-1 rounded text-xs font-semibold ${getPriorityBadge(task.priority)} text-white">
                                ${task.priority.toUpperCase()}
                            </span>
                            <span class="ml-2 font-medium ${task.is_completed ? 'line-through' : ''}">${escapeHtml(task.title)}</span>
                        </div>
                        <input type="checkbox" ${task.is_completed ? 'checked' : ''} onchange="toggleTaskFromCalendar(${task.id}, '${dateStr}')">
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Subtasks: ${task.sub_tasks_completed}/${task.sub_tasks_count}</div>
                </div>
            `).join('');
            document.getElementById('taskDetailModal').classList.remove('hidden');
            document.getElementById('taskDetailModal').classList.add('flex');
        }

        async function toggleTaskFromCalendar(taskId, dateStr) {
            await fetch(`/tasks/${taskId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            await loadCalendar();
            showTasks(dateStr);
        }

        function closeTaskDetailModal() {
            document.getElementById('taskDetailModal').classList.add('hidden');
            document.getElementById('taskDetailModal').classList.remove('flex');
        }

        function prevMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            loadCalendar();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            loadCalendar();
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        loadCalendar();
    </script>
</x-app-layout>

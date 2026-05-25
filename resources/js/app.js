import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Dark mode toggle
document.addEventListener('DOMContentLoaded', () => {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
        darkModeToggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
        });
    }
});

// Task Manager dengan Alpine
document.addEventListener('alpine:init', () => {
    Alpine.data('taskManager', () => ({
        tasks: [],
        
        async initTasks() {
            await this.fetchTasks();
            
            // SortableJS for drag & drop
            const container = document.getElementById('task-container');
            if (container && typeof Sortable !== 'undefined') {
                new Sortable(container, {
                    handle: '.drag-handle',
                    onEnd: (evt) => {
                        const order = Array.from(container.children).map(el => el.dataset.id);
                        this.updateOrder(order);
                    }
                });
            }
        },
        
        async fetchTasks() {
            try {
                const response = await fetch('/tasks');
                this.tasks = await response.json();
            } catch (error) {
                console.error('Error fetching tasks:', error);
            }
        },
        
        async toggleTask(id) {
            try {
                await fetch(`/tasks/${id}/toggle`, { 
                    method: 'PATCH', 
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } 
                });
                await this.fetchTasks();
            } catch (error) {
                console.error('Error toggling task:', error);
            }
        },
        
        async updateOrder(order) {
            try {
                await fetch('/tasks/update-order', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                    },
                    body: JSON.stringify({ order })
                });
                await this.fetchTasks();
            } catch (error) {
                console.error('Error updating order:', error);
            }
        },
        
        async addSubTask(taskId) {
            const input = document.getElementById(`subtask-input-${taskId}`);
            const title = input?.value;
            if (!title) return;
            
            try {
                await fetch('/sub-tasks', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                    },
                    body: JSON.stringify({ task_id: taskId, title })
                });
                await this.fetchTasks();
                if (input) input.value = '';
            } catch (error) {
                console.error('Error adding subtask:', error);
            }
        },
        
        async toggleSubTask(id) {
            try {
                await fetch(`/sub-tasks/${id}/toggle`, { 
                    method: 'PATCH', 
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } 
                });
                await this.fetchTasks();
            } catch (error) {
                console.error('Error toggling subtask:', error);
            }
        },
        
        async deleteSubTask(id) {
            try {
                await fetch(`/sub-tasks/${id}`, { 
                    method: 'DELETE', 
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } 
                });
                await this.fetchTasks();
            } catch (error) {
                console.error('Error deleting subtask:', error);
            }
        },
        
        async deleteTask(id) {
            if (confirm('Delete this task?')) {
                try {
                    await fetch(`/tasks/${id}`, { 
                        method: 'DELETE', 
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } 
                    });
                    await this.fetchTasks();
                } catch (error) {
                    console.error('Error deleting task:', error);
                }
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
    }));
});

// Global functions
window.openTaskModal = () => {
    document.getElementById('modalTitle').innerText = 'New Task';
    document.getElementById('taskId').value = '';
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskDesc').value = '';
    document.getElementById('taskPriority').value = 'medium';
    document.getElementById('taskDueDate').value = '';
    document.getElementById('taskModal').classList.remove('hidden');
    document.getElementById('taskModal').classList.add('flex');
};

window.closeModal = () => {
    document.getElementById('taskModal').classList.add('hidden');
    document.getElementById('taskModal').classList.remove('flex');
};

window.saveTask = async () => {
    const id = document.getElementById('taskId').value;
    const data = {
        title: document.getElementById('taskTitle').value,
        description: document.getElementById('taskDesc').value,
        priority: document.getElementById('taskPriority').value,
        due_date: document.getElementById('taskDueDate').value,
        _token: document.querySelector('meta[name="csrf-token"]').content
    };
    
    const url = id ? `/tasks/${id}` : '/tasks';
    const method = id ? 'PUT' : 'POST';
    
    try {
        await fetch(url, { 
            method, 
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, 
            body: JSON.stringify(data) 
        });
        window.closeModal();
        
        // Refresh tasks
        const taskManager = document.querySelector('[x-data="taskManager()"]')?.__x?.$data;
        if (taskManager) await taskManager.fetchTasks();
        else window.location.reload();
    } catch (error) {
        console.error('Error saving task:', error);
    }
};

// Export/Import
window.exportJson = () => {
    window.location.href = '/export/json';
};

window.importJson = (file) => {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = async (ev) => {
        try {
            await fetch('/import/json', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: ev.target.result
            });
            window.location.reload();
        } catch (error) {
            console.error('Error importing:', error);
        }
    };
    reader.readAsText(file);
};
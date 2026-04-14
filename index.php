<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Todo++</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; background: #f5f7fa; }
        h1 { text-align: center; color: #2c3e50; }
        .input-group { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
        input { flex: 1; padding: 0.7rem; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; }
        button { padding: 0.7rem 1.2rem; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; }
        button:hover { background: #2980b9; }
        ul { list-style: none; padding: 0; }
        li { background: white; padding: 1rem; margin-bottom: 0.5rem; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        li.done { text-decoration: line-through; color: #7f8c8d; }
        .actions button { background: none; color: #e74c3c; padding: 0.3rem 0.6rem; font-size: 0.9rem; }
        .toggle { background: none; color: #27ae60; padding: 0.3rem 0.6rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>📝 Todo на PHP + MySQL+тест++++</h1>
    <div class="input-group">
        <input type="text" id="taskInput" placeholder="Новая задача..." autofocus>
        <button onclick="addTask()">Добавить</button>
    </div>
    <ul id="taskList"></ul>

    <script>
        const api = 'api.php';
        const list = document.getElementById('taskList');
        const input = document.getElementById('taskInput');

        async function fetchTasks() {
            const res = await fetch(api);
            const tasks = await res.json();
            list.innerHTML = '';
            tasks.forEach(t => {
                const li = document.createElement('li');
                li.className = t.is_done ? 'done' : '';
                li.innerHTML = `
                    <span>${escapeHtml(t.title)}</span>
                    <div class="actions">
                        <button class="toggle" onclick="toggleTask(${t.id}, ${t.is_done ? 0 : 1})">${t.is_done ? '↩️' : '✅'}</button>
                        <button onclick="deleteTask(${t.id})">🗑️</button>
                    </div>
                `;
                list.appendChild(li);
            });
        }

        async function addTask() {
            const title = input.value.trim();
            if (!title) return;
            await fetch(api, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({title}) });
            input.value = '';
            fetchTasks();
        }

        async function toggleTask(id, done) {
            await fetch(api, { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({id, is_done: done}) });
            fetchTasks();
        }

        async function deleteTask(id) {
            await fetch(api, { method: 'DELETE', headers: {'Content-Type':'application/json'}, body: JSON.stringify({id}) });
            fetchTasks();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        input.addEventListener('keypress', e => { if(e.key === 'Enter') addTask(); });
        fetchTasks();
    </script>
</body>
</html>
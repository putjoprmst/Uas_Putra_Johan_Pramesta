<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <script>
        async function fetchTasks() {
            const response = await fetch('api.php');
            const tasks = await response.json();
            const taskList = document.getElementById('taskList');
            taskList.innerHTML = '';
            tasks.forEach(task => {
                taskList.innerHTML += `<li>${task.task} <button onclick="deleteTask(${task.id})">Delete</button></li>`;
            });
        }

        async function addTask() {
            const taskInput = document.getElementById('taskInput');
            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ task: taskInput.value })
            });
            taskInput.value = '';
            fetchTasks();
        }

        async function deleteTask(id) {
            await fetch('api.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            });
            fetchTasks();
        }

        window.onload = fetchTasks;
    </script>
</head>
<body>
    <h1>To-Do List</h1>
    <input type="text" id="taskInput" placeholder="Add a new task">
    <button onclick="addTask()">Add Task</button>
    <ul id="taskList"></ul>
</body>
</html>

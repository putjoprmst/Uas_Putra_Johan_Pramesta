# Laporan Proyek Aplikasi To-Do List

## Pendahuluan
Proyek ini bertujuan untuk membuat aplikasi web sederhana sebagai To-Do List menggunakan PHP dan MySQL. Aplikasi ini memungkinkan pengguna untuk menambahkan, menghapus, dan melihat daftar tugas.

## Langkah-langkah Pembuatan

### 1. Persiapan Lingkungan
- **Instalasi XAMPP**: Menginstal XAMPP untuk menyediakan server Apache dan MySQL.
- **Mulai Server**: Mengaktifkan Apache dan MySQL melalui XAMPP Control Panel.

### 2. Membuat Database dan Tabel
- **Akses phpMyAdmin**: Menggunakan `http://localhost/phpmyadmin`.
- **Buat Database**: Membuat database baru dengan nama `uasjo`.
- **Buat Tabel**: Membuat tabel `todo_list` dengan kolom `id`, `task`, `created_at`, dan `updated_at`.

```sql
CREATE DATABASE uasjo;

USE uasjo;

CREATE TABLE todo_list (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 3. Struktur Folder Proyek
- Membuat folder proyek di `htdocs` dengan nama `uasjo_todo_app`.
- Membuat file PHP:
  - `index.php`: Antarmuka pengguna.
  - `api.php`: REST API untuk operasi CRUD.
  - `db.php`: Koneksi database.

### 4. Kode Program

#### Koneksi Database (`db.php`)

```php
<?php
function connect() {
    $host = 'localhost';
    $db = 'uasjo'; // Nama database yang telah dibuat
    $user = 'root'; // Ganti dengan username database Anda
    $pass = ''; // Ganti dengan password database Anda

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
```

#### REST API (`api.php`)

```php
<?php
header("Content-Type: application/json");
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $conn = connect();
        $result = $conn->query("SELECT * FROM todo_list");
        
        if ($result) {
            $tasks = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($tasks);
        } else {
            echo json_encode(["error" => "Failed to fetch tasks"]);
        }
        
        $conn->close();
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $conn = connect();
        $stmt = $conn->prepare("INSERT INTO todo_list (task) VALUES (?)");
        $stmt->bind_param("s", $data->task);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Task added successfully"]);
        } else {
            echo json_encode(["error" => "Failed to add task: " . $stmt->error]);
        }
        
        $stmt->close();
        $conn->close();
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $conn = connect();
        $stmt = $conn->prepare("UPDATE todo_list SET task = ? WHERE id = ?");
        $stmt->bind_param("si", $data->task, $data->id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        echo json_encode(["message" => "Task updated successfully"]);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $conn = connect();
        $stmt = $conn->prepare("DELETE FROM todo_list WHERE id = ?");
        $stmt->bind_param("i", $data->id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        echo json_encode(["message" => "Task deleted successfully"]);
        break;

    default:
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
```

#### Antarmuka Pengguna (`index.php`)

```php
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
```

### 5. Pengujian Aplikasi
- **Menambahkan Tugas**: Menggunakan antarmuka untuk menambahkan tugas baru.
- **Melihat Daftar Tugas**: Memastikan tugas yang ditambahkan muncul di bawah kolom input.
- **Menghapus Tugas**: Menghapus tugas dari daftar menggunakan tombol "Delete".

## Masalah yang Ditemui
- **Daftar Tugas Tidak Muncul**: Ditemukan kesalahan "Unexpected end of JSON input".
- **Tabel Tidak Terisi**: Tabel `todo_list` tidak terisi setelah menambahkan tugas.

## Solusi
- Memeriksa koneksi database dan memastikan informasi yang benar.
- Menguji API secara terpisah menggunakan Postman dan cURL.
- Menambahkan error reporting di PHP untuk melihat kesalahan.
- Memastikan bahwa data yang dikirim dalam format JSON benar.

## Kesimpulan
Aplikasi To-Do List berhasil dibuat dengan fitur dasar untuk menambahkan dan menghapus tugas. Meskipun ada beberapa masalah yang dihadapi, langkah-langkah pemecahan masalah yang tepat membantu menyelesaikan isu tersebut. Aplikasi ini dapat dikembangkan lebih lanjut dengan menambahkan fitur seperti pengeditan tugas dan penyimpanan sesi pengguna.

<?php
session_start();

session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Logout Berjaya</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f9fb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .logout-message {
            text-align: center;
            background: #fff;
            padding: 30px 50px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .logout-message h2 {
            color: #006EA0;
            margin-bottom: 15px;
        }
        .logout-message p {
            color: #333;
        }
    </style>
    <script>
        // Papar alert mesra pengguna
        window.addEventListener('DOMContentLoaded', function() {
            alert("Anda telah berjaya logout.");
            // Redirect ke login page selepas 1.5 saat
            setTimeout(function() {
                window.location.href = "login.php";
            }, 1500);
        });
    </script>
</head>
<body>
    <div class="logout-message">
        <h2>Logout Berjaya</h2>
        <p>Anda sedang dialihkan ke halaman login...<br>
        Jika tidak, <a href="login.php">klik di sini</a>.</p>
    </div>
</body>
</html>

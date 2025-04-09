<?php
require_once 'config.php';
session_start();

if (isset($_SESSION['username'])) {
  header('Location: dashboard.php');
  exit();
}

$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"] ?? '';
  $password = $_POST["password"] ?? '';

  if (empty($username) || empty($password)) {
    echo "Por favor, ingrese ambos campos.";
    exit();
  }

  $sql = "SELECT username, password, last_login, last_ip FROM users WHERE username = ?";
  $stmt = $conn->prepare($sql);

  if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
  }

  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row["password"])) {
      $user_ip = $_SERVER['REMOTE_ADDR'];
      $sql_update = "UPDATE users SET last_login = NOW(), last_ip = ? WHERE username = ?";
      $stmt_update = $conn->prepare($sql_update);
      $stmt_update->bind_param("ss", $user_ip, $username);

      if ($stmt_update->execute()) {
        $_SESSION["username"] = $row["username"];
        header("Location: dashboard.php");
        exit();
      }
    }
  }

  echo "El nombre de usuario o la contraseña son incorrectos.";
  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer">
  <link rel="stylesheet" href="build/css/app.css">
  <title>Task Manager</title>
</head>
<body>
  <div id="login-container">
    <form action="" method="post" id="login-form">
      <i class="fa-solid fa-lock"></i>
        <input type="text" id="username" name="username" placeholder="Usuario" required>
        <input type="password" id="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>
  </div>
</body>
</html>
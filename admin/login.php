<?php
session_start();
include_once("config.inc.php");

function cadastrarAdministrador($conn, $nome, $email, $senha) {
    $stmt = $conn->prepare("SELECT id FROM administradores WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return "Já existe um administrador com este e-mail.";
    }

    $stmt->close();

    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO administradores (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $hash);

    if ($stmt->execute()) {
        return true;
    } else {
        return "Erro ao cadastrar administrador.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'];
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($acao === 'login') {
        $stmt = $conn->prepare("SELECT id, nome, senha FROM administradores WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($senha, $admin['senha'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nome'] = $admin['nome'];
                redirect("dashboard.php");
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "Administrador não encontrado!";
        }
    } elseif ($acao === 'cadastro') {
        $nome = trim($_POST['nome']);
        $retorno = cadastrarAdministrador($conn, $nome, $email, $senha);
        if ($retorno === true) {
            echo "<script>
              alert('Administrador cadastrado com sucesso! Faça login.');
              window.location='login.php';
            </script>";
            exit;
        } else {
            $erro = $retorno;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | SmartStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="layout.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body {
  background: linear-gradient(120deg, #0f172a, #1e293b);
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  color: #fff;
}
.ss-login-box {
  background: #1e293b;
  padding: 2.5rem;
  border-radius: 16px;
  width: 100%;
  max-width: 380px;
  box-shadow: 0 6px 25px rgba(0,0,0,.4);
}
.ss-login-box h3 {
  font-weight: 600;
  color: #fff;
}
.form-control {
  background: #0f172a;
  color: #fff;
  border: 1px solid #334155;
}
.form-control:focus {
  border-color: #2563eb;
  box-shadow: none;
}
a { color: #60a5fa; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="ss-login-box text-center">
  <h3 class="mb-3"><i class="bi bi-box-seam"></i> SmartStock</h3>

  <?php if (isset($erro)): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Ops...',
        text: '<?= addslashes($erro) ?>'
      });
    </script>
  <?php endif; ?>

  <?php if (!isset($_GET['cadastro'])): ?>
  <form method="post">
    <input type="hidden" name="acao" value="login">
    <div class="mb-3 text-start">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3 text-start">
      <label>Senha</label>
      <input type="password" name="senha" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100">Entrar</button>
  </form>
  <div class="mt-3">
    <a href="?cadastro">Não tem conta? Cadastre-se</a>
  </div>

  <?php else: ?>
  <form method="post">
    <input type="hidden" name="acao" value="cadastro">
    <div class="mb-3 text-start">
      <label>Nome</label>
      <input type="text" name="nome" class="form-control" required>
    </div>
    <div class="mb-3 text-start">
      <label>Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3 text-start">
      <label>Senha</label>
      <input type="password" name="senha" class="form-control" required minlength="6">
    </div>
    <button class="btn btn-success w-100">Cadastrar</button>
  </form>
  <div class="mt-3">
    <a href="login.php">Já tem conta? Entrar</a>
  </div>
  <?php endif; ?>
</div>
</body>
</html>

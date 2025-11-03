<?php
session_start();
include_once("config.inc.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cliente = ['nome' => '', 'email' => '', 'telefone' => '', 'cidade' => '', 'estado' => ''];

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE clientes SET nome=?, email=?, telefone=?, cidade=?, estado=? WHERE id=?");
        $stmt->bind_param("sssssi", $nome, $email, $telefone, $cidade, $estado, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO clientes (nome, email, telefone, cidade, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $email, $telefone, $cidade, $estado);
    }

    if ($stmt->execute()) {
        header("Location: admin_clientes.php");
        exit;
    } else {
        $erro = "Erro ao salvar cliente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $id ? 'Editar' : 'Novo' ?> Cliente | SmartStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
  background: #0f172a;
  color: #e2e8f0;
  font-family: 'Segoe UI', sans-serif;
}
.main {
  padding: 2rem;
  margin-left: 250px;
}
.card {
  background: #1e293b;
  border: none;
  border-radius: 16px;
  color: #fff;
  padding: 1.5rem;
}
.form-control {
  background: #0f172a;
  color: #e2e8f0;
  border: 1px solid #334155;
}
.form-control:focus {
  background: #0f172a;
  border-color: #2563eb;
  box-shadow: none;
}
</style>
</head>
<body>

<div class="main">
  <h3 class="mb-3"><i class="bi bi-person-plus"></i> <?= $id ? 'Editar' : 'Novo' ?> Cliente</h3>

  <?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="post">
      <div class="mb-3">
        <label>Nome</label>
        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($cliente['nome']) ?>" required>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($cliente['email']) ?>">
      </div>
      <div class="mb-3">
        <label>Telefone</label>
        <input type="text" name="telefone" class="form-control" value="<?= htmlspecialchars($cliente['telefone']) ?>">
      </div>
      <div class="mb-3">
        <label>Cidade</label>
        <input type="text" name="cidade" class="form-control" value="<?= htmlspecialchars($cliente['cidade']) ?>">
      </div>
      <div class="mb-3">
        <label>Estado</label>
        <input type="text" name="estado" class="form-control" value="<?= htmlspecialchars($cliente['estado']) ?>">
      </div>
      <button type="submit" class="btn btn-success w-100 mt-2">Salvar</button>
    </form>
  </div>
</div>
</body>
</html>

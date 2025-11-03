<?php
session_start();
include_once("config.inc.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$produto = ['nome' => '', 'categoria' => '', 'preco' => '', 'estoque' => ''];

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $produto = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $preco = floatval($_POST['preco']);
    $estoque = intval($_POST['estoque']);

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE produtos SET nome=?, categoria=?, preco=?, estoque=? WHERE id=?");
        $stmt->bind_param("ssdii", $nome, $categoria, $preco, $estoque, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO produtos (nome, categoria, preco, estoque) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $nome, $categoria, $preco, $estoque);
    }

    if ($stmt->execute()) {
        header("Location: admin_produtos.php");
        exit;
    } else {
        $erro = "Erro ao salvar produto.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $id ? 'Editar' : 'Novo' ?> Produto | SmartStock</title>
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
  <h3 class="mb-3"><i class="bi bi-pencil-square"></i> <?= $id ? 'Editar' : 'Novo' ?> Produto</h3>

  <?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="post">
      <div class="mb-3">
        <label>Nome</label>
        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
      </div>
      <div class="mb-3">
        <label>Categoria</label>
        <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($produto['categoria']) ?>">
      </div>
      <div class="mb-3">
        <label>Preço</label>
        <input type="number" step="0.01" name="preco" class="form-control" value="<?= htmlspecialchars($produto['preco']) ?>">
      </div>
      <div class="mb-3">
        <label>Estoque</label>
        <input type="number" name="estoque" class="form-control" value="<?= htmlspecialchars($produto['estoque']) ?>">
      </div>
      <button type="submit" class="btn btn-success w-100 mt-2">Salvar</button>
    </form>
  </div>
</div>
</body>
</html>

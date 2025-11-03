<?php
session_start();
include_once("config.inc.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM clientes WHERE id = $id");
    header("Location: admin_clientes.php");
    exit;
}

$result = $conn->query("SELECT * FROM clientes ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Clientes | SmartStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
  background: #0f172a;
  color: #e2e8f0;
  font-family: 'Segoe UI', sans-serif;
}
.sidebar {
  height: 100vh;
  background: #1e293b;
  padding: 1rem;
  position: fixed;
  width: 230px;
}
.sidebar a {
  display: block;
  color: #cbd5e1;
  text-decoration: none;
  padding: .6rem;
  border-radius: 8px;
  margin-bottom: .3rem;
}
.sidebar a:hover, .sidebar a.active {
  background: #2563eb;
  color: #fff;
}
.main {
  margin-left: 250px;
  padding: 2rem;
}
.card {
  background: #1e293b;
  border: none;
  border-radius: 16px;
  color: #fff;
}
.table {
  color: #e2e8f0;
}
.btn-sm {
  border-radius: 6px;
}
</style>
</head>
<body>

<div class="sidebar">
  <h4 class="text-white"><i class="bi bi-box-seam"></i> SmartStock</h4>
  <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="admin_clientes.php" class="active"><i class="bi bi-people"></i> Clientes</a>
  <a href="admin_produtos.php"><i class="bi bi-cart"></i> Produtos</a>
  <a href="admin_vendas.php"><i class="bi bi-cash-coin"></i> Vendas</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
</div>

<div class="main">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="bi bi-people"></i> Clientes</h3>
    <a href="admin_clientes_editar.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo Cliente</a>
  </div>

  <div class="card p-3">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Telefone</th>
          <th>Cidade</th>
          <th>Estado</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($c = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $c['id'] ?></td>
          <td><?= htmlspecialchars($c['nome']) ?></td>
          <td><?= htmlspecialchars($c['email']) ?></td>
          <td><?= htmlspecialchars($c['telefone']) ?></td>
          <td><?= htmlspecialchars($c['cidade']) ?></td>
          <td><?= htmlspecialchars($c['estado']) ?></td>
          <td>
            <a href="admin_clientes_editar.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este cliente?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="confirmDeleteLabel">Confirmar exclusão</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Tem certeza de que deseja excluir este registro?
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="btnConfirmDelete" class="btn btn-danger">Excluir</a>
      </div>
    </div>
  </div>
</div>

<script>
function confirmDelete(url) {
  const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
  document.getElementById('btnConfirmDelete').href = url;
  modal.show();
}
</script>

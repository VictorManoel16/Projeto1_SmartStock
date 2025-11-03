<?php
session_start();
include_once("config.inc.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$titulo = "Vendas";

$sql = "
SELECT v.id, c.nome AS cliente, v.data_venda, v.total
FROM vendas v
LEFT JOIN clientes c ON c.id = v.cliente_id
ORDER BY v.data_venda DESC";
$vendas = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Vendas | SmartStock</title>
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
  box-shadow: 0 4px 12px rgba(0,0,0,.3);
}
.table-dark {
  --bs-table-bg: #1e293b;
  --bs-table-border-color: #334155;
  --bs-table-striped-bg: #24304a;
}
.btn-add {
  background: #2563eb;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: .5rem 1rem;
}
.btn-add:hover {
  background: #1d4ed8;
}
</style>
</head>
<body>

<div class="sidebar">
  <h4><i class="bi bi-box-seam"></i> SmartStock</h4>
  <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="admin_clientes.php"><i class="bi bi-people"></i> Clientes</a>
  <a href="admin_produtos.php"><i class="bi bi-cart"></i> Produtos</a>
  <a href="admin_vendas.php" class="active"><i class="bi bi-cash-coin"></i> Vendas</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
</div>

<div class="main">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-cash-coin"></i> Vendas</h3>
    <a href="admin_vendas_nova.php" class="btn-add"><i class="bi bi-plus-lg"></i> Nova Venda</a>
  </div>

  <div class="card p-4">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Data</th>
          <th>Total (R$)</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php while ($v = $vendas->fetch_assoc()): ?>
        <tr>
          <td>#<?= $v['id'] ?></td>
          <td><?= htmlspecialchars($v['cliente']) ?></td>
          <td><?= date('d/m/Y H:i', strtotime($v['data_venda'])) ?></td>
          <td><?= number_format($v['total'], 2, ',', '.') ?></td>
          <td>
            <a href="admin_vendas_detalhes.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-primary">
              <i class="bi bi-eye"></i> Ver
            </a>
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


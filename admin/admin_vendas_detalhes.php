<?php
session_start();
include_once("config.inc.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$venda = $conn->query("
SELECT v.*, c.nome AS cliente
FROM vendas v
LEFT JOIN clientes c ON c.id = v.cliente_id
WHERE v.id = $id
")->fetch_assoc();

if (!$venda) {
    die("Venda não encontrada.");
}

$itens = $conn->query("
SELECT iv.*, p.nome AS produto
FROM itens_venda iv
LEFT JOIN produtos p ON p.id = iv.produto_id
WHERE iv.venda_id = $id
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Detalhes da Venda #<?= $id ?> | SmartStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
  background: #0f172a;
  color: #e2e8f0;
  font-family: 'Segoe UI', sans-serif;
}
.main {
  margin: 2rem auto;
  width: 90%;
  max-width: 900px;
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
a.btn-back {
  background: #2563eb;
  color: #fff;
  border-radius: 8px;
  text-decoration: none;
  padding: .5rem 1rem;
}
a.btn-back:hover {
  background: #1d4ed8;
}
</style>
</head>
<body>

<div class="main">
  <a href="admin_vendas.php" class="btn-back mb-3 d-inline-block"><i class="bi bi-arrow-left"></i> Voltar</a>

  <div class="card p-4">
    <h4>Venda #<?= $venda['id'] ?></h4>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($venda['cliente']) ?></p>
    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($venda['data_venda'])) ?></p>
    <p><strong>Total:</strong> R$ <?= number_format($venda['total'], 2, ',', '.') ?></p>

    <hr>
    <h5>Itens da Venda</h5>
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>Produto</th>
          <th>Qtd</th>
          <th>Preço Unit.</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($i = $itens->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($i['produto']) ?></td>
          <td><?= $i['quantidade'] ?></td>
          <td>R$ <?= number_format($i['preco_unitario'], 2, ',', '.') ?></td>
          <td>R$ <?= number_format($i['subtotal'], 2, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>

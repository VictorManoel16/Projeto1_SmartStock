<?php
session_start();
include_once("config.inc.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// --- Dados do banco ---
$totalProdutos = $conn->query("SELECT COUNT(*) AS total FROM produtos")->fetch_assoc()['total'];
$totalClientes = $conn->query("SELECT COUNT(*) AS total FROM clientes")->fetch_assoc()['total'];
$totalVendas = $conn->query("SELECT COUNT(*) AS total FROM vendas")->fetch_assoc()['total'];
$totalFaturamento = $conn->query("SELECT IFNULL(SUM(total),0) AS total FROM vendas")->fetch_assoc()['total'];

// --- Gráfico (últimos 7 dias) ---
$sql = "SELECT DATE(data_venda) AS data, SUM(total) AS valor 
        FROM vendas 
        WHERE data_venda >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(data_venda)
        ORDER BY data_venda ASC";
$result = $conn->query($sql);

$labels = [];
$valores = [];
while ($row = $result->fetch_assoc()) {
    $labels[] = date('d/m', strtotime($row['data']));
    $valores[] = $row['valor'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard | SmartStock</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

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
.sidebar h4 {
  color: #fff;
  margin-bottom: 1.5rem;
}
.sidebar a {
  display: block;
  color: #cbd5e1;
  text-decoration: none;
  padding: .6rem;
  border-radius: 8px;
  margin-bottom: .3rem;
  transition: all .2s ease;
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
  transition: all .25s ease;
  cursor: pointer;
}
.card:hover {
  transform: translateY(-3px);
  background: #334155;
}
.card h5 { font-weight: 600; }
.btn-ver-vendas {
  background: #2563eb;
  color: #fff;
  font-weight: 500;
  border: none;
  border-radius: 8px;
  padding: .5rem 1rem;
  transition: all .2s ease;
  text-decoration: none;
  display: inline-block;
}
.btn-ver-vendas:hover {
  background: #1d4ed8;
  color: #fff;
}
footer {
  text-align: center;
  padding: 1rem;
  color: #94a3b8;
}
</style>
</head>
<body>

<div class="sidebar">
  <h4><i class="bi bi-box-seam"></i> SmartStock</h4>
  <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
  <a href="admin_clientes.php"><i class="bi bi-people"></i> Clientes</a>
  <a href="admin_produtos.php"><i class="bi bi-cart"></i> Produtos</a>
  <a href="admin_vendas.php"><i class="bi bi-cash-coin"></i> Vendas</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
  <hr>
  <small>Admin: <?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Administrador') ?></small>
</div>

<div class="main">
  <h3 class="mb-4"><i class="bi bi-speedometer"></i> Painel de Controle</h3>

  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card p-3 text-center" onclick="window.location='admin_produtos.php'">
        <h5><i class="bi bi-box"></i> Produtos</h5>
        <h2><?= $totalProdutos ?></h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3 text-center" onclick="window.location='admin_clientes.php'">
        <h5><i class="bi bi-people"></i> Clientes</h5>
        <h2><?= $totalClientes ?></h2>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3 text-center" onclick="window.location='admin_vendas.php'">
        <h5><i class="bi bi-receipt"></i> Vendas</h5>
        <h2><?= $totalVendas ?></h2>
        <a href="admin_vendas.php" class="btn-ver-vendas mt-2">
          <i class="bi bi-eye"></i> Ver Vendas
        </a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3 text-center">
        <h5><i class="bi bi-cash-stack"></i> Faturamento</h5>
        <h2>R$ <?= number_format($totalFaturamento, 2, ',', '.') ?></h2>
      </div>
    </div>
  </div>

  <div class="card p-4">
    <h5><i class="bi bi-graph-up"></i> Vendas - Últimos 7 dias</h5>
    <canvas id="graficoVendas" height="120"></canvas>
  </div>

  <footer class="mt-4">
    © <?= date('Y') ?> SmartStock — Painel Administrativo
  </footer>
</div>

<script>
const ctx = document.getElementById('graficoVendas');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
      label: 'Faturamento (R$)',
      data: <?= json_encode($valores) ?>,
      borderWidth: 2,
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59,130,246,.3)',
      tension: .3,
      fill: true,
      pointRadius: 5,
      pointHoverRadius: 7
    }]
  },
  options: {
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>

</body>
</html>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= isset($titulo) ? htmlspecialchars($titulo).' | SmartStock' : 'SmartStock Admin' ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
:root {
  --ss-bg: #f4f6fb;
  --ss-sidebar: #0f172a;
  --ss-accent: #2563eb;
  --ss-muted: #94a3b8;
  --ss-card: #ffffff;
}

* { box-sizing: border-box; }
body {
  margin: 0;
  font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
  background: var(--ss-bg);
  color: #0b1320;
}

.ss-sidebar {
  position: fixed;
  left: 0; top: 0; bottom: 0;
  width: 240px;
  background: var(--ss-sidebar);
  color: #cbd5e1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: #475569 transparent;
  z-index: 50;
}

.ss-sidebar::-webkit-scrollbar { width: 6px; }
.ss-sidebar::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }

.ss-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}
.ss-logo i { font-size: 1.5rem; color: var(--ss-accent); }
.ss-logo-text {
  font-weight: 700;
  color: #fff;
  font-size: 1.1rem;
  letter-spacing: .3px;
}

.ss-nav {
  display: flex;
  flex-direction: column;
  padding: 12px;
}
.ss-nav a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  margin: 4px 0;
  color: #cbd5e1;
  text-decoration: none;
  border-radius: 8px;
  transition: all .15s ease;
}
.ss-nav a:hover {
  background: rgba(255,255,255,0.08);
  color: #fff;
}
.ss-nav a.active {
  background: linear-gradient(90deg, var(--ss-accent), #7c3aed);
  color: #fff;
}

.ss-bottom {
  padding: 15px;
  border-top: 1px solid rgba(255,255,255,0.1);
  font-size: 0.9rem;
  text-align: center;
  color: var(--ss-muted);
}

.ss-bottom a {
  display: block;
  text-decoration: none;
  color: #f87171;
  margin-bottom: 8px;
  font-weight: 500;
}
.ss-bottom a:hover { color: #ef4444; }

.ss-main {
  margin-left: 240px;
  min-height: 100vh;
  background: var(--ss-bg);
  transition: margin .3s;
}

.ss-topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
}
.ss-title {
  font-size: 1.1rem;
  font-weight: 600;
}
.ss-user {
  color: var(--ss-muted);
  font-weight: 500;
}

.ss-content {
  padding: 24px;
}

@media (max-width: 900px) {
  .ss-sidebar {
    width: 70px;
  }
  .ss-main {
    margin-left: 70px;
  }
  .ss-logo-text, .ss-nav a span {
    display: none;
  }
  .ss-nav a {
    justify-content: center;
  }
}
</style>
</head>
<body>
  <aside class="ss-sidebar">
    <div>
      <div class="ss-logo">
        <i class="bi bi-box-seam"></i>
        <span class="ss-logo-text">SmartStock</span>
      </div>
<nav class="ss-nav">
  <a href="dashboard.php" class="<?= $titulo == 'Dashboard' ? 'active' : '' ?>">Dashboard</a>
  <a href="admin_clientes.php" class="<?= $titulo == 'Clientes' ? 'active' : '' ?>"> Clientes</a>
  <a href="admin_produtos.php" class="<?= $titulo == 'Produtos' ? 'active' : '' ?>"> Produtos</a>
</nav>

    </div>

    <div class="ss-bottom">
      <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
      <div>&copy; <?= date('Y') ?> SmartStock</div>
    </div>
  </aside>

  <main class="ss-main">
    <header class="ss-topbar">
      <div class="ss-title">
        <?= isset($titulo) ? htmlspecialchars($titulo) : 'Painel Administrativo' ?>
      </div>
      <div class="ss-user">
         <?= htmlspecialchars($_SESSION['admin_nome'] ?? 'Administrador') ?>
      </div>
    </header>

    <section class="ss-content container-fluid">

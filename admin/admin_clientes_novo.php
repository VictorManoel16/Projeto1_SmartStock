<?php
include_once("config.inc.php");
session_start();
if (!isset($_SESSION['admin_id'])) redirect("login.php");
$titulo = "Novo Cliente";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = trim($_POST['cliente'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($cliente === '' || $telefone === '') $erro = "Preencha os campos obrigatórios.";

    if (empty($erro)) {
        $stmt = $conn->prepare("INSERT INTO clientes (cliente,nome,cidade,estado,telefone,email) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss",$cliente,$nome,$cidade,$estado,$telefone,$email);
        $stmt->execute();
        $stmt->close();
        redirect("admin_clientes.php");
    }
}

ob_start();
?>
<div class="card-ghost p-3">
  <h5>Novo Cliente</h5>
  <?php if(isset($erro)): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="post">
    <div class="row">
      <div class="col-md-6 mb-3"><label>Cliente</label><input name="cliente" class="form-control" required></div>
      <div class="col-md-6 mb-3"><label>Nome</label><input name="nome" class="form-control"></div>
      <div class="col-md-4 mb-3"><label>Cidade</label><input name="cidade" class="form-control"></div>
      <div class="col-md-4 mb-3"><label>Estado</label><input name="estado" class="form-control"></div>
      <div class="col-md-4 mb-3"><label>Telefone</label><input name="telefone" class="form-control" required></div>
      <div class="col-md-12 mb-3"><label>Email</label><input name="email" type="email" class="form-control"></div>
    </div>
    <button class="btn btn-primary">Salvar</button>
    <a class="btn btn-secondary" href="admin_clientes.php">Cancelar</a>
  </form>
</div>
<?php
$conteudo = ob_get_clean();
include("layout_admin.php");
?>
<?php include("layout_end.php"); ?>

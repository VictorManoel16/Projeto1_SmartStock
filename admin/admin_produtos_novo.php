<?php
include_once("config.inc.php");
session_start();
if (!isset($_SESSION['admin_id'])) redirect("login.php");
$titulo = "Novo Produto";

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nome = trim($_POST['nome'] ?? '');
    $preco = floatval($_POST['preco'] ?? 0);
    $quantidade = intval($_POST['quantidade'] ?? 0);
    $estoque_min = intval($_POST['estoque_minimo'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');

    if ($nome==='' || $preco<=0) $erro = "Preencha nome e preço.";
    if (empty($erro)) {
        $stmt = $conn->prepare("INSERT INTO produtos (nome,preco,quantidade,estoque_minimo,descricao) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sdiis",$nome,$preco,$quantidade,$estoque_min,$descricao);
        $stmt->execute();
        $stmt->close();
        redirect("admin_produtos.php");
    }
}

ob_start();
?>
<div class="card-ghost p-3">
  <h5>Novo Produto</h5>
  <?php if(isset($erro)): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><label>Nome</label><input name="nome" class="form-control" required></div>
    <div class="mb-3"><label>Preço</label><input name="preco" type="number" step="0.01" class="form-control" required></div>
    <div class="row">
      <div class="col-md-4 mb-3"><label>Quantidade</label><input name="quantidade" type="number" class="form-control" value="0"></div>
      <div class="col-md-4 mb-3"><label>Estoque mínimo</label><input name="estoque_minimo" type="number" class="form-control" value="0"></div>
      <div class="col-md-4 mb-3"><label>Descrição</label><input name="descricao" class="form-control"></div>
    </div>
    <button class="btn btn-primary">Salvar</button>
  </form>
</div>
<?php
$conteudo = ob_get_clean();
include("layout_admin.php");
?>
<?php include("layout_end.php"); ?>

<?php
session_start();
include_once("config.inc.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$clientes = $conn->query("SELECT id, nome FROM clientes ORDER BY nome");
$produtos = $conn->query("SELECT id, nome, preco FROM produtos ORDER BY nome");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $produtosSelecionados = $_POST['produto_id'];
    $quantidades = $_POST['quantidade'];
    $precos = $_POST['preco_unitario'];
    $subtotais = $_POST['subtotal'];

    $total = array_sum($subtotais);

    $stmt = $conn->prepare("INSERT INTO vendas (cliente_id, data_venda, total) VALUES (?, NOW(), ?)");
    $stmt->bind_param("id", $cliente_id, $total);
    $stmt->execute();
    $venda_id = $conn->insert_id;

    $stmtItem = $conn->prepare("INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
    foreach ($produtosSelecionados as $i => $produto_id) {
        $qtd = $quantidades[$i];
        $preco = $precos[$i];
        $sub = $subtotais[$i];
        $stmtItem->bind_param("iiidd", $venda_id, $produto_id, $qtd, $preco, $sub);
        $stmtItem->execute();
    }

    echo "<script>alert('Venda registrada com sucesso!');window.location='dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Nova Venda | SmartStock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #0f172a; color: #e2e8f0; font-family: 'Segoe UI', sans-serif; }
.container { max-width: 900px; margin-top: 40px; }
.card { background: #1e293b; border: none; border-radius: 12px; color: #fff; }
.btn-primary { background-color: #2563eb; border: none; }
.btn-danger { background-color: #ef4444; border: none; }
input, select { background: #334155 !important; color: #fff !important; border: 1px solid #475569 !important; }
</style>
</head>
<body>

<div class="container">
  <div class="card p-4 shadow">
    <h3><i class="bi bi-receipt"></i> Nova Venda</h3>
    <form method="POST" id="formVenda">
      <div class="mb-3">
        <label class="form-label">Cliente</label>
        <select name="cliente_id" class="form-select" required>
          <option value="">Selecione o cliente</option>
          <?php while($c = $clientes->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <table class="table table-dark align-middle" id="tabelaProdutos">
        <thead>
          <tr>
            <th>Produto</th>
            <th style="width:100px">Qtd</th>
            <th style="width:150px">Preço</th>
            <th style="width:150px">Subtotal</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <select name="produto_id[]" class="form-select produto" required>
                <option value="">Selecione</option>
                <?php while($p = $produtos->fetch_assoc()): ?>
                  <option value="<?= $p['id'] ?>" data-preco="<?= $p['preco'] ?>">
                    <?= htmlspecialchars($p['nome']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </td>
            <td><input type="number" name="quantidade[]" class="form-control quantidade" min="1" value="1" required></td>
            <td><input type="text" name="preco_unitario[]" class="form-control preco" readonly></td>
            <td><input type="text" name="subtotal[]" class="form-control subtotal" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm removerProduto"><i class="bi bi-trash"></i></button></td>
          </tr>
        </tbody>
      </table>

      <div class="text-end mb-3">
        <button type="button" id="addProduto" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> Adicionar Produto</button>
      </div>

      <h4 class="text-end">Total: R$ <span id="totalGeral">0,00</span></h4>

      <div class="text-end mt-4">
        <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check2-circle"></i> Salvar Venda</button>
        <a href="dashboard.php" class="btn btn-secondary btn-lg"><i class="bi bi-arrow-left"></i> Voltar</a>
      </div>
    </form>
  </div>
</div>

<script>
function atualizarTotais() {
  let total = 0;
  document.querySelectorAll('#tabelaProdutos tbody tr').forEach(tr => {
    const qtd = parseFloat(tr.querySelector('.quantidade').value) || 0;
    const preco = parseFloat(tr.querySelector('.preco').value) || 0;
    const subtotal = qtd * preco;
    tr.querySelector('.subtotal').value = subtotal.toFixed(2);
    total += subtotal;
  });
  document.getElementById('totalGeral').textContent = total.toLocaleString('pt-BR', {minimumFractionDigits:2});
}

document.addEventListener('change', e => {
  if (e.target.classList.contains('produto')) {
    const preco = e.target.selectedOptions[0].dataset.preco;
    const tr = e.target.closest('tr');
    tr.querySelector('.preco').value = parseFloat(preco).toFixed(2);
    atualizarTotais();
  }
  if (e.target.classList.contains('quantidade')) atualizarTotais();
});

document.getElementById('addProduto').addEventListener('click', () => {
  const linha = document.querySelector('#tabelaProdutos tbody tr').cloneNode(true);
  linha.querySelectorAll('input').forEach(i => i.value = '');
  document.querySelector('#tabelaProdutos tbody').appendChild(linha);
});

document.addEventListener('click', e => {
  if (e.target.closest('.removerProduto')) {
    const linhas = document.querySelectorAll('#tabelaProdutos tbody tr');
    if (linhas.length > 1) e.target.closest('tr').remove();
    atualizarTotais();
  }
});
</script>

</body>
</html>

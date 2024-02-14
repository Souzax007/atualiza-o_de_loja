<?php
session_start();

// Verifica a autenticação do usuário
if (!isset($_SESSION['id_login'])) {
    header("Location:../protect.php");
    exit();
}

include("../conexao/conexao.php");

// Função para obter os tamanhos associados a um produto
function getTamanhosAssociados($conn, $id_produto) {
    $tamanhos = [];
    $sql = "SELECT id_tamanho FROM prod_tam WHERE id_produto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_produto);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tamanhos[] = $row['id_tamanho'];
    }
    return $tamanhos;
}

// Inicialização de variáveis
$id_produto = $id_categoria = $id_marca = $nm_produto = $ds_descricao = $vl_valor = $nr_estoque = $mensagem = "";
$acao = "insert"; // Por padrão, estamos inserindo um produto

// Verifica se há uma ID válida para atualização
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = intval($_GET["id"]);
    $acao = "upd"; // Atualizando um produto existente

    // Consulta SQL para obter os detalhes do produto
    $sql = "SELECT id_produto, id_categoria, id_marca, nm_produto, ds_descricao, vl_valor, nr_estoque FROM produtos WHERE id_produto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_produto = $row["id_produto"];
        $id_categoria = $row["id_categoria"];
        $id_marca = $row["id_marca"];
        $nm_produto = $row["nm_produto"];
        $ds_descricao = $row["ds_descricao"];
        $vl_valor = $row["vl_valor"];
        $nr_estoque = $row["nr_estoque"];
    } else {
        $mensagem = "Nenhum registro encontrado com ID: $id";
    }
}

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenção dos dados do formulário
    $id_produto = isset($_POST["id_produto"]) ? intval($_POST["id_produto"]) : 0;
    $id_categoria = isset($_POST["id_categoria"]) ? intval($_POST["id_categoria"]) : 0;
    $id_marca = isset($_POST["id_marca"]) ? intval($_POST["id_marca"]) : 0;
    $nm_produto = isset($_POST["nm_produto"]) ? mysqli_real_escape_string($conn, $_POST["nm_produto"]) : "";
    $ds_descricao = isset($_POST["ds_descricao"]) ? mysqli_real_escape_string($conn, $_POST["ds_descricao"]) : "";
    $vl_valor = isset($_POST["vl_valor"]) ? floatval($_POST["vl_valor"]) : 0.0;
    $nr_estoque = isset($_POST["nr_estoque"]) ? intval($_POST["nr_estoque"]) : 0;

    // Verificar se os campos estão preenchidos corretamente antes de inserir ou atualizar
    if (!empty($nm_produto) && !empty($ds_descricao) && $vl_valor > 0 && $nr_estoque >= 0) {
        if ($acao === "insert") {
            // Inserção de um novo produto
            $sql = "INSERT INTO produtos (id_categoria, id_marca, nm_produto, ds_descricao, vl_valor, nr_estoque) VALUES (?, ?, ?, ?, ?, ?)";
        } elseif ($acao === "upd") {
            // Atualização de um produto existente
            $sql = "UPDATE produtos SET id_categoria = ?, id_marca = ?, nm_produto = ?, ds_descricao = ?, vl_valor = ?, nr_estoque = ? WHERE id_produto = ?";
        }

        $stmt = $conn->prepare($sql);

        if ($acao === "insert") {
            $stmt->bind_param("iisssd", $id_categoria, $id_marca, $nm_produto, $ds_descricao, $vl_valor, $nr_estoque);
        } elseif ($acao === "upd") {
            $stmt->bind_param("iisssdi", $id_categoria, $id_marca, $nm_produto, $ds_descricao, $vl_valor, $nr_estoque, $id_produto);
        }

        if ($stmt->execute()) {
            if ($acao === "insert") {
                $id_produto = mysqli_insert_id($conn);
            }
            $mensagem = "Produto " . ($acao === "insert" ? "inserido" : "atualizado") . " com sucesso!";

            // Atualizar os tamanhos associados ao produto
            $id_tam = isset($_POST["id_tam"]) ? $_POST["id_tam"] : [];
            $sql_delete = "DELETE FROM prod_tam WHERE id_produto = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $id_produto);
            $stmt_delete->execute();
            foreach ($id_tam as $valor_tam) {
                $sql_insert_prod_tam = "INSERT INTO prod_tam (id_produto, id_tamanho) VALUES (?, ?)";
                $stmt_prod_tam = $conn->prepare($sql_insert_prod_tam);
                $stmt_prod_tam->bind_param("ii", $id_produto, $valor_tam);
                $stmt_prod_tam->execute();
            }
        } else {
            $mensagem = "Erro ao " . ($acao === "insert" ? "inserir" : "atualizar") . " os dados: " . $stmt->error;
        }
    } else {
        $mensagem = "Certifique-se de preencher os campos corretamente.";
    }
}

// Consulta SQL para obter categorias, marcas e tamanhos
$sql_categorias = "SELECT id_categoria, nm_categoria FROM categorias";
$sql_marcas = "SELECT id_marca, nm_marca FROM marcas";
$sql_tam = "SELECT id_tam, nm_tam FROM tamanhos";

$result_categorias = $conn->query($sql_categorias);
$result_marcas = $conn->query($sql_marcas);
$result_tam = $conn->query($sql_tam);

$categorias = $result_categorias->fetch_all(MYSQLI_ASSOC);
$marcas = $result_marcas->fetch_all(MYSQLI_ASSOC);
$tam = $result_tam->fetch_all(MYSQLI_ASSOC);

// Obter os tamanhos associados ao produto (se aplicável)
if ($acao === "upd") {
    $prod_tam = getTamanhosAssociados($conn, $id_produto);
} else {
    $prod_tam = []; // Inicializar como um array vazio
}

// Fechar a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($acao === "insert") ? "Inserir" : "Atualizar"; ?> Produto</title>
    <link rel="stylesheet" href="../produtos/style2.css">
    <script>
        function func_categoria(valor) {
            var idTam = document.getElementById("id_tam");
            idTam.style.display = (valor == 93) ? "block" : "none";
        }
    </script>
</head>
<body onload="func_categoria(<?php echo $id_categoria; ?>);">
<a href="../produtos/prod.php"><button class="btn">Voltar</button></a>

<div class="add">
    <form name="form_produtos" class="for" action="prod_form.php?id=<?php echo $id_produto; ?>" method="POST">
        <h1><?php echo ($acao === "insert") ? "Inserir" : "Atualizar"; ?> produto</h1>

        <select name="id_categoria" required="required" class="lista-select" onchange="func_categoria(this.value);">
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo ($categoria['id_categoria'] == $id_categoria) ? 'selected' : ''; ?>>
                    <?php echo $categoria['nm_categoria']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div id="id_tam" class="checkbox" style="display:<?php echo ($id_categoria == 93) ? 'block' : 'none'; ?>">
            <?php foreach ($tam as $tam_option): ?>
                <div style="display: inline-block; margin:5px;">
                    <label>
                        <input type="checkbox" name="id_tam[]" value="<?php echo $tam_option['id_tam']; ?>" <?php echo (in_array($tam_option['id_tam'], $prod_tam)) ? 'checked' : ''; ?>>
                        <?php echo $tam_option['nm_tam']; ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <select name="id_marca" required="required" class="lista-select">
            <?php foreach ($marcas as $marca): ?>
                <option value="<?php echo $marca['id_marca']; ?>" <?php echo ($marca['id_marca'] == $id_marca) ? 'selected' : ''; ?>>
                    <?php echo $marca['nm_marca']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="nm_produto">Nome <span>*</span></label>
        <input type="text" name="nm_produto" placeholder="Nome do produto" value="<?php echo $nm_produto; ?>">

        <label for="ds_descricao">Descrição <span>*</span></label>
        <input type="text" name="ds_descricao" placeholder="Descrição do produto" value="<?php echo $ds_descricao; ?>">

        <label for="vl_valor">Valor <span>*</span></label>
        <input type="number" name="vl_valor" placeholder="Valor do produto" value="<?php echo $vl_valor; ?>">

        <label for="nr_estoque">Estoque <span>*</span></label>
        <input type="number" name="nr_estoque" placeholder="Quantidade em estoque" value="<?php echo $nr_estoque; ?>">

        <input type="hidden" name="id_produto" value="<?php echo $id_produto; ?>">
        <button type="submit">SALVAR</button>
        <p><?php echo $mensagem; ?></p>
    </form>
</div>
</body>
</html>

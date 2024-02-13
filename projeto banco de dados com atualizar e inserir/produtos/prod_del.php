<?php
include("../conexao/conexao.php");

if (!is_numeric($_GET["id"])) {
    echo "ID inválido.";
} else {
    $id = (int)$_GET["id"];
    
    // Excluir registros relacionados na tabela prod_tam
    $sql_delete_prod_tam = "DELETE FROM prod_tam WHERE id_produto = ?";
    $stmt_prod_tam = $conn->prepare($sql_delete_prod_tam);
    $stmt_prod_tam->bind_param("i", $id);
    
    if ($stmt_prod_tam->execute()) {
        // Agora podemos excluir o registro da tabela produtos
        $sql_delete_produtos = "DELETE FROM produtos WHERE id_produto = ?";
        $stmt_produtos = $conn->prepare($sql_delete_produtos);
        $stmt_produtos->bind_param("i", $id);

        if ($stmt_produtos->execute()) {
            //echo "Registro excluído com sucesso.";
            header("Location:prod.php");
        } else {
            echo "Erro ao excluir o registro: " . $stmt_produtos->error;
        }
    } else {
        echo "Erro ao excluir os registros relacionados: " . $stmt_prod_tam->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ola</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100&display=swap">
    <style>
        nav {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
        }

        .btn {
            width: 80px; /* ajuste de largura */
            height: 40px; /* ajuste de altura */
            cursor: pointer;
            background-color: white;
            color: black;
            border: 1px solid #91C9FF;
            outline: none;
            transition: 1s ease-in-out;
            text-decoration: none; /* Remover sublinhado dos links */
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            font-size: 14px; /* ajuste do tamanho da fonte */
            font-weight: 100;
            border-radius: 20px;
        }

        .btn:hover {
            background: black;
            color: white;
        }

        .btn svg {
            position: absolute;
            left: 0;
            top: 0;
            fill: none;
            stroke: #fff;
            stroke-dasharray: 100 380; /* ajuste da distância entre os traços */
            stroke-dashoffset: 100;
            transition: 0.5s ease-in-out;
            border-radius: 20px;
        }

        .btn:hover svg {
            stroke-dashoffset: -480; /* ajuste da distância percorrida pelo traço */
        }

        .btn-flip {
            width: 80px; /* ajuste de largura */
            height: 40px; /* ajuste de altura */
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            position: relative;
            color: black;
            font-size: 14px;
            font-weight: bold;
            background-color: white;
            border: 1px solid #adadaf;
            border-radius: 20px;
            overflow: hidden;
        }

        .btn-flip:after {
            content: attr(data-back);
            position: absolute;
            background-color:black;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.5s;
            transform: translateY(100%);
            border-radius: 20px;
            color:white;
        }

        .btn-flip:hover:after {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <nav>
        <a href="../produtos/prod.php" class="btn">
            <svg width="80px" height="40px" viewBox="0 0 180 60" class="border">
                <polyline points="179,1 179,59 1,59 1,1 179,1" class="bg-line" />
                <polyline points="179,1 179,59 1,59 1,1 179,1" class="hl-line" />
            </svg>
            <span>Produtos</span>
        </a>
        <a href="../categorias/ctg.php" class="btn">
            <svg width="80px" height="40px" viewBox="0 0 180 60" class="border">
                <polyline points="179,1 179,59 1,59 1,1 179,1" class="bg-line" />
                <polyline points="179,1 179,59 1,59 1,1 179,1" class="hl-line" />
            </svg>
            <span>Categorias</span>
        </a>
        <a href="../marcas/mrc.php" class="btn">
            <svg width="80px" height="40px" viewBox="0 0 180 60" class="border">
                <polyline points="179,1 179,59 1,59 1,1 179,1" class="bg-line" />
                <polyline points="179,1 179,59 1,59 1,1 179,1" class="hl-line" />
            </svg>
            <span>Marcas</span>
        </a>
        <a href="../tamanhos/tam.php" class="btn">
            <svg width="80px" height="40px" viewBox="0 0 180 60" class="border">
                <polyline points="179,1 179,59 1,59 1,1 179,1" class="bg-line" />
                <polyline points="179,1 179,59 1,59 1,1 179,1" class="hl-line" />
            </svg>
            <span>Tamanhos</span>
        </a>
        <a href="../sair.php" class="btn-flip" data-front="Perfil" data-back="Mesmo?" style="margin-left: auto;">Sair</a>

    </nav>
</body>
</html>

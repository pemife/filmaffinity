<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
    </head>
    <body>
        <?php
        $pdo = new PDO('pgsql:host=localhost;dbname=fa', 'fa', 'fa');
        $numFilas = $pdo->exec("INSERT INTO generos (genero)
                                VALUES ('Costumbrismo')");
        $st = $pdo->query('SELECT * FROM generos');
        ?>
        <table border="1" style="margin:auto">
            <thead>
                <th>Id</th>
                <th>Género</th>
            </thead>
            <tbody>
                <?php foreach ($st as $fila): ?>
                    <tr>
                        <td><?= $fila['id'] ?></td>
                        <td><?= $fila['genero'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </body>
</html>

<?php session_start() ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bases de datos</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <style media="screen">
            #busqueda { margin-top: 1em; }
        </style>
    </head>
    <body>
        <?php
        require '../comunes/auxiliar.php';
        encabezado();
        ?>
        <div class="container">
            <br>
            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="row">
                    <div class="alert alert-success" role="alert">
                        <?= $_SESSION['mensaje'] ?>
                    </div>
                </div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif ?>
            <div class="row">
                <?php
                $pdo = conectar();

                if (isset($_POST['id'])) {
                    $id = $_POST['id'];
                    $pdo->beginTransaction();
                    $pdo->exec('LOCK TABLE peliculas IN SHARE MODE');
                    if (!buscarPelicula($pdo, $id)) { ?>
                        <h3>La película no existe.</h3>
                        <?php
                    } else {
                        $st = $pdo->prepare('DELETE FROM peliculas WHERE id = :id');
                        $st->execute([':id' => $id]); ?>
                        <h3>Película borrada correctamente.</h3>
                        <?php
                    }
                    $pdo->commit();
                }

                $where = $execute = [];
                $buscarTitulo = $buscarAnyo = "";

                if(isset($_GET['buscarTitulo'])) {
                    $buscarTitulo = trim($_GET['buscarTitulo']);
                    if($buscarTitulo !== ''){
                        $where[] = 'titulo ILIKE :titulo';
                        $execute[':titulo'] = "%$buscarTitulo%";
                    }
                }

                if(isset($_GET['buscarAnyo'])) {
                    $buscarAnyo = trim($_GET['buscarAnyo']);
                    if($buscarAnyo !== ''){
                        $where[] = 'anyo::text = :anyo';
                        $execute[':anyo'] = $buscarAnyo;
                    }
                }
                $where = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

                $st = $pdo->prepare("SELECT p.*, genero
                                       FROM peliculas p
                                       JOIN generos g
                                         ON genero_id = g.id
                                     $where
                                   ORDER BY id");
                $st->execute($execute);
                ?>
            </div>
            <div class="row" id="busqueda">
                <div class="col-md-12">
                    <fieldset>
                        <legend>Buscar...</legend>
                        <form action="" method="get" class="form-inline">
                            <div class="form-group">
                                <label for="buscarTitulo">Buscar por título:</label>
                                <input id="buscarTitulo" type="text" name="buscarTitulo"
                                       value="<?= $buscarTitulo ?>"
                                       class="form-control">
                                <br>
                                <label for="buscarAnyo">Buscar por año:</label>
                                <input id="buscarAnyo" type="text" name="buscarAnyo"
                                       value="<?= $buscarAnyo ?>"
                                       class="form-control">
                            </div>
                            <br>
                            <input type="submit" value="Buscar" class="btn btn-primary">
                        </form>
                    </fieldset>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <th>Título</th>
                            <th>Año</th>
                            <th>Sinopsis</th>
                            <th>Duración</th>
                            <th>Género</th>
                            <th>Acciones</th>
                        </thead>
                        <tbody>
                            <?php foreach ($st as $fila): ?>
                                <tr>
                                    <td><?= h($fila['titulo']) ?></td>
                                    <td><?= h($fila['anyo']) ?></td>
                                    <td><?= h($fila['sinopsis']) ?></td>
                                    <td><?= h($fila['duracion']) ?></td>
                                    <td><?= h($fila['genero']) ?></td>
                                    <td>
                                        <a href="confirm_borrado.php?id=<?= $fila['id'] ?>"
                                           class="btn btn-xs btn-danger">
                                            Borrar
                                        </a>
                                        <a href="modificar.php?id=<?= $fila['id'] ?>"
                                           class="btn btn-xs btn-info">
                                            Modificar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <a href="insertarPelicula.php" class="btn btn-info">Insertar una nueva película</a>
                </div>
            </div>
            <?php if (!isset($_COOKIE['acepta'])): ?>
                <nav class="navbar navbar-fixed-bottom navbar-inverse">
                    <div class="container">
                        <div class="navbar-text navbar-right">
                            Tienes que aceptar las políticas de cookies.
                            <a href="crear_cookie.php" class="btn btn-success">Aceptar cookies</a>
                        </div>
                    </div>
                </nav>
            <?php endif ?>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>

<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'Database.php';

if (!isset($_POST['valor']) || !isset($_POST['id_camara'])) {
    die("Faltan datos POST");
}

$database = new Database();
$db = $database->getConnection();

$temp_a_registrar = floatval($_POST['valor']);
$id_camara_a_registrar = intval($_POST['id_camara']);

$sqlSensor =
"SELECT id_sensor
 FROM Sensor
 WHERE id_camara = :id_camara
 LIMIT 1";

$stmt = $db->prepare($sqlSensor);

$stmt->execute([
    ':id_camara' => $id_camara_a_registrar
]);

$sensor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sensor) {
    die("No existe sensor asociado a la camara");
}

$id_sensor = $sensor['id_sensor'];

$sql_limite_maximo =
"SELECT temperatura
 FROM limites_temperaturas
 WHERE descripcion = 'maxima'";

$stmt = $db->prepare($sql_limite_maximo);
$stmt->execute();

$temp_MAXIMA =
$stmt->fetch(PDO::FETCH_ASSOC)['temperatura'];

$sql_limite_minimo =
"SELECT temperatura
 FROM limites_temperaturas
 WHERE descripcion = 'minima'";

$stmt = $db->prepare($sql_limite_minimo);
$stmt->execute();

$temp_MINIMA =
$stmt->fetch(PDO::FETCH_ASSOC)['temperatura'];

$sql =
"INSERT INTO Temperatura
(valor, fecha, hora, id_sensor)
VALUES
(:valor, CURDATE(), CURTIME(), :id_sensor)";

$stmt = $db->prepare($sql);

$stmt->execute([
    ':valor' => $temp_a_registrar,
    ':id_sensor' => $id_sensor
]);

if ($temp_a_registrar > $temp_MAXIMA) {

    $respuesta_final =
    "La temperatura supera la temperatura maxima ";

} elseif ($temp_a_registrar < $temp_MINIMA) {

    $respuesta_final =
    "La temperatura es menor a la temperatura minima ";

} else {

    $respuesta_final =
    "Temperatura normal";
}

header('Content-Type: application/json');

echo json_encode($respuesta_final);
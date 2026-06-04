<?php

require_once 'Database.php';

$Database = new Database();
$db = $Database->getConnection();

$id_camara = $_GET['id_camara'];

$sql = "
SELECT t.valor, t.fecha, t.hora
FROM Temperatura t
JOIN Sensor s ON t.id_sensor = s.id_sensor
WHERE s.id_camara = :id_camara
ORDER BY t.fecha DESC, t.hora DESC
LIMIT 1
";

$consulta = $db->prepare($sql);

$consulta->bindParam(
    ':id_camara',
    $id_camara,
    PDO::PARAM_INT
);

$consulta->execute();

$temperatura_actual =
$consulta->fetch(PDO::FETCH_ASSOC);

echo json_encode($temperatura_actual);
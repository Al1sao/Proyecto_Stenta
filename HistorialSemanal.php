<?php

require_once 'Database.php';

$Database = new Database();
$db = $Database->getConnection();

//Obtiene el id de la cámara desde la solicitud GET
$id_camara = $_GET['id_camara'];

//Realiza la consulta para obtener el historial de temperaturas de la cámara especificada
$sql = "SELECT Temperatura.*
        FROM Temperatura
        JOIN Sensor
        ON Temperatura.id_sensor = Sensor.id_sensor
        WHERE Sensor.id_camara = :id_camara
        AND Temperatura.fecha >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
        ORDER BY Temperatura.fecha ASC, Temperatura.hora ASC";

//Deja preparada la consulta, vincula el parámetro, la ejecuta y obtiene el resultado
$consulta = $db->prepare($sql);
$consulta->bindParam(':id_camara', $id_camara, PDO::PARAM_INT);
$consulta->execute();

//Devuelve el historial de temperaturas en formato JSON(para que el frontend pueda procesarla fácilmente)
$historial_temperaturas = $consulta->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($historial_temperaturas);

?>
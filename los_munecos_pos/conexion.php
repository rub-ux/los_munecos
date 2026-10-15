<?php
// conexion.php

    $host = '127.0.0.1'; // En local. Cuando subas a HostGator, probablemente siga siendo localhost.
    $db   = 'los_munecos_pos'; // Nombre de tu base de datos en MySQL
    $user = 'root'; // Tu usuario (en HostGator crearás uno propio)
    $pass = ''; // Tu contraseña
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Nos devuelve arreglos limpios para el JSON
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // Si falla, detenemos todo y mostramos un error en JSON
        echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
        exit;
    }
?>
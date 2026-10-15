<?php
// Configuramos los encabezados para que la tablet sepa que es un JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permite que nuestra maqueta lea los datos

// Llamamos a nuestro archivo de conexión (subiendo una carpeta con ../)
require_once '../conexion.php';

try {
    // Buscamos los productos y su categoría correspondiente
    $query = "SELECT p.ID_Producto as id, p.Nombre as nombre, p.Precio_Base as precio, c.Nombre as categoria 
              FROM Productos p 
              INNER JOIN Categorias c ON p.ID_Categoria = c.ID_Categoria 
              WHERE p.Estatus_Activo = 1";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $productos = $stmt->fetchAll();
    
    // Convertimos el resultado de la base de datos a formato JSON
    echo json_encode($productos);
    
} catch (\PDOException $e) {
    echo json_encode(["error" => "Error al obtener el menú: " . $e->getMessage()]);
}
?>
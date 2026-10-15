<?php
// api/guardar_orden.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../conexion.php';

// Leemos el JSON que envía el JavaScript
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['carrito']) || count($data['carrito']) === 0) {
    echo json_encode(['error' => 'La comanda está vacía.']);
    exit;
}

try {
    // Iniciamos una transacción SQL segura
    $pdo->beginTransaction();

    $subtotal = $data['total'];
    $iva = 0; // Se puede calcular en el futuro
    $total = $subtotal;
    
    // Asumimos el usuario 1 y método de pago 1 (Efectivo) para esta etapa del desarrollo
    $id_usuario = 1; 
    $id_metodo_pago = 1; 

    // 1. Creamos la Orden General
    $sqlOrden = "INSERT INTO Ordenes (Subtotal, IVA, Total, ID_Metodo_Pago, Estado, ID_Usuario_Crea) 
                 VALUES (?, ?, ?, ?, 'Pagada', ?)";
    $stmtOrden = $pdo->prepare($sqlOrden);
    $stmtOrden->execute([$subtotal, $iva, $total, $id_metodo_pago, $id_usuario]);
    
    // Obtenemos el ID del ticket que se acaba de crear
    $id_orden = $pdo->lastInsertId();

    // 2. Insertamos cada hamburguesa/producto en el Detalle de la Orden
    $sqlDetalle = "INSERT INTO Detalle_Ordenes (ID_Orden, ID_Producto, Cantidad, Precio_Unitario, Subtotal) 
                   VALUES (?, ?, 1, ?, ?)";
    $stmtDetalle = $pdo->prepare($sqlDetalle);
    
    foreach ($data['carrito'] as $item) {
        $stmtDetalle->execute([$id_orden, $item['id'], $item['precio'], $item['precio']]);
    }

    // Sellamos la transacción en la base de datos
    $pdo->commit();

    echo json_encode(['success' => true, 'mensaje' => 'Cobro exitoso', 'orden_id' => $id_orden]);

} catch (Exception $e) {
    // Si hay un error, cancelamos todos los insert de esta orden
    $pdo->rollBack();
    echo json_encode(['error' => 'Error al guardar la venta: ' . $e->getMessage()]);
}
?>
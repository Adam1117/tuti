<?php
include('db_connect.php');
header('Content-Type: application/json'); // JSON válaszhoz

$data = json_decode(file_get_contents("php://input"), true); // JSON adatok feldolgozása

if (isset($data['id']) && isset($data['status'])) {
    $id = $data['id'];
    $status = $data['status'];
    $estimated_time = isset($data['estimated_time']) ? intval($data['estimated_time']) : null;

    // SQL lekérdezés építése
    $sql = "UPDATE orders SET status = '$status'";
    if ($status === 'Elfogadva' && $estimated_time !== null) {
        $sql .= ", estimated_time = $estimated_time";
    }
    $sql .= " WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Rendelés státusza sikeresen frissítve!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Hiba az adatbázis-frissítés során: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Hibás adatok érkeztek a kérésben."]);
}
?>
// Email értesítés (jelenleg deaktiválva)
/*
if ($status == 'accepted') {
    $to = "customer_email@example.com";
    $subject = "Rendelés elfogadva";
    $message = "A rendelésed elfogadásra került. Várható elkészítési idő: $estimated_time perc.";
    mail($to, $subject, $message);
} elseif ($status == 'rejected') {
    $to = "customer_email@example.com";
    $subject = "Rendelés elutasítva";
    $message = "A rendelésedet elutasítottuk.";
    mail($to, $subject, $message);
}
*/
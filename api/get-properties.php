<?php
session_start();

require "../db/connect.php";

$user_id = $_SESSION['user_id'];

function get_my_properties($conn, $user_id)
{
    $stmt = $conn->prepare("
        SELECT title, property_type, price, status, city, bedrooms, bathrooms, area
        FROM properties
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $properties = [];
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }


     echo json_encode($properties);
}
get_my_properties($conn, $user_id);







?>
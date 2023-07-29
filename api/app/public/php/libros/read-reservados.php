<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ERROR);
if ($_SERVER['REQUEST_METHOD'] !== 'GET') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Bad Request Detected! Only get method is allowed',
    ]);
    exit;
endif;
require dirname(__DIR__)."/classes/Database.php";
//require '../classes/Database.php';
$database = new Database();
$conn = $database->dbConnection();
$iduser = null;

if (isset($_GET['iduser'])) {
    $iduser = filter_var($_GET['iduser'], FILTER_VALIDATE_INT, [
        'options' => [
            'default' => 'all_books',
            'min_range' => 1
        ]
    ]);
}

try {

    // SELECT distinctrow id_libro, id_usuario, isbn FROM `libros` inner join `reservas` on reservas.id_libro=libros.id WHERE id_usuario=16
    $sql = "SELECT distinctrow * FROM `libros` inner join `reservas` on  reservas.id_libro=libros.id WHERE id_usuario='$iduser'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0) :
        $data = null;
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // aseguro un array
        echo json_encode([
            'success' => 1,
            'data' => $data,
        ]);

    else :
        echo json_encode([
            'success' => 0,
            'message' => 'No Record Found!',
        ]);
    endif;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}

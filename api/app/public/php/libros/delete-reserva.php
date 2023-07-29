<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}


if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Bad Request detected. HTTP method should be DELETE',
    ]);
    exit;
endif;

require '../classes/Database.php';
$database = new Database();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));
//echo $data = file_get_contents("php://input");

$iduser =  $_GET['iduser'];
$idlibro =  $_GET['idlibro'];



if (!isset($iduser) || !isset($idlibro) ) {
    echo json_encode(['success' => 0, 'message' => 'Please provide the post ID.']);
    exit;
}

try {

    $fetch_post = "SELECT * FROM reservas WHERE id_usuario=:iduser and id_libro=:idlibro";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':iduser', $iduser, PDO::PARAM_INT);
    $fetch_stmt->bindValue(':idlibro', $idlibro, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :

        $delete_post = "DELETE FROM reservas WHERE id_usuario=:iduser and id_libro=:idlibro";
        $delete_post_stmt = $conn->prepare($delete_post);
        $delete_post_stmt->bindValue(':iduser', $iduser,PDO::PARAM_INT);
        $delete_post_stmt->bindValue(':idlibro', $idlibro,PDO::PARAM_INT);

        if ($delete_post_stmt->execute()) {

            echo json_encode([
                'success' => 1,
                'message' => 'Record Deleted successfully'
            ]);
            exit;
        }

        echo json_encode([
            'success' => 0,
            'message' => 'Could not delete. Something went wrong.'
        ]);
        exit;

    else :
        echo json_encode(['success' => 0, 'message' => 'Invalid ID. No posts found by the ID.']);
        exit;
    endif;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}

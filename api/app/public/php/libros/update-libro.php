<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


 $method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}


if ($_SERVER['REQUEST_METHOD'] !== 'PUT') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Bad Request detected! Only PUT method is allowed',
    ]);
    exit;
endif;

require '../classes/Database.php';
$database = new Database();
$conn = $database->dbConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(['success' => 0, 'message' => 'Please enter correct user id.']);
    exit;
}

try {

    $fetch_post = "SELECT * FROM libros WHERE id=:id";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :

        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
        $isbn = isset($data->isbn) ? $data->isbn : $row['isbn'];
        $autor = isset($data->autor) ? $data->autor : $row['autor'];
        $titulo = isset($data->titulo) ? $data->titulo : $row['titulo'];
        $descripcion = isset($data->descripcion) ? $data->descripcion : $row['descripcion'];

        $categoria = isset($data->categoria) ? $data->categoria : $row['categoria'];
        $imagen = isset($data->imagen) ? $data->imagen : $row['imagen'];
        //$telefono = isset($data->telefono) ? $data->telefono : $row['telefono'];

       $update_query = "UPDATE libros SET isbn = :isbn, autor = :autor, titulo = :titulo, descripcion = :descripcion,
       categoria = :categoria, imagen = :imagen
        WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':isbn', htmlspecialchars(strip_tags($isbn)), PDO::PARAM_STR);
        $update_stmt->bindValue(':autor', htmlspecialchars(strip_tags($autor)), PDO::PARAM_STR);
        $update_stmt->bindValue(':titulo', htmlspecialchars(strip_tags($titulo)), PDO::PARAM_STR);
        $update_stmt->bindValue(':descripcion', htmlspecialchars(strip_tags($descripcion)), PDO::PARAM_STR);
        $update_stmt->bindValue(':categoria', htmlspecialchars(strip_tags($categoria)), PDO::PARAM_STR);
        $update_stmt->bindValue(':imagen', htmlspecialchars(strip_tags($imagen)), PDO::PARAM_STR);

        $update_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);


        if ($update_stmt->execute()) {

            echo json_encode([
                'success' => 1,
                'message' => 'Record udated successfully'
            ]);
            exit;
        }

        echo json_encode([
            'success' => 0,
            'message' => 'Did not udpate. Something went  wrong.'
        ]);
        exit;

    else :
        echo json_encode(['success' => 0, 'message' => 'Invalid ID. No record found by the ID.']);
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

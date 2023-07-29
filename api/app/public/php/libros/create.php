<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "OPTIONS") {
    die();
}

 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') :
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Bad Request!.Only POST method is allowed',
    ]);
    exit;
endif;

require '../classes/Database.php';
$database = new Database();
$conn = $database->dbConnection();
 
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->autor) || !isset($data->titulo)) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fields |  autor, título',
    ]);
    exit;
 
elseif (empty(trim($data->autor)) || empty(trim($data->titulo)) ) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
 
endif;
 
try {
    $isbn = htmlspecialchars(trim($data->isbn));
    $autor = htmlspecialchars(trim($data->autor));
    $titulo = htmlspecialchars(trim($data->titulo));
    $categoria = htmlspecialchars(trim($data->categoria));
    $descripcion = htmlspecialchars(trim($data->descripcion));
    $imagen = htmlspecialchars(trim($data->imagen));

    $query = "INSERT INTO `libros`(
    isbn, autor, titulo, categoria, descripcion, imagen ) 
    VALUES(:isbn, :autor, :titulo, :categoria, :descripcion, :imagen )";
 
    $stmt = $conn->prepare($query);
 
    $stmt->bindValue(':isbn', $isbn, PDO::PARAM_STR);
    $stmt->bindValue(':autor', $autor, PDO::PARAM_STR);
    $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
    $stmt->bindValue(':categoria', $categoria, PDO::PARAM_STR);
    $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
    $stmt->bindValue(':imagen', $imagen, PDO::PARAM_STR);

    

    if ($stmt->execute()) {

        $last_id = $conn->lastInsertId();
        http_response_code(201);
        echo json_encode([
            'success' => 1,
            'message' => 'Data created Successfully.',
            'id' =>$last_id
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => 0,
        'message' => 'There is some problem in data inserting'
    ]);
    exit;
 
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}

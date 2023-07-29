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


//print_r($data);

//$hobbies = $data->hobbyField;
//print_r($hobbies);
//$hobbies_list = '';
//foreach ($hobbies as $hobby) {
//    $hobbies_list .= $hobby.',';
// } 

if (!isset($data->iduser) || !isset($data->idlibro) || !isset($data->tiempo)) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fields |  libro y usuario',
    ]);
    exit;
 
elseif (empty(trim($data->iduser)) || empty(trim($data->idlibro)) || empty(trim($data->tiempo))) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
 
endif;
 
try {

    $iduser = htmlspecialchars(trim($data->iduser));
    $idlibro = htmlspecialchars(trim($data->idlibro));
    $tiempo = htmlspecialchars(trim($data->tiempo));
    // insert into `reservas` (id_usuario,id_libro,tiempo) values(16,3,7);
    $query = "INSERT INTO `reservas`( id_usuario, tiempo , id_libro) VALUES( :iduser, :tiempo, :idlibro)";
    $stmt = $conn->prepare($query);
 
    $stmt->bindValue(':iduser', $iduser, PDO::PARAM_INT);
    $stmt->bindValue(':tiempo', $tiempo, PDO::PARAM_INT);
    $stmt->bindValue(':idlibro', $idlibro, PDO::PARAM_INT);





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

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

if (!isset($data->name) || !isset($data->apellidos) || !isset($data->email)) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Please enter compulsory fields |  First Name, Last Name and Email',
    ]);
    exit;
 
elseif (empty(trim($data->name)) || empty(trim($data->apellidos)) || empty(trim($data->email))) :
 
    echo json_encode([
        'success' => 0,
        'message' => 'Field cannot be empty. Please fill all the fields.',
    ]);
    exit;
 
endif;
 
try {
 
    $name = htmlspecialchars(trim($data->name));
    $apellidos = htmlspecialchars(trim($data->apellidos));
    $email = htmlspecialchars(trim($data->email));
    $password = htmlspecialchars(trim($data->password));
    $biblioteca = htmlspecialchars(trim($data->biblioteca));
    $telefono = htmlspecialchars(trim($data->telefono));
    $faltas = htmlspecialchars(trim($data->faltas));
 
    $query = "INSERT INTO `users`(
    name, apellidos, email, password, biblioteca, faltas, telefono ) 
    VALUES(:name, :apellidos, :email, :password, :biblioteca, :faltas, :telefono )";
 
    $stmt = $conn->prepare($query);
 
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':apellidos', $apellidos, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt->bindValue(':biblioteca', $biblioteca, PDO::PARAM_INT);
    $stmt->bindValue(':faltas', $faltas, PDO::PARAM_INT);
    $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
    

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

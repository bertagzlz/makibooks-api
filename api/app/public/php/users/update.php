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

//print_r($data);

//die();

//$hobbies = $data->hobbyField;
//print_r($hobbies);
//$hobbies_list = '';
//foreach ($hobbies as $hobby) {
//    $hobbies_list .= $hobby.',';
// }

if (!isset($data->id)) {
    echo json_encode(['success' => 0, 'message' => 'Please enter correct user id.']);
    exit;
}

try {

    $fetch_post = "SELECT * FROM `users` WHERE id=:id";
    $fetch_stmt = $conn->prepare($fetch_post);
    $fetch_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);
    $fetch_stmt->execute();

    if ($fetch_stmt->rowCount() > 0) :
     //echo 'AAA';
        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
        $name = isset($data->name) ? $data->name : $row['name'];
        $apellidos = isset($data->apellidos) ? $data->apellidos : $row['apellidos'];
        $email = isset($data->email) ? $data->email : $row['email'];
        $password = isset($data->password) ? $data->password : $row['password'];

        $biblioteca = isset($data->biblioteca) ? $data->biblioteca : $row['biblioteca'];
        $faltas = isset($data->faltas) ? $data->faltas : $row['faltas'];
        //$telefono = isset($data->telefono) ? $data->telefono : $row['telefono'];

       $update_query = "UPDATE `users` SET name = :name, apellidos = :apellidos, email = :email, password = :password,
       biblioteca = :biblioteca, faltas = :faltas
        WHERE id = :id";

        $update_stmt = $conn->prepare($update_query);

        $update_stmt->bindValue(':name', htmlspecialchars(strip_tags($name)), PDO::PARAM_STR);
        $update_stmt->bindValue(':apellidos', htmlspecialchars(strip_tags($apellidos)), PDO::PARAM_STR);
        $update_stmt->bindValue(':email', htmlspecialchars(strip_tags($email)), PDO::PARAM_STR);
        $update_stmt->bindValue(':password', htmlspecialchars(strip_tags($password)), PDO::PARAM_STR);
        $update_stmt->bindValue(':biblioteca', htmlspecialchars(strip_tags($biblioteca)), PDO::PARAM_INT);
        $update_stmt->bindValue(':faltas', htmlspecialchars(strip_tags($faltas)), PDO::PARAM_INT);
        //$update_stmt->bindValue(':telefono', htmlspecialchars(strip_tags($telefono)), PDO::PARAM_STR);


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

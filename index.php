<?php

cors();

http_response_code(200);

require_once 'src/controllers/AuthController.php';
require_once 'src/controllers/ProfileController.php';

use Src\controllers\AuthController;
use Src\controllers\ProfileController;

$requestURL = $_SERVER['REQUEST_URI'];
$header = apache_request_headers();

// auth user
if (isset($header['Authorization']) && (new AuthController())->checkToken($header['Authorization'])) {

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            switch ($requestURL) {
                case '/api/profile/edit' :
                    $res = (new ProfileController())->edit($header['Authorization']);
                    echo json_encode($res);
                    break;
                default:
                    http_response_code(404);
                    break;
            }
            break;
        case 'POST':
            $formData = json_decode(file_get_contents("php://input"), true);
            switch ($requestURL) {
                case '/api/login' :
                    $res = (new AuthController())->login($formData);
                    echo json_encode($res);
                    break;
                case '/api/logout':
                    $res = (new AuthController())->logout( $header['Authorization']);
                    echo json_encode($res);
                    break;
                case '/api/profile/update' :
                    $res = (new ProfileController())->update($header['Authorization'], $formData);
                    echo json_encode($res);
                    break;

                default:
                    http_response_code(404);
                    break;
            }
    }
}
elseif (isset($header['Authorization']) && !(new AuthController())->checkToken($header['Authorization'])){
    http_response_code(401);
    echo "veuillez vous connecter";
}
// guest user
elseif ($_SERVER['REQUEST_METHOD'] === "POST") {
    $formData = json_decode(file_get_contents("php://input"), true);
    switch ($requestURL) {
        case '/api/login' :
            $res = (new AuthController())->login($formData);
            http_response_code($res['status']);
            echo json_encode($res['message']);
            break;
        case '/api/profile/store' :
            $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            $txt = $formData['name'];
            fwrite($myfile, $txt);
            $res = (new ProfileController())->store($formData);
            http_response_code($res['status']);
            echo json_encode($res['message']);
            break;
        default:
            http_response_code(404);
            break;
    }
}


function cors()
{

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: http://172.20.10.4:8080");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With, Application, UserId');

        exit(0);
    }

}
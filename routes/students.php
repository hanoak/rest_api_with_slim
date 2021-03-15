<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);
$app->setBasePath("/rest_api_with_slim");

$app->get('/students/get', function (Request $request, Response $response, $args) {
    
    $query = "SELECT * FROM students";

    try {

        $db = new Database();
        $db = $db->connect();

        $stmt = $db->query($query);
        $students = $stmt->fetchAll(PDO::FETCH_OBJ);

        if(! empty($students)) {

            $response->getBody()->write(json_encode($students));
            return $response->withHeader('Content-Type', 'application.json')->withStatus(200);

        } else {
            $response->getBody()->write(json_encode(array('message' => "No records found!")));
            return $response->withHeader('Content-Type', 'application.json')->withStatus(200);
        }

    } catch(PDOException $exp) {

        $response->getBody()->write(json_encode(array('Error' => $exp->getMessage())));
        return $response->withHeader('Content-Type', 'application.json')->withStatus(500);
    }
    
});
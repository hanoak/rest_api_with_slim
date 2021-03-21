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


$app->get('/students/get/{id}', function (Request $request, Response $response, $args) {
    
    $id = $args['id'];
    $query = "SELECT * FROM students WHERE id = $id";

    try {

        $db = new Database();
        $db = $db->connect();

        $stmt = $db->query($query);
        $student = $stmt->fetch(PDO::FETCH_OBJ);

        if(! empty($student)) {

            $response->getBody()->write(json_encode($student));
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

$app->post('/students/post', function (Request $request, Response $response, $args) {
    
    $data = json_decode($request->getBody(), true);
    $name = $data['name'];
    $age = $data['age'];
    $address = $data['address'];
    
    $query = "INSERT INTO students SET name = :name, address = :address, age = :age";

    try {

        $db = new Database();
        $db = $db->connect();

        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':age', $age);

        $result = $stmt->execute();

        $response->getBody()->write(json_encode(array('message'=> 'Student added')));
        return $response->withHeader('Content-Type', 'application.json')->withStatus(200);

    } catch(PDOException $exp) {

        $response->getBody()->write(json_encode(array('Error' => $exp->getMessage())));
        return $response->withHeader('Content-Type', 'application.json')->withStatus(500);
    }

});


$app->delete('/students/delete/{id}', function (Request $request, Response $response, $args) {
    
    $id = $args['id'];
    $query = "DELETE FROM students WHERE id= $id";

    try {

        $db = new Database();
        $db = $db->connect();

        $stmt = $db->prepare($query);
        $result = $stmt->execute();

        $response->getBody()->write(json_encode(array('message'=> 'Student deleted')));
        return $response->withHeader('Content-Type', 'application.json')->withStatus(200);

    } catch(PDOException $exp) {

        $response->getBody()->write(json_encode(array('Error' => $exp->getMessage())));
        return $response->withHeader('Content-Type', 'application.json')->withStatus(500);
    }

});


$app->put('/students/put/{id}', function (Request $request, Response $response, $args) {
    
    $data = json_decode($request->getBody(), true);
    $name = $data['name'];
    $age = $data['age'];
    $address = $data['address'];
    $id = $args['id'];
    
    $query = "UPDATE students SET name = :name, address = :address, age = :age WHERE id = :id";

    try {

        $db = new Database();
        $db = $db->connect();

        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':id', $id);

        $result = $stmt->execute();

        $response->getBody()->write(json_encode(array('message'=> 'Student updated')));
        return $response->withHeader('Content-Type', 'application.json')->withStatus(200);

    } catch(PDOException $exp) {

        $response->getBody()->write(json_encode(array('Error' => $exp->getMessage())));
        return $response->withHeader('Content-Type', 'application.json')->withStatus(500);
    }

});

<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;      
    });


    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });


    $app->post('/customers/register', function (Request $request, Response $response) {
        try{
        $input=$request->getParsedBody();
        $db = $this->get(PDO::class);
        $sql="INSERT INTO customers(mob,name,area,address) VALUES (:mob, :name, :area, :address)";
        $sql= $db->prepare($sql);
        $sql->bindParam(":mob",$input['mob']);
        $sql->bindParam(":name",$input['name']);
        $sql->bindParam(":area",$input['area']);
        $sql->bindParam(":address",$input['address']);
        $sql->execute();
        $data=array(
    
            "status"=>true
          );
        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
        
        }
        catch(PDOException $e){

            $data = array(
                "message" => $e->getMessage(),
                "status"=>false
              );
           
              $response->getBody()->write(json_encode($data));
              return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
           
        }
    });

    $app->post('/customers', function (Request $request, Response $response) {
        // $input=$request->getParsedBody();
        $db = $this->get(PDO::class);
        $sql= $db->prepare("SELECT * FROM customers");
        $sql->execute();
        $data=$sql->fetchAll();
        // return $this->response->withJson($data);
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->post('/customers/login', function (Request $request, Response $response) {
        try{
            $input=$request->getParsedBody();
            $db = $this->get(PDO::class);
            $sql="SELECT * FROM customers where mob=:mob";
            $sql= $db->prepare($sql);
            $sql->bindParam(":mob",$input['mob']);
            $sql->execute();
            // $data=$sql->fetchAll();
            $data=array(
                "status"=>true,
                "data"=>$sql->fetchAll()
              );
            $count=$sql->rowCount();
            if($count==0){
                $data=array(
                    "status"=>false
                  );
            }
            $payload = json_encode($data);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
            
            }
            catch(PDOException $e){
    
                $data = array(
                    "message" => $e->getMessage(),
                    "status"=>"error"
                  );
               
                  $response->getBody()->write(json_encode($data));
                  return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(500);
               
            }
    });


};



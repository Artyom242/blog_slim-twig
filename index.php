<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
require __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Slim\Middleware\MethodOverrideMiddleware;


$_SESSION['login'] = 'login';
$_SESSION['password'] = 123;


//$loader = new FilesystemLoader('templates');
//$view = new Environment($loader);
$builder = new ContainerBuilder();
$builder->addDefinitions('config/di.php');

$container = $builder->build();
AppFactory::setContainer($container);

$view = $container->get(Environment::class);

$app = AppFactory::create();


// Add MethodOverride middleware
$app->add(new MethodOverrideMiddleware());

//auth
$app->get('/admin',  [Blog\Slim\Controllers\AuthController::class, 'admin']);
$app->post('/login',  [Blog\Slim\Controllers\AuthController::class, 'login']);
$app->get('/admin/logout',  [Blog\Slim\Controllers\AuthController::class, 'logout']);

//crud
$app->map(['GET', 'POST'],'/admin/create',  [Blog\Slim\Controllers\PostController::class, 'create']);
$app->get('/admin/store',  [Blog\Slim\Controllers\PostController::class, 'store']);
$app->get('/admin/destroy/{url_key}',  [Blog\Slim\Controllers\PostController::class, 'destroy']);
$app->get('/admin/{url_key}/edit',  [Blog\Slim\Controllers\PostController::class, 'edit']);
$app->post('/admin/update/{url_key}',  [Blog\Slim\Controllers\PostController::class, 'update']);

//main
$app->get('/', [Blog\Slim\Controllers\IndexController::class, 'getHome']);
$app->get('/about', [Blog\Slim\Controllers\IndexController::class, 'getAbout']);
$app->get('/posts',  [Blog\Slim\Controllers\IndexController::class, 'getPosts']);
$app->get('/{url_key}',  [Blog\Slim\Controllers\IndexController::class, 'getPost']);


$app->addRoutingMiddleware();
$app->run();
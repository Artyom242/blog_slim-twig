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

//главная стр
$app->get('/', [Blog\Slim\Routs\Routs::class, 'getHome']);

//страница about
$app->get('/about', [Blog\Slim\Routs\Routs::class, 'getAbout']);

$app->get('/posts',  [Blog\Slim\Routs\Routs::class, 'getPosts']);

$app->get('/admin',  [Blog\Slim\Routs\Routs::class, 'admin']);

$app->post('/login',  [Blog\Slim\Routs\Routs::class, 'login']);

$app->map(['GET', 'POST'],'/admin/create',  [Blog\Slim\Routs\Routs::class, 'create']);

$app->get('/admin/story',  [Blog\Slim\Routs\Routs::class, 'story']);

$app->get('/admin/destroy/{url_key}',  [Blog\Slim\Routs\Routs::class, 'destroy']);

$app->get('/admin/logout',  [Blog\Slim\Routs\Routs::class, 'logout']);

$app->get('/admin/{url_key}/edit',  [Blog\Slim\Routs\Routs::class, 'edit']);

$app->post('/admin/update/{url_key}',  [Blog\Slim\Routs\Routs::class, 'update']);

//страница поста
$app->get('/{url_key}',  [Blog\Slim\Routs\Routs::class, 'getPost']);

$app->addRoutingMiddleware();
$app->run();
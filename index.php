<?php
session_start();

require __DIR__ . '/vendor/autoload.php';

use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

use Slim\App\Controller;
use DI\Container;


//$loader = new FilesystemLoader('templates');
//$view = new Environment($loader);
$builder = new ContainerBuilder();
$builder->addDefinitions('config/di.php');

$container = $builder->build();
AppFactory::setContainer($container);

$view = $container->get(Environment::class);

$app = AppFactory::create();

//главная стр
$app->get('/', [Controller\Rout::class, 'getHome']);

//страница about
$app->get('/about', [Controller\Rout::class, 'getAbout']);

//страница поста
$app->get('/{url_key}', function (Request $request, Response $response, $args) use ($view) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

    $post = ORM::for_table('posts')->where('id', $args['url_key'])->find_one();
    $body = $view->render('post.twig', ['post' => $post]);
    $response->getBody()->write($body);
    return $response;
});

$app->run();
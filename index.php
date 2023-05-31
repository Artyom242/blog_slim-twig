<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

require __DIR__ . '/vendor/autoload.php';


$loader = new FilesystemLoader('templates');
$view = new Environment($loader);

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('index.twig');
    $response->getBody()->write($body);
    return $response;
});

$app->get('/about', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('about.twig', [ 'name' => 'Art' ] );
    $response->getBody()->write($body);
    return $response;
});

$app->get('/{url_key}', function (Request $request, Response $response, $args) use ($view) {
    /** @var PDO $db */
    $db = require_once $_SERVER['DOCUMENT_ROOT'] . '/db.php';

    $posts = $db->prepare('select * from posts where id=:id');
    $posts->execute(['id' => $args['url_key']]);
    $post = $posts->fetch(PDO::FETCH_ASSOC);


    $body = $view->render('post.twig', [ 'post' => $post ]);
    $response->getBody()->write($body);
    return $response;
});

$app->run();
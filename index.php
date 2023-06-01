<?php
session_start();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

require __DIR__ . '/vendor/autoload.php';

$loader = new FilesystemLoader('templates');
$view = new Environment($loader);

$app = AppFactory::create();

//главная стр
$app->get('/', function (Request $request, Response $response, $args) use ($view) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
    $postsOne = ORM::for_table('posts')->find_many();
    $postsTwo = ORM::for_table('posts')->where_gt('id', 4)->find_many();

    $body = $view->render('index.twig', ['posts' => $postsOne, 'postsTwo' => $postsTwo]);
    $response->getBody()->write($body);
    return $response;
});

//страница about
$app->get('/about', function (Request $request, Response $response, $args) use ($view) {
    $body = $view->render('about.twig', [ 'name' => 'Art' ] );
    $response->getBody()->write($body);
    return $response;
});

//страница поста
$app->get('/{url_key}', function (Request $request, Response $response, $args) use ($view) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
/*
    $posts = $db->prepare('select * from posts where id=:id');
    $posts->execute(['id' => $args['url_key']]);
    $post = $posts->fetch(PDO::FETCH_ASSOC);*/

    $post = ORM::for_table('posts')->where('id', $args['url_key'])->find_one();

    $body = $view->render('post.twig', [ 'post' => $post ]);
    $response->getBody()->write($body);
    return $response;
});

$app->run();
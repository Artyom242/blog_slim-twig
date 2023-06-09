<?php

namespace Slim\App\Controller;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use ORM;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class Rout
{
    private Environment $view;

    public function __construct(Environment $view)
    {
        $this->view = $view;
    }

    public function getAbout(Request $request, Response $response, $args): Response
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

        $body = $this->view->render('about.twig', ['name' => 'Art']);
        $response->getBody()->write($body);
        return $response;
    }

    public function getHome(Request $request, Response $response, $args): Response
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
        $postsOne = ORM::for_table('posts')->find_many();
        $postsTwo = ORM::for_table('posts')->where_gt('id', 4)->find_many();

        $body = $this->view->render('index.twig', ['posts' => $postsOne, 'postsTwo' => $postsTwo]);
        $response->getBody()->write($body);
        return $response;
    }
}
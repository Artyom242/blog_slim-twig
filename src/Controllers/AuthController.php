<?php

namespace Blog\Slim\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;


class AuthController
{
    private Environment $view;

    public function __construct(Environment $view)
    {
        $this->view = $view;
    }


    public function admin(Request $request, Response $response, $args)
    {
        if ($_SESSION['admin'] == 'login') {
            header('location: /admin/store');
        } else {
            $body = $this->view->render('admin/index.twig');
            $response->getBody()->write($body);
            return $response;
        }
    }

    public function login(Request $request, Response $response, $args)
    {
        $login = $request->getParsedBody();

        if (!empty($login['login']) && !empty($login['password'])) {
            if ($login['login'] == $_SESSION['login'] && $login['password'] == $_SESSION['password']) {

                $_SESSION['admin'] = 'login';
                $body = $this->view->render('admin/addPost.twig');
                $response->getBody()->write($body);
                return $response;
            }
        }

        $body = $this->view->render('admin/index.twig');
        $response->getBody()->write($body);
        return $response;
    }


    public function logout(Request $request, Response $response, $args)
    {

        if ($_SESSION['admin'] == 'login') {

            $_SESSION['admin'] = null;
            header('location: /');
        }

        header('location: /');
    }
}
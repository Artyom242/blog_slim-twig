<?php

namespace Blog\Slim\Routs;

use ORM;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;


class Routs
{
    private Environment $view;

    public function __construct(Environment $view)
    {
        $this->view = $view;
    }

    public function getAbout(Request $request, Response $response, $args): Response
    {
        $body = $this->view->render('about.twig', ['name' => 'Art']);
        $response->getBody()->write($body);
        return $response;
    }

    public function getHome(Request $request, Response $response, $args): Response
    {
        $postsOne = ORM::for_table('posts')->limit(4)->find_many();
        $postsTwo = ORM::for_table('posts')->offset(4)->limit(2)->find_many();

        $body = $this->view->render('index.twig', ['postFirst' => $postsOne, 'postsSecond' => $postsTwo]);
        $response->getBody()->write($body);
        return $response;
    }

    public function getPost(Request $request, Response $response, $args): Response
    {
        $post = ORM::for_table('posts')->where('id', $args['url_key'])->find_one();
        $body = $this->view->render('post.twig', ['post' => $post]);
        $response->getBody()->write($body);
        return $response;
    }

    public function getPosts(Request $request, Response $response, $args): Response
    {
        $posts = ORM::for_table('posts')->find_many();


        $body = $this->view->render('posts.twig', ['posts' => $posts]);
        $response->getBody()->write($body);
        return $response;
    }

    public function admin(Request $request, Response $response, $args)
    {
        if ($_SESSION['admin'] == 'login') {
            header('location: /admin/story');
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

    public function create(Request $request, Response $response, $args): Response
    {

        if ($_SESSION['admin'] == 'login') {
            $contents = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();

            if (!empty($contents['title']) && !empty($contents['text']) && !empty($uploadedFiles['image'])) {

                $base = ORM::for_table('posts')->create();

                ///заносим title и text в объект
                $base->title = $contents['title'];
                $base->text = $contents['text'];
                $uploadedFile = $uploadedFiles['image'];

                //действия с файлом
                $filename = $uploadedFile->getClientFilename();
                $directory = $_SERVER['DOCUMENT_ROOT'] . '/img/cards' . '/' . $filename;
                $tmpFilePath = $uploadedFile->getStream()->getMetadata('uri');
                move_uploaded_file($tmpFilePath, $directory);

                //сохранение в базу
                $base->image = $filename;
                $base->save();

                $body = $this->view->render('admin/addPost.twig');
                $response->getBody()->write($body);
                return $response;
            }
            $body = $this->view->render('admin/addPost.twig');
            $response->getBody()->write($body);
            return $response;
        }

        $body = $this->view->render('admin/index.twig');
        $response->getBody()->write($body);
        return $response;
    }

    public function story(Request $request, Response $response, $args): Response
    {
        $posts = ORM::for_table('posts')->find_many();

        $body = $this->view->render('admin/story.twig', ['posts' => $posts]);
        $response->getBody()->write($body);
        return $response;
    }

    public function edit(Request $request, Response $response, $args): Response
    {
        $post = ORM::for_table('posts')->where('id', $args['url_key'])->find_one();

        $body = $this->view->render('admin/edit.twig', ['post' => $post]);
        $response->getBody()->write($body);
        return $response;
    }

    public function update(Request $request, Response $response, $args)
    {

        if ($_SESSION['admin'] == 'login') {
            $contents = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();


            $post = ORM::for_table('posts')->where('id', $args['url_key'])->find_one();;

            foreach ($contents as $key => $value) {
                if ($value != $post->$key) {
                    $post->$key = $value;
                }
            }
/*
            $uploadedFile = $uploadedFiles['image'];

            //действия с файлом

            $filename = $uploadedFile->getClientFilename();
            $directory = $_SERVER['DOCUMENT_ROOT'] . '/img/cards' . '/' . $filename;
            $tmpFilePath = $uploadedFile->getStream()->getMetadata('uri');
            move_uploaded_file($tmpFilePath, $directory);
*/
            $post->save();

            header('location: /admin/story');
        }

        header('location: /admin');
    }

    public function destroy(Request $request, Response $response, $args)
    {
        $post = ORM::for_table('posts')->where('id', $args['url_key'])->find_one();
        $post->delete();
        header('location: /admin/story');
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
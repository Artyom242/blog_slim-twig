<?php

namespace Blog\Slim\Controllers;

use ORM;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;


class PostController
{
    private Environment $view;

    public function __construct(Environment $view)
    {
        $this->view = $view;
    }


    public function create(Request $request, Response $response, $args)
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

                header('location: /admin/store');
            } else {
                $body = $this->view->render('admin/addPost.twig');
                $response->getBody()->write($body);
                return $response;
            }
        } else {
            $body = $this->view->render('admin/index.twig');
            $response->getBody()->write($body);
            return $response;
        }
    }

    public function store(Request $request, Response $response, $args): Response
    {
        $posts = ORM::for_table('posts')->find_many();

        $body = $this->view->render('admin/store.twig', ['posts' => $posts]);
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

            $uploadedFile = $uploadedFiles['image'];
            $filename = $uploadedFile->getClientFilename();
            var_dump($filename);

            if ($filename) {
                $directory = $_SERVER['DOCUMENT_ROOT'] . '/img/cards' . '/' . $filename;
                $tmpFilePath = $uploadedFile->getStream()->getMetadata('uri');
                move_uploaded_file($tmpFilePath, $directory);

                $post->image = $filename;
            }

            $post->save();

            header('location: /admin/store');
        }

        header('location: /admin');
    }

    public function destroy(Request $request, Response $response, $args)
    {
        $post = ORM::for_table('posts')->where('id', $args['url_key'])->find_one();
        $post->delete();
        header('location: /admin/store');
    }


}
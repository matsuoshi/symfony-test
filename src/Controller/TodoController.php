<?php

namespace App\Controller;

use App\Form\Type\TodoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends Controller
{
    /**
     * @Route("/todo", name="todo")
     */
    public function index()
    {
        return $this->render('todo.html.twig', [
            'message' => 'hello',
            'nums' => [1, 2, 3],
            'flg' => true,
        ]);
    }

    /**
     * @Route("/todo/create", name="todo_create")
     * @Template("create.html.twig")
     */
    public function create(Request $request)
    {
        dump($request);

        $form = $this->createForm(TodoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // do something
        }

        return [
            'form' => $form->createView()
        ];
    }
}
